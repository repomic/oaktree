<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Css;

interface RuleInterface
{
    /**
     * Should return an array containing all css
     * e.g., [--lc-scale-x' => '0.9', 'transform' => 'var(...)']
     */
    public function handle(string $utility, Compiler $compiler): array|Css|null;
}
