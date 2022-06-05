<?php

namespace TeyvatPS\utils;

class Logger
{

    public const BOLD = "\x1b[1m";
    public const ITALIC = "\x1b[3m";
    public const UNDERLINE = "\x1b[4m";
    public const STRIKETHROUGH = "\x1b[9m";

    public const RESET = "\x1b[m";

    public const BLACK = "\x1b[38;5;16m";
    public const DARK_BLUE = "\x1b[38;5;19m";
    public const DARK_GREEN = "\x1b[38;5;34m";
    public const DARK_AQUA = "\x1b[38;5;37m";
    public const DARK_RED = "\x1b[38;5;124m";
    public const PURPLE = "\x1b[38;5;127m";
    public const GOLD = "\x1b[38;5;214m";
    public const GRAY = "\x1b[38;5;145m";
    public const DARK_GRAY = "\x1b[38;5;59m";
    public const BLUE = "\x1b[38;5;63m";
    public const GREEN = "\x1b[38;5;83m";
    public const AQUA = "\x1b[38;5;87m";
    public const RED = "\x1b[38;5;203m";
    public const LIGHT_PURPLE = "\x1b[38;5;207m";
    public const YELLOW = "\x1b[38;5;227m";
    public const WHITE = "\x1b[38;5;231m";
    public const MINECOIN_GOLD = "\x1b[38;5;184m";


    public static function send(string $content, string $header): void
    {
        echo $header . " " . self::RESET . $content . self::RESET . PHP_EOL;
    }

    public static function log(string $content): void
    {
        self::send($content, self::GREEN . "[LOG]");
    }

    public static function notice(string $content): void
    {
        self::send($content, self::YELLOW . "[NOTICE]");
    }

    public static function error(string $content): void
    {
        self::send($content, self::RED . "[ERROR]");
    }
}