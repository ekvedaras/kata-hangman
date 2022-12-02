<?php

namespace Hangman;

use Hangman\Letter\Letter;
use Hangman\Letter\RevealedLetter;
use Hangman\Letter\SecretLetter;
use InvalidArgumentException;
use RuntimeException;

class Hangman
{
    private GameStatus $status;

    /** @var array<int, Letter> */
    private array $letters = [];

    private int $remainingAttempts;

    public function __construct(string $secretWord, private readonly int $attempts)
    {
        if (empty($secretWord)) {
            throw new InvalidArgumentException('Cannot play with an empty secret word');
        }

        $this->letters = array_map(fn (string $letter) => new SecretLetter($letter), mb_str_split($secretWord));
        if ($this->attempts < count($this->letters)) {
            throw new InvalidArgumentException('Given number of attempts cannot be less than the length of the word');
        }

        $this->remainingAttempts = $this->attempts;
        $this->status = GameStatus::Playing;
    }

    public static function play(string $secretWord, int $attempts = 10)
    {
        return new self($secretWord, $attempts);
    }

    public function word(): string
    {
        return implode('', array_map('strval', $this->letters));
    }

    public function status(): GameStatus
    {
        return $this->status;
    }

    public function guesses(): int
    {
        return $this->attempts - $this->remainingAttempts;
    }

    public function remainingAttempts(): int
    {
        return $this->remainingAttempts;
    }

    public function guessLetter(string $guess): self
    {
        if ($this->status !== GameStatus::Playing) {
            throw new RuntimeException('Game has already ended.');
        }

        $guessedCharacters = mb_strlen($guess);
        if ($guessedCharacters !== 1) {
            throw new InvalidArgumentException('Must guess exactly 1 letter');
        }

        $this->remainingAttempts--;
        $revealedLetters = 0;

        foreach($this->letters as &$letter) {
            if ($letter instanceof RevealedLetter) {
                $revealedLetters++;
                continue;
            }

            if ($letter->is($guess)) {
                $letter = $letter->reveal();
                $revealedLetters++;
            }
        }

        if ($revealedLetters === count($this->letters)) {
            $this->status = GameStatus::Won;
        } else if ($this->remainingAttempts === 0) {
            $this->status = GameStatus::Lost;
        }

        return $this;
    }
}