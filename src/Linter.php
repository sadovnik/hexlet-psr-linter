<?php

namespace PsrLinter;

use PsrLinter\Checkers\CamelCaseChecker;
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

    public function __construct()
    {
        $parserFactory = new ParserFactory;
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser;
        $this->linterVisitor = new LinterVisitor(static::getCoreCheckers());
        $this->traverser->addVisitor($this->linterVisitor);
    }

    /**
     * Returns default checkers
     * @return array
     */
    protected static function getCoreCheckers() : array
    {
        return [
            new CamelCaseChecker
        ];
    }

    /**
     * @param string $code
     * @return array list of linting errors
     */
    public function lint(string $code)
    {
        $ast = $this->parser->parse($code);
        $this->traverser->traverse($ast);
        return $this->linterVisitor->getErrors();
    }
}
