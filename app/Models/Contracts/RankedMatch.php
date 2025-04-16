<?php

namespace App\Models\Contracts;

use App\Models\AiModel;

interface RankedMatch
{
    /**
     * Gets the player 1 of the match.
     */
    public function getPlayer1(): AiModel;

    /**
     * Gets the player 2 of the match.
     */
    public function getPlayer2(): AiModel;

    /**
     * Gets the outcome of the match. '1' for player 1 win, '2' for player 2 win, 't' for tie.
     */
    public function getOutcome(): string;

    /**
     * Updates the ELO ratings of the match.
     */
    public function updateEloRatings(
        float $player1EloBefore,
        float $player2EloBefore,
        float $player1EloAfter,
        float $player2EloAfter
    ): void;
}
