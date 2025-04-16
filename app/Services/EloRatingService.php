<?php

namespace App\Services;

use App\Models\AiModel;
use App\Models\ChessMatch;
use App\Models\RpsMatch;
use App\Models\SvgMatch;
use Illuminate\Support\Facades\DB;

class EloRatingService
{
    /**
     * K-factor for ELO calculations.
     * Higher value means ratings change more quickly.
     */
    protected const K_FACTOR = 32;

    /**
     * Default ELO rating for new players.
     */
    protected const DEFAULT_ELO = 1000;

    /**
     * Calculate new ELO ratings for players based on match outcome.
     *
     * @param float $player1Elo Current ELO rating of player 1
     * @param float $player2Elo Current ELO rating of player 2
     * @param string $outcome '1' for player 1 win, '2' for player 2 win, 't' for tie
     * @return array{player1_new_elo: float, player2_new_elo: float} New ELO ratings
     */
    public function calculateElo(float $player1Elo, float $player2Elo, string $outcome): array
    {
        // Calculate expected scores
        $expected1 = $this->calculateExpectedScore($player1Elo, $player2Elo);
        $expected2 = $this->calculateExpectedScore($player2Elo, $player1Elo);

        // Determine actual scores based on outcome
        [$score1, $score2] = match ($outcome) {
            '1' => [1.0, 0.0],  // Player 1 wins
            '2' => [0.0, 1.0],  // Player 2 wins
            't' => [0.5, 0.5],  // Tie
            default => [0.5, 0.5],  // Default to tie for unknown outcomes
        };

        // Calculate new ratings
        $player1NewElo = $player1Elo + self::K_FACTOR * ($score1 - $expected1);
        $player2NewElo = $player2Elo + self::K_FACTOR * ($score2 - $expected2);

        return [
            'player1_new_elo' => $player1NewElo,
            'player2_new_elo' => $player2NewElo,
        ];
    }

    /**
     * Calculate the expected score for a player.
     *
     * @param float $playerElo Player's current ELO rating
     * @param float $opponentElo Opponent's current ELO rating
     * @return float Expected score between 0 and 1
     */
    protected function calculateExpectedScore(float $playerElo, float $opponentElo): float
    {
        return 1.0 / (1.0 + pow(10, ($opponentElo - $playerElo) / 400));
    }

    /**
     * Update ELO ratings for all RPS matches.
     *
     * @return int Number of matches processed
     */
    public function updateRpsEloRatings(): int
    {
        $count = 0;

        // Start a database transaction
        DB::transaction(function () use (&$count) {
            AiModel::query()->update(['rps_elo' => static::DEFAULT_ELO]);

            // Get all RPS matches sorted by creation date
            $matches = RpsMatch::with(['player1', 'player2'])
                ->orderBy('created_at')
                ->get();

            foreach ($matches as $match) {
                // Skip matches without proper player information
                if (!$match->player1 || !$match->player2) {
                    continue;
                }

                // Determine outcome
                $outcome = $this->getMatchOutcome($match);

                // Calculate new ELO ratings
                $ratings = $this->calculateElo(
                    $match->player1->rps_elo,
                    $match->player2->rps_elo,
                    $outcome
                );

                // Store original ELO ratings in the match
                $match->update([
                    'player1_elo_before' => $match->player1->rps_elo,
                    'player2_elo_before' => $match->player2->rps_elo,
                    'player1_elo_after' => $ratings['player1_new_elo'],
                    'player2_elo_after' => $ratings['player2_new_elo'],
                ]);

                // Update player ELO ratings
                $match->player1->update(['rps_elo' => $ratings['player1_new_elo']]);
                $match->player2->update(['rps_elo' => $ratings['player2_new_elo']]);

                $count++;
            }
        });

        return $count;
    }

    /**
     * Update ELO ratings for all SVG matches.
     *
     * @return int Number of matches processed
     */
    public function updateSvgEloRatings(): int
    {
        $count = 0;

        // Start a database transaction
        DB::transaction(function () use (&$count) {
            AiModel::query()->update(['svg_elo' => static::DEFAULT_ELO]);

            // Get all SVG matches sorted by creation date
            $matches = SvgMatch::with(['player1', 'player2', 'winner'])
                ->orderBy('created_at')
                ->get();

            foreach ($matches as $match) {
                // Skip matches without proper player information
                if (!$match->player1 || !$match->player2) {
                    continue;
                }

                // Determine outcome
                $outcome = $this->getSvgMatchOutcome($match);

                // Calculate new ELO ratings
                $ratings = $this->calculateElo(
                    $match->player1->svg_elo,
                    $match->player2->svg_elo,
                    $outcome
                );

                // Store original ELO ratings in the match
                $match->update([
                    'player1_elo_before' => $match->player1->svg_elo,
                    'player2_elo_before' => $match->player2->svg_elo,
                    'player1_elo_after' => $ratings['player1_new_elo'],
                    'player2_elo_after' => $ratings['player2_new_elo'],
                ]);

                // Update player ELO ratings
                $match->player1->update(['svg_elo' => $ratings['player1_new_elo']]);
                $match->player2->update(['svg_elo' => $ratings['player2_new_elo']]);

                $count++;
            }
        });

        return $count;
    }

    /**
     * Update ELO ratings for all Chess matches.
     *
     * @return int Number of matches processed
     */
    public function updateChessEloRatings(): int
    {
        $count = 0;

        // Start a database transaction
        DB::transaction(function () use (&$count) {
            AiModel::query()->update(['chess_elo' => static::DEFAULT_ELO]);

            // Get all chess matches sorted by creation date
            $matches = ChessMatch::with(['white', 'black'])
                ->orderBy('created_at')
                ->get();

            foreach ($matches as $match) {
                // Skip matches without proper player information
                if (!$match->white || !$match->black) {
                    continue;
                }

                // Determine outcome
                $outcome = $this->getChessMatchOutcome($match);

                // Calculate new ELO ratings
                $ratings = $this->calculateElo(
                    $match->white->chess_elo,
                    $match->black->chess_elo,
                    $outcome
                );

                // Store original ELO ratings in the match
                $match->update([
                    'white_elo_before' => $match->white->chess_elo,
                    'black_elo_before' => $match->black->chess_elo,
                    'white_elo_after' => $ratings['player1_new_elo'],
                    'black_elo_after' => $ratings['player2_new_elo'],
                ]);

                // Update player ELO ratings
                $match->white->update(['chess_elo' => $ratings['player1_new_elo']]);
                $match->black->update(['chess_elo' => $ratings['player2_new_elo']]);

                $count++;
            }
        });

        return $count;
    }

    /**
     * Determine the outcome of an RPS match for ELO calculation.
     *
     * @param RpsMatch $match
     * @return string '1' for player1 win, '2' for player2 win, 't' for tie
     */
    protected function getMatchOutcome(RpsMatch $match): string
    {
        if ($match->player1_score > $match->player2_score) {
            return '1';
        } elseif ($match->player2_score > $match->player1_score) {
            return '2';
        } else {
            return 't';
        }
    }

    /**
     * Determine the outcome of an SVG match for ELO calculation.
     *
     * @param SvgMatch $match
     * @return string '1' for player1 win, '2' for player2 win, 't' for tie
     */
    protected function getSvgMatchOutcome(SvgMatch $match): string
    {
        if ($match->winner_id === $match->player1_id) {
            return '1';
        } elseif ($match->winner_id === $match->player2_id) {
            return '2';
        } else {
            return 't'; // Though SVG matches typically don't have ties
        }
    }

    /**
     * Determine the outcome of a Chess match for ELO calculation.
     *
     * @param ChessMatch $match
     * @return string '1' for white win, '2' for black win, 't' for tie
     */
    protected function getChessMatchOutcome(ChessMatch $match): string
    {
        if ($match->result === 'white') {
            return '1';
        } elseif ($match->result === 'black') {
            return '2';
        } else {
            return 't'; // Draw
        }
    }
}
