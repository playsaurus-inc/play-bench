<?php

namespace App\Services\Svg;

use App\Models\AiModel;
use Illuminate\Support\Facades\Date;
use JsonSerializable;

class SvgGame implements JsonSerializable
{
    /**
     * The creative image prompt text.
     */
    protected ?string $prompt = null;

    /**
     * Player 1's SVG content.
     */
    protected ?string $player1Svg = null;

    /**
     * Player 2's SVG content.
     */
    protected ?string $player2Svg = null;

    /**
     * The judge's reasoning.
     */
    protected ?string $judgeReasoning = null;

    /**
     * The winner of the game.
     */
    protected ?SvgPlayer $winner = null;

    /**
     * The time the game started.
     */
    protected \DateTimeInterface $startedAt;

    /**
     * The time the game ended.
     */
    protected ?\DateTimeInterface $endedAt = null;

    /**
     * Create a new game state instance.
     */
    public function __construct(
        protected AiModel $player1,
        protected AiModel $player2,
        ?\DateTimeInterface $startedAt = null,
    ) {
        $this->startedAt = $startedAt ?? Date::now();
    }

    /**
     * Set the creative prompt for the SVG creation.
     */
    public function setPrompt(string $prompt): self
    {
        if ($this->isOver()) {
            throw new \RuntimeException('Game is already over');
        }

        $this->prompt = $prompt;

        return $this;
    }

    /**
     * Set an SVG submission for a player.
     */
    public function setSvg(SvgPlayer $player, string $svg): self
    {
        if ($this->isOver()) {
            throw new \RuntimeException('Game is already over');
        }

        if ($player === SvgPlayer::Player1) {
            $this->player1Svg = $svg;
        } else {
            $this->player2Svg = $svg;
        }

        return $this;
    }

    /**
     * Set the judgment result.
     */
    public function setJudgment(SvgPlayer $winner, string $reasoning): self
    {
        if ($this->isOver()) {
            throw new \RuntimeException('Game is already over');
        }

        if ($this->prompt === null || $this->player1Svg === null || $this->player2Svg === null) {
            throw new \RuntimeException('Cannot judge game before prompt and both SVGs are set');
        }

        $this->winner = $winner;
        $this->judgeReasoning = $reasoning;
        $this->endedAt = Date::now();

        return $this;
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
    public function getPlayer(SvgPlayer $player): AiModel
    {
        return match ($player) {
            SvgPlayer::Player1 => $this->player1,
            SvgPlayer::Player2 => $this->player2,
        };
    }

    /**
     * Get the prompt text.
     */
    public function getPrompt(): ?string
    {
        return $this->prompt;
    }

    /**
     * Get player 1's SVG.
     */
    public function getPlayer1Svg(): ?string
    {
        return $this->player1Svg;
    }

    /**
     * Get player 2's SVG.
     */
    public function getPlayer2Svg(): ?string
    {
        return $this->player2Svg;
    }

    /**
     * Get player's SVG by player enum.
     */
    public function getPlayerSvg(SvgPlayer $player): ?string
    {
        return match ($player) {
            SvgPlayer::Player1 => $this->player1Svg,
            SvgPlayer::Player2 => $this->player2Svg,
        };
    }

    /**
     * Get the judge's reasoning.
     */
    public function getJudgeReasoning(): ?string
    {
        return $this->judgeReasoning;
    }

    /**
     * Get the winner.
     */
    public function getWinner(): ?SvgPlayer
    {
        return $this->winner;
    }

    /**
     * Get the winning model.
     */
    public function getWinnerModel(): ?AiModel
    {
        if ($this->winner === null) {
            return null;
        }

        return $this->getPlayer($this->winner);
    }

    /**
     * Checks if the game is over.
     */
    public function isOver(): bool
    {
        return $this->endedAt !== null;
    }

    /**
     * Checks if the game has a prompt.
     */
    public function hasPrompt(): bool
    {
        return $this->prompt !== null;
    }

    /**
     * Checks if a player has submitted an SVG.
     */
    public function hasSvg(SvgPlayer $player): bool
    {
        return $this->getPlayerSvg($player) !== null;
    }

    /**
     * Checks if both players have submitted SVGs.
     */
    public function hasBothSvgs(): bool
    {
        return $this->player1Svg !== null && $this->player2Svg !== null;
    }

    /**
     * Returns the time the game started.
     */
    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    /**
     * Returns the time the game ended.
     */
    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    /**
     * Specifies the data that should be serialized to JSON.
     */
    public function jsonSerialize(): array
    {
        return [
            'player1_id' => $this->player1->id,
            'player2_id' => $this->player2->id,
            'prompt' => $this->prompt,
            'has_player1_svg' => $this->player1Svg !== null,
            'has_player2_svg' => $this->player2Svg !== null,
            'winner' => $this->winner?->value,
            'judge_reasoning' => $this->judgeReasoning,
            'is_over' => $this->isOver(),
            'started_at' => $this->startedAt->format('Y-m-d H:i:s'),
            'ended_at' => $this->endedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
