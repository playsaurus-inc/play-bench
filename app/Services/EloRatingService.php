<?php

namespace App\Services;

use App\Models\AiModel;
use App\Models\ChessMatch;
use App\Models\Contracts\RankedMatch;
use App\Models\RpsMatch;
use App\Models\SvgMatch;
use App\Support\Statistics;
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
     * Update ELO ratings for all RPS matches.
     */
    public function updateRpsEloRatings(): void
    {
        $this->updateEloRatings('rps');
    }

    /**
     * Update ELO ratings for all SVG matches.
     */
    public function updateSvgEloRatings(): void
    {
        $this->updateEloRatings('svg');
    }

    /**
     * Update ELO ratings for all Chess matches.
     */
    public function updateChessEloRatings(): void
    {
        $this->updateEloRatings('chess');
    }

    /**
     * Calculate new ELO ratings for players based on match outcome.
     *
     * @param  float  $player1Elo  Current ELO rating of player 1
     * @param  float  $player2Elo  Current ELO rating of player 2
     * @param  string  $outcome  '1' for player 1 win, '2' for player 2 win, 't' for tie
     * @return array{player1_new_elo: float, player2_new_elo: float} New ELO ratings
     */
    protected function calculateElo(float $player1Elo, float $player2Elo, string $outcome): array
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
     * @param  float  $playerElo  Player's current ELO rating
     * @param  float  $opponentElo  Opponent's current ELO rating
     * @return float Expected score between 0 and 1
     */
    protected function calculateExpectedScore(float $playerElo, float $opponentElo): float
    {
        return 1.0 / (1.0 + pow(10, ($opponentElo - $playerElo) / 400));
    }

    /**
     * Update ELO ratings for all matches of a specific type.
     *
     * @param  string  $gameType  The game type ('rps', 'svg', or 'chess')
     * @return int Number of matches processed
     */
    protected function updateEloRatings(string $gameType): int
    {
        $count = 0;

        DB::transaction(function () use ($gameType, &$count) {

            $this->resetEloRatings($gameType);

            $matches = $this->getMatchesByType($gameType);

            foreach ($matches as $match) {
                $player1 = $match->getPlayer1();
                $player2 = $match->getPlayer2();

                $player1Elo = $player1->getAttribute($this->getEloColumnName($gameType));
                $player2Elo = $player2->getAttribute($this->getEloColumnName($gameType));

                $outcome = $match->getOutcome();
                $ratings = $this->calculateElo($player1Elo, $player2Elo, $outcome);

                $match->updateEloRatings(
                    $player1Elo,
                    $player2Elo,
                    $ratings['player1_new_elo'],
                    $ratings['player2_new_elo']
                );

                $player1->update([$this->getEloColumnName($gameType) => $ratings['player1_new_elo']]);
                $player2->update([$this->getEloColumnName($gameType) => $ratings['player2_new_elo']]);

                $count++;
            }

            $this->updateRankings($gameType);
        });

        return $count;
    }

    /**
     * Reset ELO ratings for all AI models for the specified game type.
     *
     * @param  string  $gameType  The game type ('rps', 'svg', or 'chess')
     */
    protected function resetEloRatings(string $gameType): void
    {
        AiModel::query()->update([
            $this->getEloColumnName($gameType) => static::DEFAULT_ELO,
        ]);
    }

    /**
     * Get the name of the ELO column in the AI models table for the specified game type.
     *
     * @param  string  $gameType  The game type ('rps', 'svg', or 'chess')
     * @return string The ELO column name
     */
    protected function getEloColumnName(string $gameType): string
    {
        return match ($gameType) {
            'rps' => 'rps_elo',
            'svg' => 'svg_elo',
            'chess' => 'chess_elo',
            default => throw new \InvalidArgumentException("Unknown game type: {$gameType}")
        };
    }

    /**
     * Get all matches of a specific type sorted by creation date.
     *
     * @param  string  $gameType  The game type ('rps', 'svg', or 'chess')
     * @return \Illuminate\Database\Eloquent\Collection<RankedMatch> Collection of matches
     */
    protected function getMatchesByType(string $gameType): \Illuminate\Database\Eloquent\Collection
    {
        return match ($gameType) {
            'rps' => RpsMatch::with(['player1', 'player2'])->orderBy('created_at')->get(),
            'svg' => SvgMatch::with(['player1', 'player2'])->orderBy('created_at')->get(),
            'chess' => ChessMatch::with(['white', 'black'])->orderBy('created_at')->get(),
            default => throw new \InvalidArgumentException("Unknown game type: {$gameType}")
        };
    }

    /**
     * Update rankings for all AI models for a specific game type.
     * The ranking is based on ELO rating (higher ELO = better rank).
     *
     * @param  string  $gameType  The game type ('rps', 'svg', or 'chess')
     */
    protected function updateRankings(string $gameType): void
    {
        $eloColumn = $this->getEloColumnName($gameType);
        $rankColumn = $this->getRankColumnName($gameType);

        // Get all AI models sorted by ELO rating descending
        $models = AiModel::query()
            ->orderByDesc($eloColumn)
            ->get();

        // Update rank for each model
        $rank = 1;
        foreach ($models as $model) {
            $model->update([
                $rankColumn => $rank++,
            ]);
        }
    }

    /**
     * Get the name of the rank column in the AI models table for the specified game type.
     *
     * @param  string  $gameType  The game type ('rps', 'svg', or 'chess')
     * @return string The rank column name
     */
    protected function getRankColumnName(string $gameType): string
    {
        return match ($gameType) {
            'rps' => 'rps_rank',
            'svg' => 'svg_rank',
            'chess' => 'chess_rank',
            default => throw new \InvalidArgumentException("Unknown game type: {$gameType}")
        };
    }

    /**
     * Computes the overall ELO rating and rank for all game types.
     */
    public function updateOverallEloRatings(): void
    {
        // We need to update overall `elo` and `rank` for all AI models
        // We need to first calculate for each game type the average ELO rating
        // and std deviation. We will then use these values to calculate the overall ELO rating.

        $models = AiModel::all();

        $stdD = collect([
            Statistics::standardDeviation($models->pluck('rps_elo')->toArray()),
            Statistics::standardDeviation($models->pluck('svg_elo')->toArray()),
            // Statistics::standardDeviation($models->pluck('chess_elo')->toArray()),
        ]);

        $mean = collect([
            $models->avg('rps_elo'),
            $models->avg('svg_elo'),
            // $models->avg('chess_elo'),
        ]);

        $targetEloMean = $mean->avg();
        $targetEloStdD = $stdD->avg();

        foreach ($models as $model) {
            $zScores = collect([
                Statistics::zScore($model->rps_elo, $mean[0], $stdD[0]),
                Statistics::zScore($model->svg_elo, $mean[1], $stdD[1]),
                // Statistics::zScore($model->chess_elo, $mean[2], $stdD[2]),
            ]);

            $zScore = $zScores->avg();

            // Scale the z-score to the target ELO mean and std deviation
            // otherwise the z-score by itself is near zero and doesn't look good
            $model->elo = $targetEloMean + ($zScore * $targetEloStdD);
        }

        $rank = 1;
        foreach ($models->sortByDesc('elo') as $model) {
            $model->rank = $rank++;
        }

        DB::transaction(fn () => $models->each->save());
    }
}
