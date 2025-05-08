<?php

namespace App\Services;

use App\Models\AiModel;

class Matchup
{
    /**
     * Creates a new Matchup instance.
     */
    public function __construct(
        public AiModel $player1,
        public AiModel $player2,
        public int $matchesPlayed,
        bool $random = false,
    ) {
        if ($random) {
            $this->randomSwap();
        }
    }

    /**
     * Swaps the players in the matchup.
     */
    public function swap(): self
    {
        [$this->player1, $this->player2] = [$this->player2, $this->player1];

        return $this;
    }

    /**
     * Randomly swaps the players in the matchup.
     */
    public function randomSwap(): self
    {
        if (random_int(0, 1) === 1) {
            return $this->swap();
        }

        return $this;
    }
}
