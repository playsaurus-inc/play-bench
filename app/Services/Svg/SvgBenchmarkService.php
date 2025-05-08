<?php

namespace App\Services\Svg;

use App\Models\AiModel;
use App\Services\AiClient\AiClientService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class SvgBenchmarkService
{
    /**
     * System prompt for idea generation
     */
    protected const IDEA_SYSTEM_PROMPT = 'You are a highly creative art director generating brief, specific image prompts for an SVG drawing competition. Create imaginative, unusual ideas that would be interesting to draw. Keep your prompts under 100 characters. Be concise but vivid. Focus on a single clear concept rather than many details. Respond ONLY with the idea - no explanations or additional text.';

    /**
     * User prompt for idea generation
     */
    protected const IDEA_USER_PROMPT = 'Generate a brief idea for a simple art scene that would be easy to determine who is the winner in a head to head drawing competition.';

    /**
     * System prompt for SVG creation
     */
    protected const SVG_SYSTEM_PROMPT = "You are an expert SVG artist with a flair for creativity and innovation. Create a detailed and imaginative SVG drawing of the provided idea. Use a viewBox='0 0 300 300' for consistent sizing. Utilize color, gradients, patterns, and interesting compositions. Focus on both technical excellence and artistic expression. Return ONLY valid SVG code - no explanations, just the <svg> element with its contents.";

    /**
     * System prompt for judging
     */
    protected const JUDGE_SYSTEM_PROMPT = "You are a sophisticated art critic with expertise in visual design, creativity, and technical execution. Given two SVG images, evaluate them based on creativity, adherence to the prompt, technical quality, compositional strength, and visual appeal. Be thoughtful and specific in your analysis. Respond ONLY with valid JSON containing a key 'winner' with your decision (either 'player1' or 'player2') and a 'reason' key explaining your choice in 2-3 sentences.";

    /**
     * User prompt for judging
     */
    protected const JUDGE_USER_PROMPT = "Judge these two images based on the idea: [IDEA_TEXT]. Image 1 is Player 1's submission and Image 2 is Player 2's submission. Evaluate them based on creativity, adherence to the prompt, technical quality, and visual appeal. Respond with a JSON object with 'winner' (either 'player1' or 'player2') and 'reason' explaining your choice in 2-3 sentences.";

    /**
     * Create a new service instance.
     */
    public function __construct(
        protected AiClientService $aiClient,
        protected SvgImageService $svgService
    ) {}

    /**
     * Get all available AI models for the SVG benchmark
     *
     * @return Collection<AiModel>
     */
    public function getAvailableModels(): Collection
    {
        return AiModel::whereIn('slug', $this->aiClient->getAvailableModels('svg'))->get();
    }

    /**
     * Run an SVG game
     *
     * @param  callable|null  $onPromptGenerated  Callback after prompt is generated
     * @param  callable|null  $onSvgSubmitted  Callback after each SVG is submitted
     * @param  callable|null  $onJudgingComplete  Callback after judging is complete
     */
    public function runGame(
        SvgGame $game,
        ?callable $onPromptGenerated = null,
        ?callable $onSvgSubmitted = null,
    ): void {
        $onPromptGenerated ??= fn () => null;
        $onSvgSubmitted ??= fn () => null;
        $onJudgingComplete ??= fn () => null;

        // Generate creative prompt
        $prompt = $this->generateImageIdea();
        $game->setPrompt($prompt);

        $onPromptGenerated($game);

        // Get SVG from Player 1
        $svg1 = $this->getPlayerSvg($game, SvgPlayer::Player1);
        $game->setSvg(SvgPlayer::Player1, $svg1);
        $png1Bytes = $this->svgService->svgToPng($svg1, width: 300, height: 300);

        $onSvgSubmitted($game, SvgPlayer::Player1);

        // Get SVG from Player 2
        $svg2 = $this->getPlayerSvg($game, SvgPlayer::Player2);
        $game->setSvg(SvgPlayer::Player2, $svg2);
        $png2Bytes = $this->svgService->svgToPng($svg2, width: 300, height: 300);

        $onSvgSubmitted($game, SvgPlayer::Player2);

        // Judge the SVGs
        $judgmentResult = $this->judgeSvgs($prompt, $png1Bytes, $png2Bytes);

        $winner = $judgmentResult['winner'] === 'player1' ? SvgPlayer::Player1 : SvgPlayer::Player2;
        $game->setJudgment($winner, $judgmentResult['reason']);
    }

    /**
     * Generate a creative image idea
     */
    protected function generateImageIdea(): string
    {
        $juryModel = config('playbench.svg_jury');

        $idea = $this->aiClient->getResponse($juryModel, self::IDEA_SYSTEM_PROMPT, self::IDEA_USER_PROMPT, config: [
            'temperature' => 1.0,
            'max_tokens' => 80,
            'top_p' => 1.0,
        ]);

        return Str::of($idea)
            ->remove('\"')
            ->limit(limit: 100, end: '...', preserveWords: true);
    }

    /**
     * Build SVG creation prompt
     */
    protected function buildSvgPrompt(string $imageIdea): string
    {
        return "Create an imaginative SVG representation of: \"{$imageIdea}\".

Use viewBox=\"0 0 300 300\" for your canvas.

Be creative and detailed in your approach. Consider using:
- Vibrant colors and gradients
- Interesting shapes and compositions
- Visual metaphors and artistic expression
- A distinctive style that captures the essence of the prompt

Push the boundaries of SVG capabilities while maintaining technical excellence.

Return ONLY valid SVG code in your response.";
    }

    /**
     * Get SVG from a player
     */
    protected function getPlayerSvg(SvgGame $game, SvgPlayer $player): string
    {
        $model = $game->getPlayer($player);

        if ($model->name === 'random') {
            return $this->defaultSvg();
        }

        $prompt = $this->buildSvgPrompt($game->getPrompt());

        $response = $this->aiClient->getResponse($model->slug, self::SVG_SYSTEM_PROMPT, $prompt);

        // Look for SVG content
        if (preg_match('/<svg.*<\/svg>/s', $response, $matches)) {
            return $this->svgService->cleanupSvg($matches[0]);
        }

        throw new \Exception('Could not find SVG content in response');
    }

    /**
     * Converts a file to a data URL string.
     */
    protected function toDataUrl(string $mime, string $contents): string
    {
        return "data:$mime;base64,".base64_encode($contents);
    }

    /**
     * Judge the SVGs using GPT-4o
     */
    protected function judgeSvgs(string $imagePrompt, string $png1Bytes, string $png2Bytes): array
    {
        $response = $this->aiClient->getResponseWithImages(
            model: config('playbench.svg_jury'),
            systemPrompt: self::JUDGE_SYSTEM_PROMPT,
            userPrompt: Str::replaceArray('[IDEA_TEXT]', [$imagePrompt], self::JUDGE_USER_PROMPT),
            images: [
                $this->toDataUrl('image/png', $png1Bytes),
                $this->toDataUrl('image/png', $png2Bytes),
            ],
        );

        // Process the response to extract winner and reason
        $jsonResult = json_decode($response, true);
        if (is_array($jsonResult) && isset($jsonResult['winner'])) {
            return [
                'winner' => $jsonResult['winner'],
                'reason' => $jsonResult['reason'] ?? 'No reason provided',
            ];
        }

        throw new \Exception('Invalid response from judge');
    }

    /**
     * Generate a random SVG
     */
    protected function defaultSvg(): string
    {
        // Return a simple SVG circle
        $color = dechex(random_int(0, 16777215)); // Random hex color
        $r = random_int(30, 100);
        $cx = random_int(100, 200);
        $cy = random_int(100, 200);

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300"><circle cx="'.$cx.'" cy="'.$cy.'" r="'.$r.'" fill="#'.$color.'"/></svg>';
    }
}
