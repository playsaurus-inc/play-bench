<?php

namespace App\Services\Rps;

use JsonSerializable;
use Stringable;

class RpsRound implements JsonSerializable, Stringable
{
    /**
     * The player 1's move.
     */
    public readonly RpsMove $player1Move;

    /**
     * The player 2's move.
     */
    public readonly RpsMove $player2Move;

    /**
     * The result of the round.
     */
    public readonly RpsRoundResult $result;

    /**
     * Create a new round instance.
     */
    public function __construct(RpsMove $player1Move, RpsMove $player2Move)
    {
        $this->player1Move = $player1Move;
        $this->player2Move = $player2Move;
        $this->result = $this->determineResult();
    }

    /**
     * Determine the result of the round.
     */
    protected function determineResult(): RpsRoundResult
    {
        if ($this->player1Move === $this->player2Move) {
            return RpsRoundResult::Tie;
        }

        if (
            ($this->player1Move === RpsMove::Rock && $this->player2Move === RpsMove::Scissors) ||
            ($this->player1Move === RpsMove::Paper && $this->player2Move === RpsMove::Rock) ||
            ($this->player1Move === RpsMove::Scissors && $this->player2Move === RpsMove::Paper)
        ) {
            return RpsRoundResult::Player1Win;
        }

        return RpsRoundResult::Player2Win;
    }

    /**
     * Get the short representation of the round.
     *
     * Example:
     * - "pr1" for player 1 paper, player 2 rock, player 1 win
     * - "sr2" for player 1 scissors, player 2 rock, player 2 win
     * - "sst" for player 1 scissors, player 2 scissors, tie
     */
    public function short(): string
    {
        return sprintf(
            '%s%s%s',
            $this->player1Move->value,
            $this->player2Move->value,
            $this->result->value
        );
    }

    /**
     * Get the long representation of the round.
     */
    public function long(): string
    {
        return sprintf(
            'Player 1: %s, Player 2: %s, Result: %s',
            $this->player1Move->name(),
            $this->player2Move->name(),
            $this->result->name()
        );
    }

    /**
     * Get the string representation of the round.
     */
    public function __toString(): string
    {
        return $this->short();
    }

    /**
     * Specify data which should be serialized to JSON.
     */
    public function jsonSerialize(): string
    {
        return $this->short();
    }

    /**
     * Create a new round instance from a string.
     */
    public static function fromString(string $round): self
    {
        if (strlen($round) !== 3) {
            throw new \InvalidArgumentException('Invalid round string format.');
        }

        return new self(
            RpsMove::parse($round[0]),
            RpsMove::parse($round[1]),
        );
    }
}
