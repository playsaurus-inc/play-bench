<?php

namespace App\Services\Chess;

use App\Models\AiModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use JsonSerializable;
use PChess\Chess\Chess;
use PChess\Chess\Entry;
use PChess\Chess\Move;
use PChess\Chess\Output\AsciiOutput;
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
     * The list of valid moves available to play in the next turn.
     *
     * @var Collection<ChessMove>
     */
    protected Collection $validMoves;

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
        $this->chess = new Chess;
        $this->whitePlayer = $whitePlayer;
        $this->blackPlayer = $blackPlayer;
        $this->startedAt = $startedAt ?? Date::now();
        $this->validMoves = $this->computeValidMoves();
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
     * Returns an array of valid moves.
     *
     * @return array<ChessMove>
     */
    public function getValidMoves(): array
    {
        return $this->validMoves->all();
    }

    /**
     * Finds a valid move by its ID.
     */
    public function findValidMoveById(int $id): ?ChessMove
    {
        return $this->validMoves->firstWhere('id', $id);
    }

    /**
     * Find a valid move by its SAN representation.
     */
    public function findValidMoveBySan(string $san): ?ChessMove
    {
        return $this->validMoves->firstWhere('san', $san);
    }

    /**
     * Find a valid move by its UCI representation.
     */
    public function findValidMoveByUci(string $uci): ?ChessMove
    {
        return $this->validMoves->firstWhere('uci', $uci);
    }

    /**
     * Make a move in the game.
     */
    public function move(ChessMove $move): void
    {
        if ($this->isEnded()) {
            throw new \Exception('Game is already over.');
        }

        if ($this->findValidMoveById($move->id) !== $move) {
            // Move was not valid, this should only happen if the move was outdated for some reason.
            throw new \Exception('Invalid move: '.$move->uci);
        }

        $chessMove = $this->chess->move($move->uciArray);

        if (! $chessMove) {
            // Move was invalid, this should only happen if the move was outdated for some reason.
            throw new \Exception('Invalid move: '.$move->uci);
        }

        // Check game over conditions

        if ($this->chess->gameOver()) {
            if ($this->chess->inCheckmate()) {
                $winner = $this->chess->turn === Piece::WHITE ? 'black' : 'white';
                $this->endGame($winner, isForced: false);
            } else {
                $this->endGame('draw', isForced: false);
            }

            return;
        }

        if (count($this->chess->getHistory()->getEntries()) >= self::MAX_MOVES) {
            $this->endGame('draw', isForced: true);

            return;
        }

        // Prepare for next turn
        $this->validMoves = $this->computeValidMoves();
    }

    /**
     * Compute the valid moves for the current player.
     *
     * @return Collection<ChessMove>
     */
    protected function computeValidMoves(): Collection
    {
        return collect($this->chess->moves())
            ->map(function (Move $move, int $index) {
                return new ChessMove(
                    id: $index,
                    san: $move->san,
                    uci: "{$move->from}{$move->to}".($move->promotion ?? ''),
                    piece: $move->piece->getType(),
                    uciArray: [
                        'from' => $move->from,
                        'to' => $move->to,
                        'promotion' => $move->promotion,
                    ],
                );
            });
    }

    /**
     * End the game with a specific result.
     */
    protected function endGame(string $result, bool $isForced): void
    {
        $this->result = $result;
        $this->isForced = $isForced;
        $this->endedAt = Date::now();
        $this->validMoves = collect();
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
        return (new AsciiOutput)->render($this->chess);
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
     * Check if the game is over.
     */
    public function isEnded(): bool
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
            'is_forced' => $this->isForced,
            'result' => $this->result,
            'ply_count' => $this->getPlyCount(),
            'is_over' => $this->isEnded(),
            'started_at' => $this->startedAt->format('Y-m-d H:i:s'),
            'ended_at' => $this->endedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
