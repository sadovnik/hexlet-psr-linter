<?php

namespace PsrLinter\Cli;

use PsrLinter\Linter;
use PsrLinter\Cli\Io;
use PsrLinter\Cli\ReportGenerator;
use PhpParser\Error;

class App
{
    private $args;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function run()
    {
        $linter = new Linter;
        $path = $this->args['<path>'];
        $code = Io::read($path);

        try {
            $errors = $linter->lint($code);
        } catch (Error $e) {
            $message = 'Unable to parse the sourcecode. Message: ' . $e->getMessage() . PHP_EOL;
            $code = 1;
            $this->exit($message, $code);
        }

        if (empty($errors)) {
            $this->exit('Code is valid!' . PHP_EOL, 0);
        } else {
            $message = '🐛  Code is not valid. Errors: ';
            $report = ReportGenerator::generate($errors);
            $this->exit($message . PHP_EOL . $report, 1);
        }
    }

    private function exit($message, $code)
    {
        echo $message;
        exit($code);
    }
}

