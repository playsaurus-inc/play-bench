<?php

namespace App\Services\Rps;

use App\Models\AiModel;
use App\Models\RpsMatch;
use App\Services\AiClient\AiClientService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RpsBenchmarkService
{
    /**
     * The number of times to retry a failed request
     */
    protected int $retryCount = 1;

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
    public function runMatch(AiModel $player1, AiModel $player2, int $maxRounds = 50): RpsMatch
    {
        // Create a new match record
        $match = new RpsMatch;
        $match->player1_id = $player1->id;
        $match->player2_id = $player2->id;
        $match->started_at = Date::now();
        $match->move_history = '';
        $match->rounds_played = 0;
        $match->player1_score = 0;
        $match->player2_score = 0;

        Log::info('Starting RPS match', [
            'match_id' => $match->id,
            'player1' => $player1->name,
            'player2' => $player2->name,
            'max_rounds' => $maxRounds,
        ]);

        // System prompts for each player
        $player1SystemPrompt = $this->buildSystemPrompt('player1');
        $player2SystemPrompt = $this->buildSystemPrompt('player2');

        $p1Score = 0;
        $p2Score = 0;
        $moveHistory = [];

        // Run the match
        $isForced = false;

        for ($round = 1; $round <= $maxRounds; $round++) {
            // Build the player prompts with current game state
            $player1Prompt = $this->buildPlayerPrompt('player1', $p1Score, $p2Score, $moveHistory);
            $player2Prompt = $this->buildPlayerPrompt('player2', $p1Score, $p2Score, $moveHistory);

            // [$player1Answer, $player2Answer] = Concurrency::run([
            //    fn () => app(AiClientService::class)->getResponse($player1->name, $player1SystemPrompt, $player1Prompt, 'rps'),
            //    fn() => app(AiClientService::class)->getResponse($player2->name, $player2SystemPrompt, $player2Prompt, 'rps'),
            // ]);
            $player1Answer = $this->getResponse($player1, $player1SystemPrompt, $player1Prompt);
            $player2Answer = $this->getResponse($player2, $player2SystemPrompt, $player2Prompt);

            // Get the player moves
            $player1Move = $this->getNormalizedMove($player1Answer);
            $player2Move = $this->getNormalizedMove($player2Answer);

            // Determine the winner
            $roundResult = RpsMatch::determineRoundResult(
                $this->abbreviateMove($player1Move),
                $this->abbreviateMove($player2Move)
            );

            // Update scores
            if ($roundResult === '1') {
                $p1Score++;
            } elseif ($roundResult === '2') {
                $p2Score++;
            }

            // Add to move history
            $moveHistory[] = sprintf('%d%s%s%s',
                $round,
                $this->abbreviateMove($player1Move),
                $this->abbreviateMove($player2Move),
                $roundResult
            );

            Log::debug('RPS round completed', [
                'round' => $round,
                'match_id' => $match->id,
                'player1_move' => $player1Move,
                'player2_move' => $player2Move,
                'result' => $roundResult,
                'player1_score' => $p1Score,
                'player2_score' => $p2Score,
            ]);

            // Check if we have a clear winner (more than half the max rounds)
            if ($p1Score > $maxRounds / 2 || $p2Score > $maxRounds / 2) {
                Log::info('RPS match has a clear winner before completing all rounds', [
                    'match_id' => $match->id,
                    'completed_rounds' => $round,
                    'max_rounds' => $maxRounds,
                    'player1_score' => $p1Score,
                    'player2_score' => $p2Score,
                ]);
                break;
            }
        }

        // Update the match with final results
        $match->move_history = implode(' ', $moveHistory);
        $match->rounds_played = count($moveHistory);
        $match->player1_score = $p1Score;
        $match->player2_score = $p2Score;
        $match->ended_at = Date::now();
        $match->is_forced_completion = $isForced;

        // Winner will be determined automatically by the model's saving logic
        $match->save();

        Log::info('RPS match completed', [
            'match_id' => $match->id,
            'rounds_played' => $match->rounds_played,
            'player1_score' => $p1Score,
            'player2_score' => $p2Score,
            'winner' => $match->winner?->name ?? 'Tie',
            'duration_seconds' => $match->getDuration(),
        ]);

        return $match;
    }

    /**
     * Build the system prompt for a player
     */
    protected function buildSystemPrompt(string $player): string
    {
        return "You are an expert Rock-Paper-Scissors AI for {$player}. Respond ONLY with valid JSON containing a key 'move' with your move: rock, paper, or scissors.";
    }

    /**
     * Build the player prompt with current game state
     *
     * @param  string  $player  The player identifier ('player1' or 'player2')
     * @param  int  $player1Score  Current score for player 1
     * @param  int  $player2Score  Current score for player 2
     * @param  array  $moveHistory  Array of move history entries
     */
    protected function buildPlayerPrompt(string $player, int $player1Score, int $player2Score, array $moveHistory): string
    {
        $prompt = "Game: Rock-Paper-Scissors\n";
        $prompt .= "You are: {$player}\n";
        $prompt .= "Current Score - Player1: {$player1Score}, Player2: {$player2Score}\n";

        if (! empty($moveHistory)) {
            $prompt .= 'Condensed History: '.implode(' ', $moveHistory)."\n";
            $prompt .= "Interpretation: Each history token is of the form [round][P1 move][P2 move][result]. 'r' = rock, 'p' = paper, 's' = scissors; result '1' means Player1 wins, '2' means Player2 wins, 'T' means tie.\n";
        } else {
            $prompt .= "Condensed History: None\n";
        }

        $prompt .= "Legal moves: rock, paper, scissors\n";
        $prompt .= 'Please provide your move in JSON format (e.g., {"move":"rock"}).';

        return $prompt;
    }

    /**
     * Get the response from the AI model
     */
    protected function getResponse(AiModel $aiModel, string $systemPrompt, string $playerPrompt): string
    {
        if ($aiModel->name === 'random') {
            return ['rock', 'paper', 'scissors'][random_int(0, 2)];
        }

        $response = $this->aiClient->getResponse($aiModel->name, $systemPrompt, $playerPrompt);

        // Extract the move from a JSON response
        if (preg_match('/"move"\s*:\s*"([^"]+)"/', $response, $matches)) {
            return strtolower($matches[1]);
        }

        // If not in JSON format, look for "rock", "paper", or "scissors" keywords
        if (preg_match('/\b(rock|paper|scissors)\b/i', $response, $matches)) {
            return strtolower($matches[1]);
        }

        throw new \Exception('Could not parse RPS move from response: '.$response);
    }

    /**
     * Convert a move to its abbreviated form
     */
    protected function abbreviateMove(string $move): string
    {
        return substr(strtolower($move), 0, 1);
    }

    /**
     * Normalize a move response
     */
    protected function getNormalizedMove(string $move): string
    {
        $move = strtolower(trim($move));

        // Handle abbreviated moves
        if ($move === 'r') {
            return 'rock';
        } elseif ($move === 'p') {
            return 'paper';
        } elseif ($move === 's') {
            return 'scissors';
        }

        // If it's already a valid move, return it
        if (in_array($move, ['rock', 'paper', 'scissors'])) {
            return $move;
        }

        // Otherwise, try to find the move in the response
        if (Str::contains($move, 'rock')) {
            return 'rock';
        }

        if (Str::contains($move, 'paper')) {
            return 'paper';
        }

        if (Str::contains($move, 'scissors')) {
            return 'scissors';
        }

        // If all else fails, return a random move
        return ['rock', 'paper', 'scissors'][random_int(0, 2)];
    }
}
