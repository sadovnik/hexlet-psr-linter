<?php

namespace PsrLinter;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

use PsrLinter\Rules\FixableRuleInterface;
use PsrLinter\Rules\FilewideRuleInterface;
use PsrLinter\Rules\RuleCollection;

use PsrLinter\RuleResults\WarningRuleResult;
use PsrLinter\RuleResults\AbstractRuleResult;
use PsrLinter\RuleResults\AbstractFailRuleResult;
use PsrLinter\RuleResults\OkRuleResult;
use PsrLinter\RuleResults\FixedRuleResult;
use PsrLinter\RuleResults\ResultCollection;

class LinterVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $nodeTypeToRulesMap = [];

    /**
     * @var Node
     */
    private $currentNode;

    /**
     * @var bool
     */
    private $fix;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var ResultCollection
     */
    private $collection;

    /**
     * @var RuleCollection
     */
    private $rules;

    /**
     * @param RuleCollection $rules
     * @param bool           $fix   whether try to fix nodes or not
     * @param bool           $debug whether collect verbose or not
     */
    public function __construct($rules, $fix, $debug)
    {
        $this->fix = $fix;
        $this->debug = $debug;
        $this->rules = $rules;
        $this->collection = new ResultCollection;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        $this->currentNode = $node;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        foreach ($this->rules->getAssociatedRules($node) as $rule) {
            $result = $rule->verify($node);

            if (!($result instanceof AbstractRuleResult)) {
                continue;
            }

            if ($result instanceof OkRuleResult) {
                if ($this->debug) {
                    $this->collect($result);
                }
                continue;
            }

            if ($this->fix
                && $result instanceof WarningRuleResult
                && $rule instanceof FixableRuleInterface
            ) {
                $fixResult = $rule->fix($node);

                if ($fixResult instanceof FixedRuleResult) {
                    $this->collect($fixResult);
                    return $fixResult->getFixedNode();
                }
            }

            $this->collect($result);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterTraverse(array $nodes)
    {
        $fileWideRules = $this->rules->getFilewideRules();
        array_walk(
            $fileWideRules,
            function ($rule) {
                $result = $rule->conclude();
                if ($result instanceof AbstractFailRuleResult
                    || $result instanceof OkRuleResult && $this->debug
                ) {
                    $this->collect($result);
                }
            }
        );
    }

    /**
     * @param AbstractRuleResult $result
     */
    private function collect(AbstractRuleResult $result)
    {
        $this->collection->add($result);
    }

    /**
     * @return array
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
