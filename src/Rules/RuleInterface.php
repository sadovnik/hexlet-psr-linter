<?php

namespace PsrLinter\Rules;

use PhpParser\Node;

use PsrLinter\RuleResults\AbstractRuleResult;

/**
 * Represents a linting rule.
 *
 * @see AbstractRule for __construct interface
 */
interface RuleInterface
{
    /**
     * Specifies what node types this rule interested in.
     * If there's no types specified this rule will receive every node.
     *
     * @return array of node types
     */
    public function getNodeTypes();

    /**
     * Verifies node.
     *
     * @param  Node $node
     * @return bool false if invalid
     */
    public function verify(Node $node);
}
