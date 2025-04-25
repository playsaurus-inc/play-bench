<?php

namespace App\Services\Rps;

enum RpsPlayer: string
{
    case Player1 = '1';
    case Player2 = '2';

    /**
     * Get the move name.
     */
    public function name(): string
    {
        return match ($this) {
            self::Player1 => 'Player 1',
            self::Player2 => 'Player 2',
        };
    }
}
