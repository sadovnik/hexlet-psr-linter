<?php

namespace PsrLinter;

use PhpParser\Node;

interface CheckerInterface
{
    /**
     * @param Node $node
     */
    public function check(Node $node);

    /**
     * @return array of accumulated erorrs
     */
    public function getErrors() : array;
}
