<?php

use Hangman\GameStatus;
use Hangman\Hangman;

it('can start a game with a secret word and status becomes "playing"')
    ->expect(Hangman::play('game')->status())
    ->toBe(GameStatus::Playing);

it('does not allow to start a game with empty secret word', function () {
    Hangman::play('');
})->throws(InvalidArgumentException::class);

it('does not allow to start a game with less attempts than the word length', function () {
    Hangman::play(secretWord: 'win', attempts: 2);
})->throws(InvalidArgumentException::class);

it('masks the word if no letters were guessed')
    ->expect(Hangman::play('game')->word())
    ->toBe('****');

it('unmasks a letter when it is guessed correctly')
    ->expect(Hangman::play('game')->guessLetter('a')->word())
    ->toBe('*a**');

it('does not unmask any letters when a guess is not correct')
    ->expect(Hangman::play('game')->guessLetter('b')->word())
    ->toBe('****');

it('unmasks all instances of the same letter when guessed correctly')
    ->expect(Hangman::play('hangman')->guessLetter('a')->word())
    ->toBe('*a***a*');

it('increments attempts on each guess', function () {
    $hangman = Hangman::play(secretWord: 'game', attempts: 5);
    $hangman->guessLetter('g')->guessLetter('a')->guessLetter('c');

    expect($hangman)
        ->remainingAttempts()->toBe(2)
        ->guesses()->toBe(3);
});

it('sets status to lost if all attempts were used up before the word is guessed correctly', function () {
    $hangman = Hangman::play(secretWord: 'win', attempts: 3);
    $hangman->guessLetter('a')->guessLetter('b')->guessLetter('c');

    expect($hangman)->status()->toBe(GameStatus::Lost);
});

it('sets status to won if the word is guess before all attempts were used up', function () {
    $hangman = Hangman::play(secretWord: 'win', attempts: 3);
    $hangman->guessLetter('w')->guessLetter('i')->guessLetter('n');

    expect($hangman)->status()->toBe(GameStatus::Won);
});

it('unmasks the whole world when the game is won', function () {
    $hangman = Hangman::play(secretWord: 'win');
    $hangman->guessLetter('w')->guessLetter('i')->guessLetter('n');

    expect($hangman->word())->toBe('win');
});

it('does not allow to guess more than one letter', function () {
    Hangman::play('game')->guessLetter('ga');
})->throws(InvalidArgumentException::class);

it('requires a letter to guess', function () {
    Hangman::play('game')->guessLetter('');
})->throws(InvalidArgumentException::class);

it('does not allow to guess again when the game is won', function () {
    Hangman::play('a')->guessLetter('a')->guessLetter('a');
})->throws(RuntimeException::class);

it('does not allow to guess again when the game is lost', function () {
    Hangman::play(secretWord: 'a', attempts: 1)->guessLetter('b')->guessLetter('a');
})->throws(RuntimeException::class);