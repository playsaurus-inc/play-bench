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
     * Create a new service instance.
     */
    public function __construct(
        protected AiClientService $aiClient
    ) {}

    /**
     * Get all available AI models for the chess benchmark
     */
    public function getAvailableModels(): Collection
    {
        return AiModel::whereIn('name', $this->aiClient->getAvailableModels())->get();
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
        $match->save();

        Log::info('Starting SVG match', [
            'match_id' => $match->id,
            'player1' => $player1->name,
            'player2' => $player2->name,
        ]);

        try {
            // Step 1: Generate a creative image prompt
            $promptSystemMessage = "You are an AI that generates creative, unique image ideas for a drawing competition. Respond ONLY with a creative idea. Aim for unusual, imaginative concepts that would be visually interesting and would make it easy to determine which of two drawings is better.";
            $promptUserMessage = "Generate a brief idea for a simple art scene that would be easy to determine who is the winner in a head to head drawing competition.";

            $imagePrompt = $this->aiClient->getResponse(
                $this->getRandomModel(),
                $promptSystemMessage,
                $promptUserMessage,
                'text'
            );

            // Ensure the prompt isn't too long
            if (strlen($imagePrompt) > 100) {
                $imagePrompt = substr($imagePrompt, 0, 100);
            }

            Log::info('Generated SVG prompt', [
                'match_id' => $match->id,
                'prompt' => $imagePrompt,
            ]);

            // Update the match with the prompt
            $match->prompt = $imagePrompt;
            $match->save();

            // Step 2: Get SVGs from both AIs
            $svgSystemMessage = "You are an expert SVG artist with a flair for creativity and innovation. Create a detailed and imaginative SVG drawing of the provided idea. Use a viewBox='0 0 300 300' for consistent sizing. Utilize color, gradients, patterns, and interesting compositions. Focus on both technical excellence and artistic expression. Return ONLY valid SVG code - no explanations, just the <svg> element with its contents.";

            $svgPrompt = "Create an imaginative SVG representation of: \"{$imagePrompt}\".

Use viewBox=\"0 0 300 300\" for your canvas.

Be creative and detailed in your approach. Consider using:
- Vibrant colors and gradients
- Interesting shapes and compositions
- Visual metaphors and artistic expression
- A distinctive style that captures the essence of the prompt

Push the boundaries of SVG capabilities while maintaining technical excellence.

Return ONLY valid SVG code in your response.";

            // Get SVGs concurrently (not really, but simulate it)
            $svg1 = $this->aiClient->getResponse($player1, $svgSystemMessage, $svgPrompt, 'svg');
            $svg2 = $this->aiClient->getResponse($player2, $svgSystemMessage, $svgPrompt, 'svg');

            // Clean SVGs
            $svg1 = $this->cleanupSvgContent($svg1);
            $svg2 = $this->cleanupSvgContent($svg2);

            // Store SVGs
            $player1SvgPath = 'svg/' . $match->id . '_player1.svg';
            $player2SvgPath = 'svg/' . $match->id . '_player2.svg';

            Storage::put($player1SvgPath, $svg1);
            Storage::put($player2SvgPath, $svg2);

            // Update the match with SVG paths
            $match->player1_svg_path = $player1SvgPath;
            $match->player2_svg_path = $player2SvgPath;
            $match->save();

            Log::info('Generated SVGs', [
                'match_id' => $match->id,
                'player1_svg_size' => strlen($svg1),
                'player2_svg_size' => strlen($svg2),
            ]);

            // Step 3: Judge the SVGs
            $judgeSystemMessage = "You are a sophisticated art critic with expertise in visual design, creativity, and technical execution. Given two SVG images described to you, evaluate them based on creativity, adherence to the prompt, technical quality, compositional strength, and visual appeal. Be thoughtful and specific in your analysis. Respond ONLY with valid JSON containing a key 'winner' with your decision (either 'player1' or 'player2') and a 'reason' key explaining your choice in 2-3 sentences.";

            $judgePrompt = "Judge these two SVG images based on the idea: \"{$imagePrompt}\".

PLAYER 1 SVG:
```
{$svg1}
```

PLAYER 2 SVG:
```
{$svg2}
```

Evaluate them based on creativity, adherence to the prompt, technical quality, and visual appeal. Respond with a JSON object with 'winner' (either 'player1' or 'player2') and 'reason' explaining your choice in 2-3 sentences.";

            $judgeResponse = $this->aiClient->getResponse(
                $this->getRandomModel(),
                $judgeSystemMessage,
                $judgePrompt,
                'judgment'
            );

            // Extract winner and reasoning
            $winner = 'player1'; // Default
            $reasoning = 'Default judgment: Player 1 demonstrated better creativity and technical execution.';

            if (preg_match('/"winner"\s*:\s*"(player[12])"/', $judgeResponse, $matches)) {
                $winner = $matches[1];
            }

            if (preg_match('/"reason"\s*:\s*"([^"]+)"/', $judgeResponse, $matches)) {
                $reasoning = $matches[1];
            } elseif (preg_match('/"reason"\s*:\s*"(.+)"/', $judgeResponse, $matches)) {
                $reasoning = $matches[1];
            }

            // Update the match with winner and reasoning
            $match->judge_reasoning = $reasoning;
            $match->winner_id = $winner === 'player1' ? $player1->id : $player2->id;

            Log::info('Judgment complete', [
                'match_id' => $match->id,
                'winner' => $winner,
                'reasoning' => $reasoning,
            ]);

        } catch (\Exception $e) {
            Log::error('Error during SVG match', [
                'match_id' => $match->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Finalize match
        $match->ended_at = Date::now();
        $match->save();

        return $match;
    }

    /**
     * Get a random AI model for tasks like prompt generation and judging
     */
    protected function getRandomModel(): AiModel
    {
        // Prefer to use OpenAI models for prompt generation and judging
        $preferredModels = AiModel::query()
            ->where('name', 'like', 'gpt-4o%')
            ->get();

        if ($preferredModels->isNotEmpty()) {
            return $preferredModels->random();
        }

        // Fall back to any model
        return AiModel::all()->random();
    }

    /**
     * Clean up SVG content
     */
    protected function cleanupSvgContent(string $svgString): string
    {
        // Remove markdown code block syntax
        $cleaned = preg_replace('/```svg\n?|```\n?/', '', $svgString);

        // Fix spaces in tag names (e.g., "< defs>" to "<defs>")
        $cleaned = preg_replace('/< ([a-zA-Z])/', '<$1', $cleaned);

        // Fix self-closing tags that might be malformed
        $cleaned = preg_replace('/([a-zA-Z])\/>//', '$1 />', $cleaned);

        // Ensure proper XML structure
        if (!Str::contains($cleaned, 'xmlns="http://www.w3.org/2000/svg"') && Str::contains($cleaned, '<svg')) {
            $cleaned = preg_replace('/<svg/', '<svg xmlns="http://www.w3.org/2000/svg"', $cleaned);
        }

        // Make sure viewBox is properly defined
        if (!Str::contains($cleaned, 'viewBox') && Str::contains($cleaned, '<svg')) {
            $cleaned = preg_replace('/<svg/', '<svg viewBox="0 0 300 300"', $cleaned);
        }

        return $cleaned;
    }
}
