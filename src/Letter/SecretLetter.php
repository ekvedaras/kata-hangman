<?php

namespace Hangman\Letter;

final class SecretLetter implements Letter
{
    public function __construct(private readonly string $letter)
    {
        if (mb_strlen($this->letter) !== 1) {
            throw new \InvalidArgumentException("Letter \"$this->letter\" must be 1 character");
        }
    }

    public function reveal(): RevealedLetter
    {
        return new RevealedLetter($this->letter);
    }

    public function __toString(): string
    {
        return '*';
    }

    public function is(string $letter): bool
    {
        return $this->letter === $letter;
    }
}