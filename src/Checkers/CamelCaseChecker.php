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
        $type = null;
        $name = null;

        switch (true) {
            case $node instanceof Node\Stmt\Function_:
                $type = 'function';
                $name = $node->name;
                break;

            case $node instanceof Node\Stmt\ClassMethod:
                $type = 'method';
                $name = $node->name;
                break;

            case $node instanceof Node\Expr\Assign:
                if ($node->expr instanceof Node\Expr\Closure) {
                    $type = 'callable';
                    $name = $node->var->name;
                    break;
                }
                // fall-through if assign is not a closure

            default:
                return;
        }

        if (!self::isCamelCase($name)) {
            $line = $node->getLine();
            $title = "Wrong $type name.";
            $description = ucfirst($type) . ' names must be declared in camelCase.';
            array_push($this->errors, compact('line', 'title', 'description'));
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
