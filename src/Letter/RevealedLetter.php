<?php

namespace Hangman\Letter;

final class RevealedLetter implements Letter
{
    public function __construct(private readonly string $letter)
    {
        if (mb_strlen($this->letter) !== 1) {
            throw new \InvalidArgumentException("Letter \"$this->letter\" must be 1 character");
        }
    }

    public function __toString(): string
    {
        return $this->letter;
    }

    public function is(string $letter): bool
    {
        return $this->letter === $letter;
    }
}