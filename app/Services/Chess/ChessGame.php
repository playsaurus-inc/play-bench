<?php

namespace App\Services\Chess;

use App\Models\AiModel;
use Illuminate\Support\Facades\Date;
use JsonSerializable;
use PChess\Chess\Chess;
use PChess\Chess\Entry;
use PChess\Chess\Move;
use PChess\Chess\Piece;

class ChessGame implements JsonSerializable
{
    /**
     * The underlying chess game engine.
     */
    protected Chess $chess;

    /**
     * The white player.
     */
    protected AiModel $whitePlayer;

    /**
     * The black player.
     */
    protected AiModel $blackPlayer;

    /**
     * The history of illegal moves.
     *
     * @var array<array{move: string, color: string}>
     */
    protected array $illegalMoves = [];

    /**
     * The number of illegal moves by white.
     */
    protected int $illegalMovesWhite = 0;

    /**
     * The number of illegal moves by black.
     */
    protected int $illegalMovesBlack = 0;

    /**
     * Whether the game was forced to end (e.g., due to move limit or illegal moves).
     */
    protected bool $isForced = false;

    /**
     * The result of the game: 'white', 'black', or 'draw'.
     */
    protected ?string $result = null;

    /**
     * The time the game started.
     */
    protected \DateTimeInterface $startedAt;

    /**
     * The time the game ended.
     */
    protected ?\DateTimeInterface $endedAt = null;

    /**
     * Maximum number of moves before forcing a draw.
     */
    protected const MAX_MOVES = 100;

    /**
     * Maximum number of illegal moves before forfeiting.
     */
    protected const MAX_ILLEGAL_MOVES = 10;

    /**
     * Create a new chess game.
     */
    public function __construct(
        AiModel $whitePlayer,
        AiModel $blackPlayer,
        ?\DateTimeInterface $startedAt = null
    ) {
        $this->chess = new Chess();
        $this->whitePlayer = $whitePlayer;
        $this->blackPlayer = $blackPlayer;
        $this->startedAt = $startedAt ?? Date::now();
    }

    /**
     * Get the current player to move.
     */
    public function getCurrentPlayer(): ChessPlayer
    {
        return $this->chess->turn === Piece::WHITE
            ? ChessPlayer::White
            : ChessPlayer::Black;
    }

    /**
     * Get the current player model.
     */
    public function getCurrentPlayerModel(): AiModel
    {
        return $this->getCurrentPlayer() === ChessPlayer::White
            ? $this->whitePlayer
            : $this->blackPlayer;
    }

    /**
     * Convert a move to SAN format. The move can be either in UCI or SAN format.
     */
    protected function convertToSanOrArray(string $move): array|string
    {
        // Check if the move is in UCI format (e.g., e2e4)
        if (preg_match('/^[a-h][1-8][a-h][1-8](?:[qrbn])?$/', $move)) {
            return [
                'from' => $move[0].$move[1],
                'to' => $move[2].$move[3],
                'promotion' => isset($move[4]) ? $move[4] : null,
            ];
        }

        // Otherwise, assume it's in SAN format
        return $move;
    }

    /**
     * Apply a move in UCI format.
     */
    public function applyMove(string $move): ?string
    {
        // Clean up the move string
        $move = trim($move);

        $chessMove = $this->chess->move(
            $this->convertToSanOrArray($move)
        );

        if (! $chessMove) {
            // Move was invalid, record it as illegal
            $this->recordIllegalMove($move);
            return null;
        }

        if ($this->chess->gameOver() && !$this->isOver()) {
            if ($this->chess->inCheckmate()) {
                $this->endGame(
                    $this->chess->turn === Piece::WHITE ? 'black' : 'white',
                    false
                );
            } else {
                $this->endGame('draw', false);
            }
        }

        if (count($this->chess->getHistory()->getEntries()) >= self::MAX_MOVES && !$this->isOver()) {
            $this->endGame('draw', true);
        }

        return $chessMove->san;
    }

    /**
     * Record an illegal move attempt.
     */
    protected function recordIllegalMove(string $moveUci): void
    {
        $currentPlayer = $this->getCurrentPlayer();

        $this->illegalMoves[] = [
            'move' => $moveUci,
            'color' => $currentPlayer->value,
        ];

        if ($currentPlayer === ChessPlayer::White) {
            $this->illegalMovesWhite++;
            if ($this->illegalMovesWhite >= self::MAX_ILLEGAL_MOVES) {
                $this->endGame('black', true); // White forfeits due to too many illegal moves
            }
        } else {
            $this->illegalMovesBlack++;
            if ($this->illegalMovesBlack >= self::MAX_ILLEGAL_MOVES) {
                $this->endGame('white', true); // Black forfeits due to too many illegal moves
            }
        }
    }

    /**
     * End the game with a specific result.
     */
    protected function endGame(string $result, bool $isForced): void
    {
        $this->result = $result;
        $this->isForced = $isForced;
        $this->endedAt = Date::now();
    }

    /**
     * Get the FEN representation of the current board.
     */
    public function getFen(): string
    {
        return $this->chess->fen();
    }

    /**
     * Get a text representation of the board.
     */
    public function getBoardText(): string
    {
        $board = $this->chess->board;
        $text = "Chess Board State:\n";

        for ($i = 0; $i < 8; $i++) {  // i represents rank (0 = rank 8, 7 = rank 1)
            $rank = 8 - $i;
            $text .= $rank.' | ';

            for ($j = 0; $j < 8; $j++) {  // j represents file (0 = a, 7 = h)
                $piece = $board[$i * 16 + $j]; // Calculate the correct square index

                if ($piece !== null) {
                    $symbol = $piece->getType();
                    if ($piece->getColor() === 'w') {
                        $symbol = strtoupper($symbol);
                    }
                    $text .= $symbol.'  ';
                } else {
                    $text .= '.  ';
                }
            }

            $text .= "\n";
        }

        $text .= "    a  b  c  d  e  f  g  h\n";

        return $text;
    }

    /**
     * Get legal moves in UCI format.
     */
    public function getLegalMovesUci(): array
    {
        $moves = $this->chess->moves();

        return array_map(function (Move $move) {
            return $move->from.$move->to.($move->promotion ?? '');
        }, $moves);
    }

    /**
     * Get the move history as an array of SAN strings.
     *
     * @return array<string>
     */
    public function getMovesHistory(): array
    {
        return collect($this->chess->getHistory()->getEntries())
            ->map(fn (Entry $entry) => $entry->move->san)
            ->all();
    }

    /**
     * Get the move count.
     */
    public function getMoveCount(): int
    {
        return count($this->chess->getHistory()->getEntries());
    }

    /**
     * Get the illegal moves history.
     */
    public function getIllegalMoves(): array
    {
        return $this->illegalMoves;
    }

    /**
     * Get the white player.
     */
    public function getWhitePlayer(): AiModel
    {
        return $this->whitePlayer;
    }

    /**
     * Get the black player.
     */
    public function getBlackPlayer(): AiModel
    {
        return $this->blackPlayer;
    }

    /**
     * Get the player by enum.
     */
    public function getPlayer(ChessPlayer $player): AiModel
    {
        return $player === ChessPlayer::White
            ? $this->whitePlayer
            : $this->blackPlayer;
    }

    /**
     * Get the number of illegal moves by white.
     */
    public function getIllegalMovesWhite(): int
    {
        return $this->illegalMovesWhite;
    }

    /**
     * Get the number of illegal moves by black.
     */
    public function getIllegalMovesBlack(): int
    {
        return $this->illegalMovesBlack;
    }

    /**
     * Check if the game is over.
     */
    public function isOver(): bool
    {
        return $this->endedAt !== null;
    }

    /**
     * Check if the game was forced to end.
     */
    public function isForced(): bool
    {
        return $this->isForced;
    }

    /**
     * Get the result.
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * Get the time the game started.
     */
    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    /**
     * Get the time the game ended.
     */
    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    /**
     * Generate PGN from the list of moves.
     */
    public function generatePgn(): string
    {
        $resultMap = [
            'white' => '1-0',
            'black' => '0-1',
            'draw' => '1/2-1/2',
        ];

        $pgn = '';
        $pgn .= "[Event \"AI Chess Benchmark\"]\n";
        $pgn .= "[Site \"PlayBench\"]\n";
        $pgn .= '[Date "'.date('Y.m.d')."\"]\n";
        $pgn .= "[Round \"1\"]\n";
        $pgn .= '[White "'.$this->whitePlayer->name."\"]\n";
        $pgn .= '[Black "'.$this->blackPlayer->name."\"]\n";
        $pgn .= '[Result "'.($resultMap[$this->result] ?? '*')."\"]\n\n";

        // Add the moves
        $moves = $this->getMovesHistory();
        for ($i = 0; $i < count($moves); $i++) {
            if ($i % 2 === 0) {
                $pgn .= (($i / 2) + 1).'. ';
            }

            $pgn .= $moves[$i].' ';

            // Line break every 5 full moves
            if ($i % 10 === 9) {
                $pgn .= "\n";
            }
        }

        // Add the result
        $pgn .= $resultMap[$this->result] ?? '*';

        return $pgn;
    }

    /**
     * Get the PChess engine instance.
     */
    public function getEngine(): Chess
    {
        return $this->chess;
    }

    /**
     * Get the ply count.
     */
    public function getPlyCount(): int
    {
        return count($this->chess->getHistory()->getEntries());
    }

    /**
     * Specify data which should be serialized to JSON.
     */
    public function jsonSerialize(): array
    {
        return [
            'white_id' => $this->whitePlayer->id,
            'black_id' => $this->blackPlayer->id,
            'moves' => $this->getMovesHistory(),
            'illegal_moves_white' => $this->illegalMovesWhite,
            'illegal_moves_black' => $this->illegalMovesBlack,
            'is_forced' => $this->isForced,
            'result' => $this->result,
            'ply_count' => $this->getPlyCount(),
            'is_over' => $this->isOver(),
            'started_at' => $this->startedAt->format('Y-m-d H:i:s'),
            'ended_at' => $this->endedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
