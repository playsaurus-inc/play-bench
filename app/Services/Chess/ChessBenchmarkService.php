<?php

namespace App\Services\Chess;

use App\Models\AiModel;
use App\Services\AiClient\AiClientService;
use Exception;
use Illuminate\Database\Eloquent\Collection;

use function PHPUnit\Framework\callback;

class ChessBenchmarkService
{
    /**
     * The number of times to retry a failed move.
     */
    protected int $retryCount = 3;

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
     * Run a chess game
     *
     * @param  callable|null  $onMoveMade  Callback after each move
     * @param  callable|null  $onIllegalMove  Callback after illegal move
     */
    public function runGame(
        ChessGame $game,
        ?callable $onMoveMade = null,
        ?callable $onIllegalMove = null,
    ): void {
        // Set default callbacks
        $onMoveMade ??= fn () => null;
        $onIllegalMove ??= fn () => null;

        while (! $game->isEnded()) {

            $currentPlayer = $game->getCurrentPlayer();

            // The AiClientService will handle retries for failed requests
            // so this is a double retry mechanism in case the move is illegal
            $move = retry(
                times: $this->retryCount,
                callback: fn (int $attempts) => $this->getMoveId($game, $attempts),
                sleepMilliseconds: 1000,
                when: fn (Exception $e) => $onIllegalMove($game, $currentPlayer, $e) !== false,
            );

            $game->move($move);

            $onMoveMade($game, $currentPlayer, $move);
        }
    }

    /**
     * Build the system prompt for a player
     */
    protected function buildSystemPrompt(ChessPlayer $player): string
    {
        return "You are an expert chess AI for {$player->name()}. Respond ONLY with valid JSON containing a key 'move_id' with the ID of the move you want to make. Do not include any other text or explanations. The response must be a valid JSON (e.g., {\"move_id\":5}).";
    }

    /**
     * Build the player prompt with current game state
     */
    protected function buildPlayerPrompt(ChessGame $game, int $failedAttempts): string
    {
        $prompt = implode("\n", [
            'Game: chess',
            'FEN: '.$game->getFen(),
            'Board:',
            $game->getBoardText(),
            'Legal Moves: '.json_encode($game->getValidMoves()),
            'Move History (SAN): '.json_encode($game->getMovesHistory()),
        ]);

        if ($failedAttempts > 1) {
            $prompt .= "\nWARNING: This is your failed attempt #$failedAttempts out of {$this->retryCount}.\n";
            $prompt .= "\nIf you don't provide a valid move, you will be disqualified and lose the game.\n";
            $prompt .= "\nYour number one priority should be to make a valid move.\n";
        }

        return $prompt;
    }

    /**
     * Get the response from the AI model
     */
    protected function getMoveId(ChessGame $game, int $attempts = 0): ChessMove
    {
        $aiModel = $game->getCurrentPlayerModel();

        if ($aiModel->name === 'random') {
            return collect($game->getValidMoves())->random();
        }

        $systemPrompt = $this->buildSystemPrompt($game->getCurrentPlayer());
        $playerPrompt = $this->buildPlayerPrompt($game, $attempts);

        $response = $this->aiClient->getResponse($aiModel->name, $systemPrompt, $playerPrompt);

        if (empty($response)) {
            throw new \Exception('Empty response from AI model: '.$aiModel->name);
        }

        $data = json_decode($response, true);
        $moveId = $data['move_id']
            ?? $data['move']
            ?? throw new \Exception('Invalid response format: '.$response);

        return $game->findValidMoveById((int) $moveId);
    }
}
