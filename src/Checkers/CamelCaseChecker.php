<?php

namespace PsrLinter\Checkers;

use PsrLinter\CheckerInterface;
use PhpParser\Node;

class CamelCaseChecker implements CheckerInterface
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @inheritdoc
     */
    public function check(Node $node)
    {
        $functionName = null;

        switch (true) {
            case $node instanceof Node\Stmt\Function_:
            case $node instanceof Node\Stmt\ClassMethod:
                $functionName = $node->name;
                break;

            case $node instanceof Node\Expr\Assign:
                if ($node->expr instanceof Node\Expr\Closure) {
                    $functionName = $node->var->name;
                    break;
                }

            default:
                return;
        }

        if (!self::isCamelCase($functionName)) {
            $title = 'Wrong function name.';
            $description = 'Function names must be declared as camelCase.';
            $error = [
                $node->getLine(),
                $title,
                $description
            ];
            array_push($this->errors, $error);
        }
    }

    /**
     * @inheritdoc
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @param string $funtionName to check
     * @return bool
     */
    public static function isCamelCase(string $functionName)
    {
        $legalFirstChar = '[a-z]';

        if (preg_match("/^$legalFirstChar/", $functionName) === 0) {
            return false;
        }

        $legalChars = 'a-zA-Z0-9';

        if (preg_match("|[^$legalChars]|", substr($functionName, 1)) > 0) {
            return false;
        }

        $chars = str_split($functionName);
        $lastCharWasCaps = false;

        foreach ($chars as $char) {
            $ascii = ord($char);
            if ($ascii >= 48 && $ascii <= 57) {
                $isCaps = false;
            } else {
                if (strtoupper($char) === $char) {
                    $isCaps = true;
                } else {
                    $isCaps = false;
                }
            }

            if ($isCaps === true && $lastCharWasCaps === true) {
                return false;
            }

            $lastCharWasCaps = $isCaps;
        }

        return true;
    }
}
