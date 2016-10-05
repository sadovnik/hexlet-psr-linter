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
    private $hasPossibleSideEffects = false;

    /**
     * @var bool
     */
    private $hasDeclarations = false;

    /**
     * @var Node\Stmt\Class_|Node\Stmt\Function_|null
     */
    private $context = null;

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
        if ($node instanceof Node\Stmt\Class_
            || $node instanceof Node\Stmt\Function_
            || $node instanceof Node\Stmt\Trait_
            || $node instanceof Node\Expr\Closure
        ) {
            $this->hasDeclarations = true;
            $this->hasPossibleSideEffects = false;
            return;
        }

        if ($node instanceof Node\Stmt) {
            if ($node instanceof Node\Stmt\Echo_) {
                $this->hasSideEffects = true;
            } else {
                $this->hasDeclarations = true;
            }
        } else {
            if ($node instanceof Node\Expr\Eval_
                || $node instanceof Node\Expr\Exit_
                || $node instanceof Node\Expr\Include_
            ) {
                $this->hasSideEffects = true;
            } else {
                $this->hasPossibleSideEffects = true;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function conclude()
    {
        if (!$this->hasSideEffects && !$this->hasPossibleSideEffects && !$this->hasDeclarations) {
            return $this->ok();
        }

        return ($this->hasSideEffects || $this->hasPossibleSideEffects) ^ $this->hasDeclarations
            ? $this->ok()
            : $this->error(
                'Side effects and declarations are mixed.',
                'A file SHOULD declare new symbols (classes, functions, constants, etc.) ' .
                'and cause no other side effects, or it SHOULD execute logic with side effects, ' .
                'but SHOULD NOT do both.'
            );
    }
}
