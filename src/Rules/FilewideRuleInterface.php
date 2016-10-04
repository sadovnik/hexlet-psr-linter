<?php

namespace PsrLinter\Rules;

use PhpParser\Node;

/**
 * Represents a file-wide rule.
 *
 * By implementing this interface the rule will be notified that the last node were traversed.
 */
interface FilewideRuleInterface
{
    /**
     * This method will be called when the last node will be traversed.
     */
    public function conclude();
}
