<?php

namespace PsrLinter;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class LinterVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $errors = [];

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
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        array_map(
            function ($checker) use ($node) {
                $checker->check($node);
            },
            $this->checkers
        );

        $errors = array_reduce(
            $this->checkers,
            function ($acc, $checker) {
                $errors = $checker->getErrors();
                return array_merge($acc, $errors);
            },
            []
        );

        $this->errors = array_merge($this->errors, $errors);
    }
}
