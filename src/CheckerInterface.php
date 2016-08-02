<?php

namespace PsrLinter;

use PhpParser\Node;

interface CheckerInterface
{
    /**
     * Makes check
     * @param Node $node
     */
    public function check(Node $node);

    /**
     * Makes check
     * @return array of accumulated erorrs
     */
    public function getErrors() : array;
}
