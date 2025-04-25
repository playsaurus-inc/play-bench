<?php

namespace App\Services\Chess;

enum ChessPlayer: string
{
    case White = 'w';
    case Black = 'b';

    /**
     * Get the player name.
     */
    public function name(): string
    {
        return match ($this) {
            self::White => 'White',
            self::Black => 'Black',
        };
    }
}
