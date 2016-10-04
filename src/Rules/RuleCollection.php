<?php

namespace PsrLinter\Rules;

use PhpParser\Node;

class RuleCollection
{
    /**
     * @var AbstractRule[]
     */
    private $rules;

    /**
     * @var array
     */
    private $map = null;

    /**
     * @param AbstractRule[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param Node $node
     */
    public function getAssociatedRules(Node $node)
    {
        foreach ($this->getNodeTypeMap() as $nodeType => $rules) {
            if ($node instanceof $nodeType) {
                return $rules;
            }
        }
        return [];
    }

    /**
     * @return AbstractRule[]
     */
    protected function getNodeTypeMap()
    {
        if ($this->map === null) {
            $this->map = [];
            foreach ($this->rules as $rule) {
                $nodeTypes = $rule->getNodeTypes();
                foreach ($nodeTypes as $nodeType) {
                    if (!array_key_exists($nodeType, $this->map)) {
                        $this->map[$nodeType] = [];
                    }
                    array_push($this->map[$nodeType], $rule);
                }
            }
        }

        return $this->map;
    }

    /**
     * @return AbstractRule[]
     */
    public function getFilewideRules()
    {
        return array_filter(
            $this->rules,
            function ($rule) {
                return $rule instanceof FilewideRuleInterface;
            }
        );
    }
}
