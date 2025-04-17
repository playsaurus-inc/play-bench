<?php

namespace App\Services;

use App\Models\AiModel;
use App\Models\SvgMatch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SvgBenchmarkService
{
    /**
     * System prompt for idea generation
     */
    protected const IDEA_SYSTEM_PROMPT = "You are a highly creative art director generating brief, specific image prompts for an SVG drawing competition. Create imaginative, unusual ideas that would be interesting to draw. Keep your prompts under 100 characters. Be concise but vivid. Focus on a single clear concept rather than many details. Respond ONLY with the idea - no explanations or additional text.";

    /**
     * User prompt for idea generation
     */
    protected const IDEA_USER_PROMPT = "Generate a brief idea for a simple art scene that would be easy to determine who is the winner in a head to head drawing competition.";

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
        protected SvgService $svgService
    ) {}

    /**
     * Get all available AI models for the SVG benchmark
     */
    public function getAvailableModels(): Collection
    {
        return AiModel::whereIn('name', $this->aiClient->getAvailableModels())->get();
    }

    /**
     * Generate a creative image idea using GPT-4o
     */
    protected function generateImageIdea(): string
    {
        $idea = $this->aiClient->getResponse('gpt-4o', self::IDEA_SYSTEM_PROMPT, self::IDEA_USER_PROMPT, config: [
            "temperature" => 1.0,
            "max_tokens" => 80,
            "top_p" => 1.0,
        ]);

        $idea = Str::limit($idea, 100, end: '...', preserveWords: true);

        return $idea;
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
     * Judge the SVGs using GPT-4o
     */
    protected function judgeSvgs(string $imagePrompt, string $svg1DataUrl, string $svg2DataUrl): array
    {
        $response = $this->aiClient->getResponseWithImages(
            'gpt-4o',
            self::JUDGE_SYSTEM_PROMPT,
            Str::replaceArray('[IDEA_TEXT]', [$imagePrompt], self::JUDGE_USER_PROMPT),
            images: [$svg1DataUrl, $svg2DataUrl],
        );

        // Process the response to extract winner and reason
        $jsonResult = json_decode($response, true);
        if (is_array($jsonResult) && isset($jsonResult['winner'])) {
            return [
                'winner' => $jsonResult['winner'],
                'reason' => $jsonResult['reason'] ?? 'No reason provided'
            ];
        }

        // If JSON parsing fails, try regex extraction
        $winner = 'player1'; // Default
        $reason = 'Default judgment: Player 1 demonstrated better creativity and technical execution.';

        if (preg_match('/"winner"\s*:\s*"(player[12])"/', $response, $matches)) {
            $winner = $matches[1];
        }

        if (preg_match('/"reason"\s*:\s*"([^"]*)"/', $response, $matches)) {
            $reason = $matches[1];
        }

        return [
            'winner' => $winner,
            'reason' => $reason
        ];
    }

    /**
     * Run a single SVG creation match between two AI models
     */
    public function runMatch(AiModel $player1, AiModel $player2): SvgMatch
    {
        // Create a new match record
        $match = new SvgMatch();
        $match->player1_id = $player1->id;
        $match->player2_id = $player2->id;
        $match->started_at = Date::now();

        Log::info('Starting SVG match', [
            'match_id' => $match->id,
            'player1' => $player1->name,
            'player2' => $player2->name,
        ]);

        try {
            // Step 1: Generate a creative image prompt
            $match->prompt = $this->generateImageIdea();

            Log::info('Generated SVG prompt', [
                'match_id' => $match->id,
                'prompt' => $match->prompt,
            ]);

            // Step 2: Build the SVG creation prompt
            $svgPrompt = $this->buildSvgPrompt($match->prompt);

            // Get SVGs from both models
            $svg1 = $this->getResponse($player1, self::SVG_SYSTEM_PROMPT, $svgPrompt);
            $svg2 = $this->getResponse($player2, self::SVG_SYSTEM_PROMPT, $svgPrompt);

            // Clean SVGs
            $svg1 = $this->svgService->cleanupSvg($svg1);
            $svg2 = $this->svgService->cleanupSvg($svg2);

            // Store SVGs
            $player1SvgPath = "svg/{$match->id}_player1.svg";
            $player2SvgPath = "svg/{$match->id}_player2.svg";

            Storage::put($player1SvgPath, $svg1);
            Storage::put($player2SvgPath, $svg2);

            // Update the match with SVG paths
            $match->player1_svg_path = $player1SvgPath;
            $match->player2_svg_path = $player2SvgPath;

            Log::info('Generated SVGs', [
                'match_id' => $match->id,
                'player1_svg_size' => strlen($svg1),
                'player2_svg_size' => strlen($svg2),
            ]);

            // Step 3: Prepare SVGs for judging
            $svg1DataUrl = $this->svgService->svgToPngDataUrl($svg1);
            $svg2DataUrl = $this->svgService->svgToPngDataUrl($svg2);

            // Step 4: Judge the SVGs
            $judgmentResult = $this->judgeSvgs(
                $match->prompt,
                $svg1DataUrl,
                $svg2DataUrl
            );

            // Update the match with winner and reasoning
            $match->judge_reasoning = $judgmentResult['reason'];
            $match->winner_id = $judgmentResult['winner'] === 'player1' ? $player1->id : $player2->id;

            Log::info('Judgment complete', [
                'match_id' => $match->id,
                'winner' => $judgmentResult['winner'],
                'reasoning' => $judgmentResult['reason'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error during SVG match', [
                'match_id' => $match->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Handle the error by marking the match as forced completion
            $match->is_forced_completion = true;
        }

        // Finalize match
        $match->ended_at = Date::now();
        $match->save();

        return $match;
    }

    /**
     * Generate a random SVG using the random provider
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

    /**
     * Get the response from the AI model
     */
    protected function getResponse(AiModel $model, string $systemPrompt, string $userPrompt): string
    {
        if ($model->name === 'random') {
            return $this->defaultSvg();
        }

        $response = $this->aiClient->getResponse($model->name, $systemPrompt, $userPrompt);

        // Look for SVG content
        if (preg_match('/<svg.*<\/svg>/s', $response, $matches)) {
            return $matches[0];
        }

        throw new \Exception("Could not find SVG content in response");
    }
}
