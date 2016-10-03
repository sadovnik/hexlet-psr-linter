<?php

namespace PsrLinter;

use PsrLinter\Checkers\CamelCaseChecker;
use PsrLinter\RuleResults\ResultCollection;

use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;

class Linter
{
    /**
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * @var PhpParser\Parser
     */
    private $parser;

    /**
     * @var LinterVisitor
     */
    private $linterVisitor;

    /**
     * @var AbstractRule[]
     */
    private $rules;

    /**
     * @var Node[]
     */
    private $fixedAst = [];

    /**
     * @param AbstractRule[] $rules
     * @param bool           $fix
     * @param book           $debug
     */
    public function __construct($rules = [], $fix = false, $debug = false)
    {
        $parserFactory = new ParserFactory;
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser;
        $this->linterVisitor = new LinterVisitor($rules, $fix, $debug);
        $this->traverser->addVisitor($this->linterVisitor);
    }

    /**
     * @param string $code
     * @param bool   $fix
     *
     * @return ResultCollection of linting errors
     */
    public function lint(string $code) : ResultCollection
    {
        $ast = $this->parser->parse($code);
        $this->fixedAst = $this->traverser->traverse($ast);
        return $this->linterVisitor->getCollection();
    }

    /**
     * @return string
     */
    public function getFixedAst()
    {
        return $this->fixedAst;
    }
}
