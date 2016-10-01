<?php

namespace PsrLinter\RuleResults;

use PhpParser\Node;

/**
 * Represents a successful fix of a node.
 */
class FixedRuleResult extends AbstractRuleResult
{
    /**
     * @var null|Node|false|Node[] Node
     */
    private $fixedNode;

    /**
     * @var string
     */
    private $beforeFix;

    /**
     * @var string|null
     */
    private $afterFix;

    /**
     * @return null|Node|false|Node[] Node
     */
    public function getFixedNode()
    {
        return $this->fixedNode;
    }

    /**
     * @return string
     */
    public function getBeforeFix()
    {
        return $this->beforeFix;
    }

    /**
     * @return string|null
     */
    public function getAfterFix()
    {
        return $this->afterFix;
    }

    /**
     * @param mixed afterFix
     * @param mixed beforeFix
     * @param null|Node|false|Node[] $fixedNode
     * The semantics is exacly like the NodeVisitor::leaveNode return value:
     *  * null:      $node stays as-is
     *  * false:     $node is removed from the parent array
     *  * array:     The return value is merged into the parent array (at the position of the $node)
     *  * otherwise: $node is set to the return value
     *
     * @param string $rule
     * @param Node $node the previos node
     */
    public function __construct($afterFix, $beforeFix, $fixedNode, $rule, Node $node = null)
    {
        $this->beforeFix = $beforeFix;
        $this->afterFix = $afterFix;
        $this->fixedNode = $fixedNode;

        parent::__construct($rule, $node);
    }
}
