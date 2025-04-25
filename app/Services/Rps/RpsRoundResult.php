<?php

namespace App\Services\Rps;

enum RpsRoundResult: string
{
    case Player1Win = '1';
    case Player2Win = '2';
    case Tie = 't';

    /**
     * Get the result name.
     */
    public function name(): string
    {
        return match ($this) {
            self::Player1Win => 'Player 1 Win',
            self::Player2Win => 'Player 2 Win',
            self::Tie => 'Tie',
        };
    }
}
