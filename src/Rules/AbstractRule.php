<?php

namespace PsrLinter\Rules;

use PhpParser\Node;

use PsrLinter\Reporter;
use PsrLinter\RuleResults\ErrorRuleResult;
use PsrLinter\RuleResults\WarningRuleResult;
use PsrLinter\RuleResults\FixedRuleResult;
use PsrLinter\RuleResults\OkRuleResult;

abstract class AbstractRule
{
    /**
     * @param Node|null current node
     *
     * @return OkRuleResult
     */
    protected function ok($node = null)
    {
        return new OkRuleResult(static::class, $node);
    }

    /**
     * @param string $title
     * @param string|null $description
     * @param Node|null $node current node
     *
     * @return WarningRuleResult
     */
    protected function warning($title, $description, $node = null)
    {
        return new WarningRuleResult($title, $description, static::class, $node);
    }

    /**
     * @param string $title
     * @param string|null $description
     * @param Node|null $node current node
     *
     * @return ErrorRuleResult
     */
    protected function error($title, $description, $node = null)
    {
        return new ErrorRuleResult($title, $description, static::class, $node);
    }

    /**
     * @param string $afterFix a fixed value. Null means node was deleted
     * @param string $beforeFix a value of an attribute to be fixed
     * @param null|Node|false|Node[] $newNode replacement for current node
     * @param Node|null $node current node
     *
     * @return FixedRuleResult
     */
    protected function fixed($afterFix, $beforeFix, $newNode, $node = null)
    {
        return new FixedRuleResult($afterFix, $beforeFix, $newNode, static::class, $node);
    }
}
