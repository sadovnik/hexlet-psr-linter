<?php

namespace PsrLinter;

use PsrLinter\Linter;
use PsrLinter\Exceptions\ParseException;
use PsrLinter\Reporter;
use PsrLinter\Rules;
use PsrLinter\RuleResults\AbstractFailRuleResult;
use PsrLinter\RuleResults\WarningRuleResult;
use PsrLinter\RuleResults\ErrorRuleResult;
use PsrLinter\RuleResults\OkRuleResult;
use PsrLinter\RuleResults\FixedRuleResult;

use PsrLinter\Rules\CamelCaseRule;
use PsrLinter\Rules\EitherDeclarationsOrSideEffectsRule;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter\Standard;

use League\CLImate\CLImate;

use Fs\Fs;

class CliApp
{
    /**
     * @var array
     */
    private $args;

    /**
     * @var League\CLImate\CLImate
     */
    private $cli;

    /**
     * @var PrettyPrinter\Standard|null
     */
    private $printer = null;

    /**
     * @param array $args
     */
    public function __construct($args)
    {
        $this->args = $args;
        $this->cli = new CLImate;
    }

    /**
     * @return PrettyPrinter\Standard
     */
    public function getPrinter()
    {
        if ($this->printer === null) {
            $this->printer = new Standard;
        }
        return $this->printer;
    }

    /**
     * Returns default rules
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
    public function run()
    {
        $path = $this->args['<path>'];
        $debug = $this->args['--debug'];
        $fix = $this->args['--fix'];

        $hasErrors = Fs::isDir($path)
            ? $this->lintDirectory($path, $fix, $debug)
            : $this->lintFile($path, $fix, $debug);

        if ($hasErrors) {
            $this->cli->yellow('🐛 Found some errors.');
        } else {
            $this->cli->green('👍 Code is valid!');
        }

        return (int) $hasErrors;
    }

    /**
     * @param string $file
     * @param bool $fix
     * @param bool $debug
     *
     * @return bool true if has errors
     */
    private function lintFile($file, $fix, $debug)
    {
        $linter = new Linter(self::getCoreRules(), $fix, $debug);
        $code = Fs::read($file);

        try {
            $results = $linter->lint($code);
        } catch (Error $e) {
            $this->printUnableToParseMessage($file, $e->getMessage());
            return true;
        }

        if (empty($results)) {
            return false;
        }

        $this->cli->gray($file);

        $errorsOccured = $this->processResults($results);

        if (!$debug && $fix) {
            $code = $this->generatePhpCode($linter->getFixedAst());
            Fs::write($file, $code);
        }

        return $errorsOccured;
    }

    /**
     * @param string $directory
     * @param bool $fix
     * @param bool $debug
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
     * @param AbstractRuleResult[] $results
     * @return bool errors occured
     */
    private function processResults($results)
    {
        $errorsOccured = array_reduce(
            $results,
            function ($errorsOccured, $result) {
                $line = $result->getNode()
                    ? sprintf('%6s', $result->getNode()->getLine())
                    : '';

                if ($result instanceof AbstractFailRuleResult) {
                    $this->cli->inline($line . ' ');

                    if ($result instanceof WarningRuleResult) {
                        $this->cli->yellow()->inline('warning')->white();
                    } else {
                        $this->cli->red()->inline('error')->white();
                    }

                    $this->cli
                        ->inline(' ')
                        ->inline($result->getTitle())
                        ->inline('    ')
                        ->inline($result->getDescription())
                        ->br()
                        ->br();

                    return true;
                }

                if ($result instanceof FixedRuleResult) {
                    $this->cli
                        ->inline($line . ' ')->green()
                        ->inline('fixed')->white()
                        ->inline(' ' . $result->getBeforeFix() . ' → ' . $result->getAfterFix())
                        ->br()
                        ->br();
                }

                if ($result instanceof OkRuleResult) {
                    $this->cli
                        ->gray()->inline('ok: ')->white($result->getRule())
                        ->br();
                }

                return $errorsOccured;
            },
            false
        );

        return $errorsOccured;
    }

    /**
     * @param string $file
     */
    public function printUnableToParseMessage($file, $message)
    {
        $this->cli
            ->out($file)->red()
            ->inline(' unable to parse ')->white()
            ->inline($message)
            ->br()->br();
    }
}
