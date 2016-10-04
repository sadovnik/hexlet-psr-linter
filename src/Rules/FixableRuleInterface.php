<?php

namespace PsrLinter\Rules;

use PhpParser\Node;

/**
 * Represents a fixable rule.
 *
 * By implementing this interface the rule will be asked to fix the node that
 * was verified as unvalid.
 */
interface FixableRuleInterface
{
    /**
     * Fixes given node
     *
     * @param  Node $node
     * @return Node|false
     */
    public function fix(Node $node);
}
