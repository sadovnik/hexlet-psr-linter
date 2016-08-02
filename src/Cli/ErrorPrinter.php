<?php

namespace PsrLinter\Cli;

class ErrorPrinter
{
    public static function print(array $errors)
    {
        foreach ($errors as $error) {
            list ($line, $title, $description) = $error;
            echo "Line #$line: $title $description" . PHP_EOL;
        }
    }
}
