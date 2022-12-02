<?php

namespace Hangman;

enum GameStatus
{
    case Playing;
    case Won;
    case Lost;
}
