<?php

namespace PsrLinter\RuleResults;

use PhpParser\Node;

/**
 * Represents a fail rule result.
 */
abstract class AbstractFailRuleResult extends AbstractRuleResult
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $title
     * @param string $description
     * @param string $rule
     * @param Node $node
     */
    public function __construct($title, $description, $rule, Node $node = null)
    {
        $this->title = $title;
        $this->description = $description;
        parent::__construct($rule, $node);
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }
}
