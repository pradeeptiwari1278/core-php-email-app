<?php

namespace Utils;

class EnvLoader
{
    public static function load(string $path): void
    {
        if (!file_exists($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;

            [$key, $value] = explode('=', $line, 2);
            $key           = trim($key);
            $value         = trim($value, " \t\n\r\0\x0B\"'");

            $_ENV[$key] = $value;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}
