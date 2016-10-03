<?php

namespace PsrLinter\Rules;

use PhpParser\Node;

class EitherDeclarationsOrSideEffectsRule extends AbstractRule implements RuleInterface, FilewideRuleInterface
{
    /**
     * @var bool
     */
    private $hasSideEffects = false;

    /**
     * @var bool
     */
    private $hasDeclarations = false;

    /**
     * @inheritdoc
     */
    public function getNodeTypes()
    {
        return [
            Node\Expr::class,
            Node\Stmt::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function verify(Node $node)
    {
        $isStatement = $node instanceof Node\Stmt;
        $isEchoStatement = $node instanceof Node\Stmt\Echo_;
        $isExpression = $node instanceof Node\Expr;

        if ($isStatement && !$isEchoStatement) {
            $this->hasDeclarations |= true;
        }

        if ($isEchoStatement || $isEchoStatement) {
            $this->hasSideEffects |= true;
        }
    }

    public function finally()
    {
        if (!$this->hasSideEffects && !$this->hasDeclarations) {
            return $this->ok();
        }

        return $this->hasSideEffects ^ $this->hasDeclarations
            ? $this->ok()
            : $this->error(
                'Side effects and declarations are mixed.',
                'A file SHOULD declare new symbols (classes, functions, constants, etc.) ' .
                'and cause no other side effects, or it SHOULD execute logic with side effects, ' .
                'but SHOULD NOT do both.'
            );
    }
}
