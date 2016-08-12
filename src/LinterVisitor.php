<?php

namespace PsrLinter;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class LinterVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $checkers = [];

    /**
     * @param array $checkers
     */
    public function __construct(array $checkers = [])
    {
        $this->checkers = $checkers;
    }

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
        return array_reduce(
            $this->checkers,
            function ($acc, $checker) {
                $errors = $checker->getErrors();
                return array_merge($acc, $errors);
            },
            []
        );
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        array_walk(
            $this->checkers,
            function ($checker) use ($node) {
                $checker->check($node);
            }
        );
    }
}
