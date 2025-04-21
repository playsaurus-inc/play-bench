<?php

namespace App\Support;

/**
 * Statistical utilities for game outcome analysis
 */
class Statistics
{
    /**
     * Default confidence level for statistical significance tests (approximately 95% confidence)
     */
    public const DEFAULT_CONFIDENCE_LEVEL = 2.0;

    /**
     * Determines if the difference between two scores is statistically significant.
     * Uses a binomial distribution model to determine if score differences could be due to random chance.
     *
     * @param int $score1 First player's score
     * @param int $score2 Second player's score
     * @param int $totalRounds Total number of rounds played
     * @param float $confidenceLevel Number of standard deviations for significance (default: 2.0 for ~95% confidence)
     * @return bool True if the difference is statistically significant
     */
    public static function isScoreDifferenceSignificant(
        int $score1,
        int $score2,
        int $totalRounds,
        float $confidenceLevel = self::DEFAULT_CONFIDENCE_LEVEL
    ): bool {
        // Handle edge cases
        if ($totalRounds <= 0) {
            return false;
        }

        // For a binomial distribution with p=0.5 (equal chance of winning)
        // the standard deviation is sqrt(n * p * (1-p))
        $standardDeviation = sqrt($totalRounds * 0.5 * 0.5);

        // Threshold for statistical significance based on confidence level
        $threshold = $confidenceLevel * $standardDeviation;

        // If absolute difference exceeds threshold, it's statistically significant
        return abs($score1 - $score2) > $threshold;
    }

    /**
     * Calculate the maximum allowable difference for statistical tie based on rounds played
     *
     * @param int $totalRounds Total number of rounds played
     * @param float $confidenceLevel Number of standard deviations for significance
     * @return float The maximum difference that would still be considered a tie
     */
    public static function getSignificanceThreshold(
        int $totalRounds,
        float $confidenceLevel = self::DEFAULT_CONFIDENCE_LEVEL
    ): float {
        if ($totalRounds <= 0) {
            return 0;
        }

        $standardDeviation = sqrt($totalRounds * 0.5 * 0.5);
        return $confidenceLevel * $standardDeviation;
    }
}
