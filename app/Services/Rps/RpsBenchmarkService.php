<?php

namespace App\Services\Rps;

use App\Models\AiModel;
use App\Models\RpsMatch;
use App\Services\AiClient\AiClientService;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class RpsBenchmarkService
{
    /**
     * The number of times to retry a failed request
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
     * Run a single RPS match between two AI models
     */
    public function runMatch(AiModel $player1, AiModel $player2): RpsMatch
    {
        $game = new RpsGame($player1, $player2, targetScore: 50);
        $startedAt = now();

        while (true) {
            try {
                $player1Move = $this->getMove($game, forPlayer: RpsPlayer::Player1);
                $player2Move = $this->getMove($game, forPlayer: RpsPlayer::Player2);
            } catch (\Exception $e) {
                Log::error('Error getting move from AI', [
                    'player1' => $player1->name,
                    'player2' => $player2->name,
                    'error' => $e->getMessage(),
                ]);

                break; // If we can't get a move, we assume the game is over
            }

            $round = $game->addRound($player1Move, $player2Move);

            Log::debug('RPS round completed', [
                'round' => $game->getRoundCount(),
                'round' => $round->long(),
                'player1_score' => $game->getPlayer1Score(),
                'player2_score' => $game->getPlayer2Score(),
            ]);

            if ($game->isOver()) {
                break;
            }
        }

        return $this->createMatch($game, $startedAt);
    }

    /**
     * Creates a new RPS match instance from the game state
     */
    protected function createMatch(RpsGame $game, CarbonInterface $startedAt): RpsMatch
    {
        // Winner and remaining properties will be determined automatically by the model's saving logic
        return RpsMatch::create([
            'player1_id' => $game->getPlayer1()->id,
            'player2_id' => $game->getPlayer2()->id,
            'started_at' => $startedAt,
            'ended_at' => now(),
            'move_history' => $game->getRoundHistory(),
            'rounds_played' => count($game->getRounds()),
            'player1_score' => $game->getPlayer1Score(),
            'player2_score' => $game->getPlayer2Score(),
            'is_forced_completion' => !$game->isOver(),
        ]);
    }

    /**
     * Get the response from the AI model
     */
    protected function getMove(RpsGame $game, RpsPlayer $forPlayer): RpsMove
    {
        return retry(
            times: $this->retryCount,
            callback: fn() => $this->requestMove($game, $forPlayer),
            sleepMilliseconds: 1000
        );
    }

    /**
     * Request a move from the AI model
     */
    protected function requestMove(RpsGame $game, RpsPlayer $player): RpsMove
    {
        $aiModel = $game->getPlayer($player);

        if ($aiModel->name === 'random') {
            return RpsMove::random();
        }

        $response = $this->aiClient->getResponse(
            modelName: $aiModel->name,
            systemPrompt: $this->buildSystemPrompt($player),
            userPrompt: $this->buildUserPrompt($game, $player),
        );

        return $this->extractMoveFromResponse($response);
    }

    /**
     * Extract the move from the AI response
     */
    protected function extractMoveFromResponse(string $response): RpsMove
    {
        if (
            preg_match('/"move"\s*:\s*"([^"]+)"/', $response, $matches) ||
            preg_match('/\b(rock|paper|scissors)\b/i', $response, $matches) ||
            preg_match('/\b(r|p|s)\b/i', $response, $matches)
        ) {
            return RpsMove::parse($matches[1]);
        }

        throw new \Exception('Could not parse RPS move from response: '.$response);
    }

    /**
     * Build the system prompt for a player
     */
    public function buildSystemPrompt(RpsPlayer $player): string
    {
        return "You are an expert Rock-Paper-Scissors AI for {$player->name()}. Respond ONLY with valid JSON containing a key 'move' with your move: rock, paper, or scissors.";
    }

    /**
     * Build the player prompt with current game state
     */
    public function buildUserPrompt(RpsGame $game, RpsPlayer $player): string
    {
        $prompt = "Game: Rock-Paper-Scissors\n";
        $prompt .= "You are: {$player->name()}\n";
        $prompt .= "Current Score - Player1: {$game->getPlayer1Score()}, Player2: {$game->getPlayer2Score()}\n";

        if ($history = $game->getRoundHistory(withRoundNumbers: true)) {
            $prompt .= "Condensed History: {$history}\n";
            $prompt .= "Interpretation: Each history token is of the form [round][P1 move][P2 move][result]. 'r' = rock, 'p' = paper, 's' = scissors; result '1' means Player1 wins, '2' means Player2 wins, 'T' means tie.\n";
        } else {
            $prompt .= "Condensed History: None\n";
        }

        $prompt .= "Legal moves: rock, paper, scissors\n";
        $prompt .= 'Please provide your move in JSON format (e.g., {"move":"rock"}).';

        return $prompt;
    }
}
