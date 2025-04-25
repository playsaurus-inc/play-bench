<?php

namespace App\Services\Rps;

use App\Models\AiModel;
use App\Services\AiClient\AiClientService;
use Illuminate\Database\Eloquent\Collection;

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
     *
     * @param null|callable<RpsRound> onRoundComplete Callback to be called after each round
     */
    public function runGame(RpsGame $game, ?callable $onRoundComplete = null): void
    {
        $onRoundComplete = $onRoundComplete ?? fn () => null;

        while (! $game->isOver()) {
            $player1Move = $this->getMove($game, forPlayer: RpsPlayer::Player1);
            $player2Move = $this->getMove($game, forPlayer: RpsPlayer::Player2);

            $round = $game->addRound(new RpsRound($player1Move, $player2Move));

            $onRoundComplete($round);
        }
    }

    /**
     * Get the response from the AI model
     */
    protected function getMove(RpsGame $game, RpsPlayer $forPlayer): RpsMove
    {
        return retry(
            times: $this->retryCount,
            callback: fn () => $this->requestMove($game, $forPlayer),
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
