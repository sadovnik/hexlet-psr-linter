<?php

namespace PsrLinter;

use PhpParser\Error as ErrorException;
use PhpParser\ParserFactory;

use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;


class Linter
{
    public static function factory(array $config = []) : Linter
    {
        // TODO: some serious bussiness here
        return new self;
    }

    /**
     * @param string $code
     * @return mixed true – no linting error were found
     *               array – list of linting errors
     * @throws PhpParser\Error
     */
    public function lint(string $code)
    {
        $parserFactory = new ParserFactory;
        $parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser;
        $linterVisitor = new LinterVisitor;
        $traverser->addVisitor($linterVisitor);
        $ast = $parser->parse($code);
        $traverser->traverse($ast);

        $errors = $linterVisitor->getErrors();

        if ($errors) {
            return $errors;
        }

        return true;
    }
}
