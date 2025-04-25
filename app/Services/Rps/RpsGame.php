<?php

namespace App\Services\Rps;

use App\Models\AiModel;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use JsonSerializable;

class RpsGame implements JsonSerializable
{
    /**
     * The round history of the game.
     *
     * @var array<int, RpsRound>
     */
    protected array $rounds = [];

    /**
     * The score required to win the game.
     */
    protected int $targetScore = 50;

    /**
     * The player 1 of the game.
     */
    protected AiModel $player1;

    /**
     * The player 2 of the game.
     */
    protected AiModel $player2;

    /**
     * The player 1's score.
     */
    protected int $player1Score = 0;

    /**
     * The player 2's score.
     */
    protected int $player2Score = 0;

    /**
     * The time the game started.
     */
    protected CarbonInterface $startedAt;

    /**
     * The time the game ended.
     */
    protected ?CarbonInterface $endedAt = null;

    /**
     * Create a new game state instance.
     */
    public function __construct(
        AiModel $player1,
        AiModel $player2,
        int $targetScore = 50,
        string $rounds = '',
        CarbonInterface $startedAt = null,
    )
    {
        $this->targetScore = $targetScore;
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->startedAt = $startedAt ?? now();

        if ($rounds) {
            $rounds = str($rounds)
                ->explode(' ')
                ->map(fn (string $round) => RpsRound::fromString($round))
                ->all();

            foreach ($rounds as $round) {
                $this->addRound($round);
            }
        }
    }

    /**
     * Adds a round to the game state.
     */
    public function addRound(RpsRound $round): RpsRound
    {
        if ($this->endedAt) {
            throw new \RuntimeException('Game is already over');
        }

        $this->rounds[] = $round;

        if ($round->result === RpsRoundResult::Player1Win) {
            $this->player1Score++;
        } elseif ($round->result === RpsRoundResult::Player2Win) {
            $this->player2Score++;
        }

        if ($this->player1Score >= $this->targetScore || $this->player2Score >= $this->targetScore) {
            $this->endedAt = now();
        }

        return $round;
    }

    /**
     * Returns the player 1 of the game.
     */
    public function getPlayer1(): AiModel
    {
        return $this->player1;
    }

    /**
     * Returns the player 2 of the game.
     */
    public function getPlayer2(): AiModel
    {
        return $this->player2;
    }

    /**
     * Returns the given player AI model.
     */
    public function getPlayer(RpsPlayer $player): AiModel
    {
        return match ($player) {
            RpsPlayer::Player1 => $this->player1,
            RpsPlayer::Player2 => $this->player2,
        };
    }

    /**
     * Returns the round history of the game in a short string format.
     */
    public function getRoundHistory(bool $withRoundNumbers = false): string
    {
        if (!$withRoundNumbers) {
            return implode(' ', $this->rounds);
        }

        return collect($this->rounds)
            ->map(fn (RpsRound $round, int $index) => sprintf('%d: %s', $index + 1, $round))
            ->implode(' ');
    }

    /**
     * Returns an array of rounds.
     */
    public function getRounds(): array
    {
        return $this->rounds;
    }

    /**
     * Returns the number of rounds played.
     */
    public function getRoundCount(): int
    {
        return count($this->rounds);
    }

    /**
     * Returns the player 1's score.
     */
    public function getPlayer1Score(): int
    {
        return $this->player1Score;
    }

    /**
     * Returns the player 2's score.
     */
    public function getPlayer2Score(): int
    {
        return $this->player2Score;
    }

    /**
     * Checks if the game is over.
     */
    public function isOver(): bool
    {
        return $this->endedAt !== null;
    }

    /**
     * Returns the time the game started.
     */
    public function getStartedAt(): CarbonInterface
    {
        return $this->startedAt;
    }

    /**
     * Returns the time the game ended.
     */
    public function getEndedAt(): ?CarbonInterface
    {
        return $this->endedAt;
    }

    /**
     * Returns the target score.
     */
    public function getTargetScore(): int
    {
        return $this->targetScore;
    }

    /**
     * Specifies the data that should be serialized to JSON.
     */
    public function jsonSerialize(): array
    {
        return [
            'player1_id' => $this->player1->id,
            'player2_id' => $this->player2->id,
            'rounds' => $this->getRoundHistory(),
            'round_count' => $this->getRoundCount(),
            'player1_score' => $this->getPlayer1Score(),
            'player2_score' => $this->getPlayer2Score(),
            'is_over' => $this->isOver(),
            'target_score' => $this->targetScore,
            'started_at' => $this->startedAt->toDateTimeString(),
            'ended_at' => $this->endedAt?->toDateTimeString(),
        ];
    }

    /**
     * Creates a new game state instance from JSON data.
     */
    public static function fromJson(array $data): self
    {
        $player1 = AiModel::find($data['player1_id']);
        $player2 = AiModel::find($data['player2_id']);

        if (!$player1 || !$player2) {
            throw new \InvalidArgumentException('Invalid player IDs');
        }

        return new self(
            player1: $player1,
            player2: $player2,
            targetScore: $data['target_score'],
            rounds: $data['rounds']
        );
    }
}


