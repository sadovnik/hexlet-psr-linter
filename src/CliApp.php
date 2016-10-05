<?php

namespace PsrLinter;

use PsrLinter\Linter;
use PsrLinter\Exceptions\ParseException;
use PsrLinter\Reporter;
use PsrLinter\RuleResults\AbstractFailRuleResult;
use PsrLinter\RuleResults\WarningRuleResult;
use PsrLinter\RuleResults\ErrorRuleResult;
use PsrLinter\RuleResults\OkRuleResult;
use PsrLinter\RuleResults\FixedRuleResult;

use PsrLinter\Rules\CamelCaseRule;
use PsrLinter\Rules\EitherDeclarationsOrSideEffectsRule;
use PsrLinter\Rules\RuleCollection;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;

use League\CLImate\CLImate;

use Fs\Fs;

class CliApp
{
    /**
     * @var League\CLImate\CLImate
     */
    private $cli;

    /**
     * @var PrettyPrinter\Standard|null
     */
    private $printer = null;

    /**
     * @return CLImate
     */
    protected function getCLi()
    {
        if ($this->cli === null) {
            $this->cli = new CLImate;
        }
        return $this->cli;
    }

    /**
     * @return PrettyPrinter\Standard
     */
    public function getPrinter()
    {
        if ($this->printer === null) {
            $this->printer = new PrettyPrinter\Standard;
        }
        return $this->printer;
    }

    /**
     * Returns default rules
     *
     * @return AbstractRule[]
     */
    public static function getCoreRules()
    {
        return [
            new CamelCaseRule,
            new EitherDeclarationsOrSideEffectsRule
        ];
    }

    /**
     * @return integer exit code
     */
    public function run($args)
    {
        $path = $args['<path>'];
        $debug = $args['--debug'];
        $fix = $args['--fix'];

        $hasErrors = Fs::isDir($path)
            ? $this->lintDirectory($path, $fix, $debug)
            : $this->lintFile($path, $fix, $debug);

        if ($hasErrors) {
            $this->getCli()->yellow('🐛 Found some errors.');
        } else {
            $this->getCli()->green('👍 Code is valid!');
        }

        return (int) $hasErrors;
    }

    /**
     * @param string $file
     * @param bool   $fix
     * @param bool   $debug
     *
     * @return bool true if has errors
     */
    private function lintFile($file, $fix, $debug)
    {
        $rules = new RuleCollection(self::getCoreRules());
        $linter = new Linter($rules, $fix, $debug);
        $code = Fs::read($file);

        try {
            $resultCollection = $linter->lint($code);
        } catch (Error $e) {
            $this->printUnableToParseMessage($file, $e->getMessage());
            return true;
        }

        if ($resultCollection->isEmpty()) {
            return false;
        }

        $this->getCli()->gray($file);

        $resultCollection->traverse(function ($result) {
            $this->printResult($result);
        });

        if (!$debug && $fix) {
            $code = $this->generatePhpCode($linter->getFixedAst());
            Fs::write($file, $code);
        }

        return $resultCollection->hasErrors();
    }

    /**
     * @param string $directory
     * @param bool   $fix
     * @param bool   $debug
     *
     * @return bool true if has errors
     */
    private function lintDirectory($directory, $debug, $fix)
    {
        $directoryIterator = new \RecursiveDirectoryIterator($directory);
        $iteratorIterator = new \RecursiveIteratorIterator($directoryIterator);
        $regexIterator = new \RegexIterator($iteratorIterator, '/\.php$/');

        $errorsOccured = array_reduce(
            iterator_to_array($regexIterator),
            function ($errorsOccured, $file) use ($debug, $fix) {
                return $this->lintFile($file, $debug, $fix) | $errorsOccured;
            },
            false
        );

        return $errorsOccured;
    }

    /**
     * @return string
     */
    private function generatePhpCode($nodes)
    {
        $printer = $this->getPrinter();
        $code = $printer->prettyPrint($nodes);
        return '<?php' . PHP_EOL . PHP_EOL . $code;
    }

    /**
     * @param AbstractRuleResult $result
     */
    private function printResult($result)
    {
        $lineCount = $result->getNode() ? $result->getNode()->getLine() : '';
        $line = sprintf('%6s', $lineCount);

        if ($result instanceof AbstractFailRuleResult) {
            $this->getCli()->inline($line . ' ');

            if ($result instanceof WarningRuleResult) {
                $this->getCli()->yellow()->inline('warning')->white();
            } else {
                $this->getCli()->red()->inline('error')->white();
            }

            $this->getCli()
                ->inline(' ')
                ->inline($result->getTitle())
                ->inline('    ')
                ->inline($result->getDescription())
                ->br()->br();
        }

        if ($result instanceof FixedRuleResult) {
            $this->getCli()
                ->inline($line . ' ')->green()
                ->inline('fixed')->white()
                ->inline(' ' . $result->getBeforeFix() . ' → ' . $result->getAfterFix())
                ->br()->br();
        }

        if ($result instanceof OkRuleResult) {
            $this->getCli()
                ->gray()->inline('ok: ')->white($result->getRule())
                ->br();
        }
    }

    /**
     * @param string $file
     * @param string $message
     */
    public function printUnableToParseMessage($file, $message)
    {
        $this->getCli()
            ->out($file)
            ->inline('       ')->red()
            ->inline('unable to parse ')->white()
            ->inline($message)
            ->br()->br();
    }
}
