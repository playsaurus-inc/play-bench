<?php

namespace App\Services;

use App\Models\AiModel;
use App\Models\ChessMatch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use PChess\Chess\Chess;
use PChess\Chess\Entry;
use PChess\Chess\Piece;

class ChessBenchmarkService
{
    /**
     * Max number of moves before forcing a draw
     */
    protected const MAX_MOVES = 100;

    /**
     * Max number of illegal moves before forfeiting
     */
    protected const MAX_ILLEGAL_MOVES = 3;

    /**
     * Create a new service instance.
     */
    public function __construct(
        protected AiClientService $aiClient
    ) {}

    /**
     * Get all available AI models for the chess benchmark
     */
    public function getAvailableModels(): Collection
    {
        return AiModel::whereIn('name', $this->aiClient->getAvailableModels())->get();
    }

    /**
     * Run a single chess match between two AI models
     */
    public function runMatch(AiModel $whitePlayer, AiModel $blackPlayer): ChessMatch
    {
        // Create a new match record
        $match = new ChessMatch();
        $match->white_id = $whitePlayer->id;
        $match->black_id = $blackPlayer->id;
        $match->started_at = Date::now();
        $match->ply_count = 0;
        $match->illegal_moves_white = 0;
        $match->illegal_moves_black = 0;
        $match->is_forced_completion = false;

        Log::info('Starting chess match', [
            'match_id' => $match->id,
            'white' => $whitePlayer->name,
            'black' => $blackPlayer->name,
        ]);

        try {
            // Initialize a new chess game
            $chess = new Chess();
            $isForced = false;
            $result = null;
            $moves = [];
            $illegalMoves = [];
            $illegalMovesWhite = 0;
            $illegalMovesBlack = 0;

            // System prompts for each player
            $whiteSystemPrompt = $this->buildSystemPrompt('White');
            $blackSystemPrompt = $this->buildSystemPrompt('Black');

            $moveCounter = 0;

            while (!$chess->gameOver() && $moveCounter < self::MAX_MOVES) {
                $playerToMove = $chess->turn === Piece::WHITE ? $whitePlayer : $blackPlayer;
                $systemPrompt = $chess->turn === Piece::WHITE ? $whiteSystemPrompt : $blackSystemPrompt;
                $playerPrompt = $this->buildPlayerPrompt($chess, $illegalMoves);

                $aiMove = $this->aiClient->getResponse(
                    $playerToMove,
                    $systemPrompt,
                    $playerPrompt,
                    'chess'
                );

                // Validate and apply the move
                $validMove = $this->validateAndApplyMove($chess, $aiMove);

                if ($validMove) {
                    // Move was valid, record it
                    $moveCounter++;
                    $moves[] = $validMove;
                    // Clear illegal moves for this player
                    $illegalMoves = array_filter($illegalMoves, function($move) use ($chess) {
                        return $move['color'] !== $chess->turn;
                    });
                } else {
                    // Move was invalid, record it as illegal
                    if ($chess->turn === Piece::WHITE) {
                        $illegalMovesWhite++;
                        if ($illegalMovesWhite >= self::MAX_ILLEGAL_MOVES) {
                            $result = 'black'; // White forfeits due to too many illegal moves
                            $isForced = true;
                            break;
                        }
                    } else {
                        $illegalMovesBlack++;
                        if ($illegalMovesBlack >= self::MAX_ILLEGAL_MOVES) {
                            $result = 'white'; // Black forfeits due to too many illegal moves
                            $isForced = true;
                            break;
                        }
                    }

                    $illegalMoves[] = [
                        'move' => $aiMove,
                        'color' => $chess->turn,
                    ];
                }

                // Check if the game is over
                if ($chess->gameOver()) {
                    if ($chess->inCheckmate()) {
                        $result = $chess->turn === Piece::WHITE ? 'black' : 'white';
                    } else {
                        $result = 'draw';
                    }
                }
            }

            // If we reached the move limit, declare a draw
            if ($moveCounter >= self::MAX_MOVES && !$result) {
                $result = 'draw';
                $isForced = true;
            }

            // Generate PGN from the list of moves
            $pgn = $this->generatePgn($whitePlayer->name, $blackPlayer->name, $moves, $result);

            // Update the match with the final game state
            $match->pgn = $pgn;
            $match->final_fen = $chess->fen();
            $match->result = $result ?? 'draw';
            $match->ply_count = count($chess->getHistory()->getEntries());
            $match->illegal_moves_white = $illegalMovesWhite;
            $match->illegal_moves_black = $illegalMovesBlack;
            $match->is_forced_completion = $isForced;
            $match->ended_at = Date::now();

            // Winner will be determined and set by the model
            $match->save();

            Log::info('Chess match completed', [
                'match_id' => $match->id,
                'ply_count' => $match->ply_count,
                'result' => $match->result,
                'winner' => $match->winner?->name ?? 'Draw',
                'illegal_moves_white' => $illegalMovesWhite,
                'illegal_moves_black' => $illegalMovesBlack,
                'is_forced' => $isForced,
            ]);
        } catch (\Exception $e) {
            Log::error('Error during chess match', [
                'match_id' => $match->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update the match with error state
            $match->is_forced_completion = true;
            $match->result = 'draw'; // Default to draw in case of error
            $match->ended_at = Date::now();
            $match->save();
        }

        return $match;
    }

    /**
     * Build the system prompt for a player
     */
    protected function buildSystemPrompt(string $color): string
    {
        return "You are an expert chess AI for {$color}. Respond ONLY with valid JSON containing a key 'move' with your move in UCI format.";
    }

    /**
     * Build the player prompt with current game state
     */
    protected function buildPlayerPrompt(Chess $chess, array $illegalMoves): string
    {
        $prompt = "Game: chess\n";
        $prompt .= "FEN: " . $chess->fen() . "\n";
        $prompt .= $this->boardToText($chess) . "\n";

        // Get legal moves in UCI format
        $legalMoves = $this->getLegalMovesUci($chess);
        $prompt .= "Legal Moves (UCI): " . implode(', ', $legalMoves) . "\n";

        // Include move history
        $moveHistory = collect($chess->getHistory()->getEntries())
            ->map(fn (Entry $entry) => $entry->move->san)
            ->jsonSerialize();
        $prompt .= "Move History (SAN): " . $moveHistory . "\n";

        // Include illegal moves if any
        if (!empty($illegalMoves)) {
            $illegalMovesText = array_map(function($m) {
                return $m['move'] . ' (' . ($m['color'] === 'w' ? 'White' : 'Black') . ')';
            }, $illegalMoves);

            $prompt .= "Note: The following move(s) were illegal and must not be repeated: " . implode(', ', $illegalMovesText) . "\n";
        }

        $prompt .= "Please provide your next move in UCI format as valid JSON (e.g., {\"move\":\"e7e5\"}) select only from the list of legal moves.";

        return $prompt;
    }

    /**
     * Convert a chess board to text representation
     */
    protected function boardToText(Chess $chess): string
    {
        $board = $chess->board;
        $text = "Chess Board State:\n";

        for ($i = 0; $i < 8; $i++) {
            $rank = 8 - $i;
            $text .= $rank . " | ";

            for ($j = 0; $j < 8; $j++) {
                $piece = $board[$i][$j];
                $text .= ($piece ? ($piece['color'] === "w" ? strtoupper($piece['type']) : $piece['type']) : ".") . "  ";
            }

            $text .= "\n";
        }

        $text .= "    a  b  c  d  e  f  g  h\n";
        return $text;
    }

    /**
     * Get legal moves in UCI format
     */
    protected function getLegalMovesUci(Chess $chess): array
    {
        $moves = $chess->moves();
        return array_map(function($move) {
            return $move['from'] . $move['to'] . ($move['promotion'] ?? '');
        }, $moves);
    }

    /**
     * Validate and apply a move
     */
    protected function validateAndApplyMove(Chess $chess, string $moveUci): ?string
    {
        // Clean up the move string
        $moveUci = trim($moveUci);

        // Try to match the UCI format
        if (preg_match('/^([a-h][1-8])([a-h][1-8])([qrbnk]?)$/i', $moveUci, $matches)) {
            $from = $matches[1];
            $to = $matches[2];
            $promotion = $matches[3] ?: null;

            $moveObj = [
                'from' => $from,
                'to' => $to,
            ];

            if ($promotion) {
                $moveObj['promotion'] = strtolower($promotion);
            }

            // Attempt to make the move
            $move = $chess->move($moveObj);

            if ($move) {
                return $move['san'];
            }
        }

        return null;
    }

    /**
     * Generate PGN from a list of moves
     */
    protected function generatePgn(string $white, string $black, array $moves, ?string $result): string
    {
        $resultMap = [
            'white' => '1-0',
            'black' => '0-1',
            'draw' => '1/2-1/2',
        ];

        $pgn = "";
        $pgn .= "[Event \"AI Chess Benchmark\"]\n";
        $pgn .= "[Site \"PlayBench\"]\n";
        $pgn .= "[Date \"" . date('Y.m.d') . "\"]\n";
        $pgn .= "[Round \"1\"]\n";
        $pgn .= "[White \"" . $white . "\"]\n";
        $pgn .= "[Black \"" . $black . "\"]\n";
        $pgn .= "[Result \"" . ($resultMap[$result] ?? '*') . "\"]\n\n";

        // Add the moves
        for ($i = 0; $i < count($moves); $i++) {
            if ($i % 2 === 0) {
                $pgn .= (($i / 2) + 1) . ". ";
            }

            $pgn .= $moves[$i] . " ";

            // Line break every 5 full moves
            if ($i % 10 === 9) {
                $pgn .= "\n";
            }
        }

        // Add the result
        $pgn .= $resultMap[$result] ?? '*';

        return $pgn;
    }
}
