<?php

namespace Utils;

class Logger
{
    public static function log(string $message, string $file = 'logs/email.log'): void
    {
        $date     = date('Y-m-d H:i:s');
        $logEntry = "[$date] $message\n";

        // Ensure the directory exists
        $logDir = dirname($file);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Append log message to file
        file_put_contents($file, $logEntry, FILE_APPEND);
    }
}
