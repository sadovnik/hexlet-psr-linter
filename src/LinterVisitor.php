<?php

namespace PsrLinter;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PsrLinter\Checkers\CamelCaseChecker;

class LinterVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $errors = [];

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

        $errors = array_reduce(
            $checkers,
            function ($acc, $checker) {
                $errors = $checker->getErrors();
                return array_merge($acc, $errors);
            },
            []
        );

        $this->errors = array_merge($this->errors, $errors);
    }
}
