<?php

namespace App\Services\Rps;

use Illuminate\Support\Str;

enum RpsMove: string
{
    case Rock = 'r';
    case Paper = 'p';
    case Scissors = 's';

    /**
     * Get the move name.
     */
    public function name(): string
    {
        return match ($this) {
            self::Rock => 'Rock',
            self::Paper => 'Paper',
            self::Scissors => 'Scissors',
        };
    }

    /**
     * Gets a random move.
     */
    public static function random(): RpsMove
    {
        return match (rand(0, 2)) {
            0 => RpsMove::Rock,
            1 => RpsMove::Paper,
            2 => RpsMove::Scissors,
        };
    }

    /**
     * Try to parse a string into a RpsMove.
     */
    public static function tryParse(string $move): ?RpsMove
    {
        $move = strtolower(trim($move));

        return match ($move) {
            'rock', 'r' => RpsMove::Rock,
            'paper', 'p' => RpsMove::Paper,
            'scissors', 's' => RpsMove::Scissors,
            Str::contains($move, 'rock') => RpsMove::Rock,
            Str::contains($move, 'paper') => RpsMove::Paper,
            Str::contains($move, 'scissors') => RpsMove::Scissors,
            default => null,
        };
    }

    /**
     * Parse a string into a RpsMove.
     */
    public static function parse(string $move): RpsMove
    {
        return self::tryParse($move) ?? throw new \InvalidArgumentException("Invalid move: $move");
    }
}
