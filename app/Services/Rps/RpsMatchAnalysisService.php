<?php

namespace App\Services\Rps;

use App\Models\RpsMatch;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class RpsMatchAnalysisService
{
    /**
     * Generate strategic insights for a Rock Paper Scissors match
     */
    public static function strategicInsights(RpsMatch $match): HtmlString
    {
        $player1Percentages = $match->player1_move_distribution_percentages;
        $player2Percentages = $match->player2_move_distribution_percentages;

        $matchOutcomeInsight = self::getMatchOutcomeInsight($match, $player1Percentages, $player2Percentages);
        $tieRateInsight = self::getTieRateInsight($match->getTieRate());

        return new HtmlString("{$matchOutcomeInsight} {$tieRateInsight}");
    }

    /**
     * Get insights about the match outcome and player strategies
     */
    private static function getMatchOutcomeInsight(RpsMatch $match, array $player1Percentages, array $player2Percentages): string
    {
        if ($match->isTie()) {
            return 'This match ended in a tie, with both models demonstrating equally effective strategies. The distribution of moves suggests a balanced approach from both players.';
        }

        if ($match->player1_score > $match->player2_score) {
            $playerName = Str::ucfirst($match->player1->name);
            $score = $match->player1_score;
            $winRate = Number::percentage($match->getPlayer1WinRate() * 100, 1);
            $movePercentages = $player1Percentages;
        } else {
            $playerName = Str::ucfirst($match->player2->name);
            $score = $match->player2_score;
            $winRate = Number::percentage($match->getPlayer2WinRate() * 100, 1);
            $movePercentages = $player2Percentages;
        }

        $insight = "{$playerName} demonstrated a superior strategy in this match, winning {$score} out of {$match->rounds_played} rounds ({$winRate} win rate).";

        // Check if there was a preferred move (over 40% usage)
        $maxMove = array_search(max($movePercentages), $movePercentages);
        $maxMovePercentage = max($movePercentages) * 100;

        if ($maxMovePercentage > 40) {
            $moveUsagePercentage = Number::percentage($maxMovePercentage, 1);
            $insight .= " They showed a preference for {$maxMove}, using it in {$moveUsagePercentage} of rounds.";
        } else {
            $insight .= ' They maintained a balanced distribution of moves, making their strategy harder to predict.';
        }

        return $insight;
    }

    /**
     * Get insights about the tie rate
     */
    private static function getTieRateInsight(float $tieRate): string
    {
        $tiePercentage = $tieRate * 100;
        $formattedTieRate = Number::percentage($tiePercentage, 1);

        if ($tiePercentage > 40) {
            return "The high tie rate ({$formattedTieRate}) suggests that both models may have been using similar strategies or were effectively countering each other's moves.";
        }

        if ($tiePercentage < 20) {
            return "The low tie rate ({$formattedTieRate}) indicates that the models were using distinctly different strategies, rarely making the same move.";
        }

        return "The match had a moderate tie rate of {$formattedTieRate}, typical for Rock Paper Scissors games.";
    }
}
