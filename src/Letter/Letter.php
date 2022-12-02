<?php

namespace Hangman\Letter;

use Stringable;

interface Letter extends Stringable
{
    public function is(string $letter): bool;
}