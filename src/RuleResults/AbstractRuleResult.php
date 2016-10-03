<?php

namespace PsrLinter\RuleResults;

use PhpParser\Node;

/**
 * Represents an abstract rule result.
 */
abstract class AbstractRuleResult
{
    /**
     * @var string
     */
    private $rule;

    /**
     * @var Node|null
     */
    private $node;

    /**
     * @param string $rule
     * @param Node   $node
     */
    public function __construct($rule, Node $node = null)
    {
        $this->rule = $rule;
        $this->node = $node;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return Node|null
     */
    public function getNode()
    {
        return $this->node;
    }
}
