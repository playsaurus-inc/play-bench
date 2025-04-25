<?php

namespace App\Services\Chess;

use JsonSerializable;
use Stringable;

class ChessMove implements JsonSerializable, Stringable
{
    /**
     * Create a new chess move instance.
     *
     * @param  int  $id  The ID of the move. This id is used to identify the move in the movements list presented to the player.
     * @param  string  $san  The move representation in Standard Algebraic Notation (SAN).
     * @param  string  $uci  The move representation in Universal Chess Interface (UCI).
     * @param  array{from: string, to: string, promotion?: string}  $uciArray  The move representation in UCI format as an array.
     * @param  string  $piece  The piece being moved. (`)
     */
    public function __construct(
        public readonly int $id,
        public readonly string $san,
        public readonly string $uci,
        public readonly array $uciArray,
        public readonly string $piece,
    ) {}

    /**
     * Get the JSON representation of the move.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'san' => $this->san,
            'uci' => $this->uci,
            'piece' => $this->piece,
        ];
    }

    /**
     * Get the string representation of the move.
     */
    public function __toString(): string
    {
        return $this->san;
    }
}
