<?php

namespace App\Support;

/**
 * Statistical utilities for game outcome analysis
 */
class Statistics
{
    /**
     * The default Z-score for a 95% confidence level (two-tailed).
     * This is the threshold for determining statistical significance.
     */
    public const DEFAULT_Z = 1.96;

    /**
     * Determines if the difference in scores between two players is statistically significant.
     *
     * @param  int  $score1         Player 1's number of wins
     * @param  int  $score2         Player 2's number of wins
     * @param  float  $confidenceZ  Z-score threshold for significance (default: 1.96 for ≈95% confidence)
     * @return bool True if the difference is statistically significant
     */
    public static function isScoreDifferenceSignificant(
        int $score1,
        int $score2,
        float $confidenceZ = self::DEFAULT_Z
    ): bool {
        // Step 1: Compute how many rounds had an actual winner (i.e., not ties)
        $decisiveRounds = $score1 + $score2;

        // Step 2: Edge case: If there are no decisive rounds, we cannot evaluate anything
        if ($decisiveRounds === 0) {
            return false;
        }

        // Step 3: Compute the observed win proportion of Player 1
        $pHat = $score1 / $decisiveRounds;

        // Step 4: Under the null hypothesis, we assume both players are equally good.
        // That means the expected probability of Player 1 winning a round is 0.5
        $pNull = 0.5;

        // Step 5: Compute the standard deviation of the observed win proportion,
        // assuming Player 1 has a true 50% win rate. This is based on binomial variance:
        //     std dev = sqrt(p * (1 - p) / n)
        $stdDev = sqrt($pNull * (1 - $pNull) / $decisiveRounds);

        // Step 6: Convert the difference between observed and expected win rates into a z-score.
        // This tells us how many "standard deviations" the result is away from what we'd expect by chance.
        $zScore = ($pHat - $pNull) / $stdDev;

        // Step 7: Compare the absolute z-score to our confidence threshold (default 1.96 for 95% two-sided).
        // If it's greater, the win difference is unlikely due to chance, so we reject the null hypothesis.
        return abs($zScore) > $confidenceZ;
    }

    /**
     * Calculate the maximum allowed score difference that is still considered
     * statistically insignificant (i.e. a "tie") based on the number of decisive rounds.
     *
     * This is useful to explain *why* a match with an unequal score might still be
     * considered a tie, if the observed difference is too small to be confidently
     * distinguished from random noise.
     *
     * @param  int   $score1         Player 1's number of wins
     * @param  int   $score2         Player 2's number of wins
     * @param  float $confidenceZ    Z-score threshold (e.g. 1.96 for ≈95% confidence)
     * @return float                 Maximum difference that would *not* be statistically significant
     */
    public static function getDifferenceThreshold(
        int $score1,
        int $score2,
        float $confidenceZ = self::DEFAULT_Z
    ): float {
        // Step 1: Determine how many *decisive rounds* were played.
        // Only rounds that ended in a win (not a tie) carry information about player skill.
        // We count these as the sum of wins by Player 1 and Player 2.
        $decisiveRounds = $score1 + $score2;

        // Step 2: Edge case — if no one won any round, then there are zero decisive rounds.
        // In that case, we can't measure any difference at all, so we return 0.
        if ($decisiveRounds === 0) {
            return 0.0;
        }

        // Step 3: Under the null hypothesis (H0), we assume both players are equally skilled.
        // That means the true probability of either one winning any decisive round is 50%.
        //
        // Let D = (score1 - score2) be the *difference in wins*.
        // If Player 1 and Player 2 were equally skilled, then D is expected to be around 0.
        // But D is a random variable, and it will naturally vary just due to luck.
        //
        // What is the expected variability (standard deviation) of D?
        // Turns out:
        //   Var(D) = Var(2X - n) where X ~ Binomial(n, 0.5)
        //          = 4 * Var(X)
        //          = 4 * n * 0.5 * (1 - 0.5)
        //          = n
        //
        // So the standard deviation of the score *difference* D is:
        $stdDevOfDifference = sqrt($decisiveRounds);

        // Step 4: Convert the desired confidence level (e.g., z = 1.96 for 95%) into
        // a maximum allowable difference. Any observed difference smaller than this
        // is still plausible under the assumption of equal skill.
        return $confidenceZ * $stdDevOfDifference;
    }
}
