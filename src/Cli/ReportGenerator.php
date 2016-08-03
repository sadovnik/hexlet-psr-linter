<?php

namespace PsrLinter\Cli;

class ReportGenerator
{
    /**
     * @param array $errors
     * @return string
     */
    public static function generate(array $errors) : string
    {
        return array_reduce(
            $errors,
            function ($acc, $error) {
                extract($error);
                return $acc . "Line #$line: $title $description" . PHP_EOL;
            },
            ''
        );
    }
}
