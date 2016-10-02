<?php

namespace PsrLinter\RuleResults;

use Closure;

class ResultCollection
{
    /**
     * @var array
     */
    private $results = [];

    /**
     * @param AbstractRuleResult $result
     */
    public function add(AbstractRuleResult $result)
    {
        array_push($this->results, $result);
    }

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->results);
    }

    /**
     * @return bool
     */
    public function hasErrors() : bool
    {
        foreach ($this->results as $result) {
            if ($result instanceof AbstractFailRuleResult) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Closure $closure
     */
    public function traverse(Closure $closure)
    {
        array_walk(
            $this->results,
            $closure
        );
    }
}
