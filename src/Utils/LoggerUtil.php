<?php

namespace App\Utils;

class LoggerUtil
{
    public function log(string $filePath, string $message)
    {
        file_put_contents($filePath, $message . PHP_EOL, FILE_APPEND);
    }
}
