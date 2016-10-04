<?php

namespace PsrLinter\Rules;

/**
 * @param string $funtionName to check
 * @return bool
 */
function isCamelCase(string $functionName) : bool
{
    $legalFirstChar = '[a-z]';

    if (preg_match("/^$legalFirstChar/", $functionName) === 0) {
        return false;
    }

    $legalChars = 'a-zA-Z0-9';

    if (preg_match("|[^$legalChars]|", substr($functionName, 1)) > 0) {
        return false;
    }

    $chars = str_split($functionName);
    $lastCharWasCaps = false;

    foreach ($chars as $char) {
        $ascii = ord($char);
        if ($ascii >= 48 && $ascii <= 57) {
            $isCaps = false;
        } else {
            $isCaps = strtoupper($char) === $char;
        }

        if ($isCaps === true && $lastCharWasCaps === true) {
            return false;
        }

        $lastCharWasCaps = $isCaps;
    }

    return true;
}

/**
 * @param string $functionName
 * @return string
 */
function convertToCamelCase($functionName)
{
    $trimmedName = trim($functionName, '_');
    $explodedName = explode('_', $trimmedName);
    $filteredPartials = array_filter(
        $explodedName,
        function ($partial) {
            return $partial !== '';
        }
    );
    $firstPart = array_shift($filteredPartials);
    $camelPart = array_reduce(
        $filteredPartials,
        function ($acc, $partial) {
            return $acc . ucfirst($partial);
        },
        ''
    );
    return $firstPart . $camelPart;
}
