<?php

namespace PsrLinter\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\ClassMethod;

use PsrLinter\RuleResults\AbstractRuleResult;

use function PsrLinter\Rules\isCamelCase;
use function PsrLinter\Rules\convertToCamelCase;

class CamelCaseRule extends AbstractRule implements RuleInterface, FixableRuleInterface
{
    const MAGIC_METHODS = [
        '__construct', '__destruct', '__call', '__callStatic',
        '__get', '__set', '__isset', '__unset', '__sleep',
        '__wakeup', '__toString', '__invoke', '__set_state',
        '__clone', '__debugInfo'
    ];


    /**
     * @inheritdoc
     */
    public function getNodeTypes()
    {
        return [
            Function_::class,
            ClassMethod::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function verify(Node $node) : AbstractRuleResult
    {
        if ($node instanceof ClassMethod && self::isMagic($node->name)) {
            return $this->ok($node);
        }

        $type = $node instanceof Function_
            ? 'function'
            : 'method';

        $isCamelCase = isCamelCase($node->name);

        if ($isCamelCase) {
            return $this->ok($node);
        }

        $title = "Wrong $type name: " . $node->name;
        $description = ucfirst($type) . ' names must be declared in camelCase.';

        return $this->warning($title, $description, $node);
    }

    /**
     * Checks whether $name is a magic method or not
     *
     * @see http://php.net/manual/en/language.oop5.magic.php
     * @param string $method
     * @return bool
     */
    private static function isMagic($method)
    {
        return in_array($method, self::MAGIC_METHODS);
    }

    /**
     * @param Node $node
     * @return Node
     */
    public function fix(Node $node)
    {
        $newNode = clone $node;
        $currentName = $newNode->name;
        $fixedName = convertToCamelCase($currentName);
        $newNode->name = $fixedName;
        return $this->fixed($fixedName, $currentName, $newNode, $node);
    }
}
