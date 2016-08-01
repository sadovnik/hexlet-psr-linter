<?php

namespace PsrLinter;

use PhpParser\Error;
use PhpParser\ParserFactory;

class Linter
{
    public static function factory(array $config = []) : Linter
    {
        // TODO: some serious bussiness here
        return new self;
    }

    /**
     * @param string $code
     * @return boolean is code valid
     */
    public function lint(string $code) : bool
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $nodes = $parser->parse($code);
            return true;
        } catch (Error $e) {
            return false;
        }
    }
}

