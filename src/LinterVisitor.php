<?php

namespace PsrLinter;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PsrLinter\Checkers\CamelCaseChecker;

class LinterVisitor extends NodeVisitorAbstract
{
    private $errors;

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        $checkers = [ new CamelCaseChecker ];

        array_map(
            function ($checker) use ($node) {
                $checker->check($node);
            },
            $checkers
        );

        $this->errors = array_reduce(
            $checkers,
            function ($acc, $checker) use ($node) {
                $errors = $checker->getErrors();
                return array_merge($acc, $errors);
            },
            []
        );
    }
}
