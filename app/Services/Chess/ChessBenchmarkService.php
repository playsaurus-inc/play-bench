<?php

namespace App\Services\Chess;

use App\Models\AiModel;
use App\Services\AiClient\AiClientService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ChessBenchmarkService
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
     * Run a chess game
     *
     * @param callable|null $onMoveMade Callback after each move
     * @param callable|null $onIllegalMove Callback after illegal move
     */
    public function runGame(
        ChessGame $game,
        ?callable $onMoveMade = null,
        ?callable $onIllegalMove = null,
    ): void {
        // Set default callbacks
        $onMoveMade ??= fn () => null;
        $onIllegalMove ??= fn () => null;

        // System prompts for each player
        $whiteSystemPrompt = $this->buildSystemPrompt(ChessPlayer::White);
        $blackSystemPrompt = $this->buildSystemPrompt(ChessPlayer::Black);

        while (!$game->isOver()) {
            $currentPlayer = $game->getCurrentPlayer();
            $systemPrompt = $currentPlayer === ChessPlayer::White
                ? $whiteSystemPrompt
                : $blackSystemPrompt;

            $playerPrompt = $this->buildPlayerPrompt($game);

            echo "Current Player: {$currentPlayer->name()}\n";
            echo "Player Prompt: {$playerPrompt}\n";

            // Get move from AI model
            $aiMove = $this->getResponse(
                $game,
                $game->getCurrentPlayerModel(),
                $systemPrompt,
                $playerPrompt,
            );

            // Apply the move
            $validMove = $game->applyMove($aiMove);

            if ($validMove) {
                Log::info('Valid move made', [
                    'player' => $game->getCurrentPlayerModel()->name,
                    'color' => $currentPlayer->name(),
                    'move' => $validMove,
                ]);

                $onMoveMade($game, $currentPlayer, $validMove);
            } else {
                Log::warning('Illegal move attempted', [
                    'player' => $game->getCurrentPlayerModel()->name,
                    'color' => $currentPlayer->name(),
                    'move' => $aiMove,
                ]);

                $onIllegalMove($game, $currentPlayer, $aiMove);
            }
        }
    }

    /**
     * Build the system prompt for a player
     */
    protected function buildSystemPrompt(ChessPlayer $player): string
    {
        return "You are an expert chess AI for {$player->name()}. Respond ONLY with valid JSON containing a key 'move' with your move in UCI format.";
    }

    /**
     * Build the player prompt with current game state
     */
    protected function buildPlayerPrompt(ChessGame $game): string
    {
        $prompt = "Game: chess\n";
        $prompt .= 'FEN: '.$game->getFen()."\n";
        $prompt .= $game->getBoardText()."\n";

        // Get legal moves in UCI format
        $legalMoves = $game->getLegalMovesUci();
        $prompt .= 'Legal Moves (UCI): '.implode(', ', $legalMoves)."\n";

        // Include move history
        $moveHistory = json_encode($game->getMovesHistory());
        $prompt .= 'Move History (SAN): '.$moveHistory."\n";

        // Include illegal moves if any
        $illegalMoves = $game->getIllegalMoves();
        if (!empty($illegalMoves)) {
            $illegalMovesText = array_map(function ($m) {
                return $m['move'].' ('.($m['color'] === 'w' ? 'White' : 'Black').')';
            }, $illegalMoves);

            $prompt .= 'Note: The following move(s) were illegal and must not be repeated: '.implode(', ', $illegalMovesText)."\n";
        }

        $prompt .= 'Please provide your next move in UCI format as valid JSON (e.g., {"move":"e7e5"}) select only from the list of legal moves.';

        return $prompt;
    }

    /**
     * Get the response from the AI model
     */
    protected function getResponse(ChessGame $game, AiModel $aiModel, string $systemPrompt, string $playerPrompt): string
    {
        if ($aiModel->name === 'random') {
            // For random model, return a random move
            $legalMoves = $game->getLegalMovesUci();
            return $legalMoves[array_rand($legalMoves)];
        }

        $response = $this->aiClient->getResponse($aiModel->name, $systemPrompt, $playerPrompt);

        // Extract the move from a JSON response
        if (preg_match('/"move"\s*:\s*"([^"]+)"/', $response, $matches)) {
            return $matches[1];
        }

        // Look for a UCI format move (e.g., e2e4)
        if (preg_match('/\b([a-h][1-8][a-h][1-8][qrbnk]?)\b/', $response, $matches)) {
            return $matches[1];
        }

        throw new \Exception('Could not parse chess move from response: '.$response);
    }
}
