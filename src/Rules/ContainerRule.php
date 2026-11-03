<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Css;

class ContainerRule implements RuleInterface
{
    public function handle(string $utility, Compiler $compiler): array|Css|null
    {
        $breakpoints = $compiler->breakpoints();

        if (preg_match('/^container-(' . implode('|', array_keys($breakpoints)) . '|fluid)$/', $utility, $matches)) {
            $default = ['width' => '100%', 'max-width' => '100%'];

            if (isset($breakpoints[$matches[1]])) {
                $default['max-width'] = $breakpoints[$matches[1]] . 'px';
            }

            return $default;
        }

        return null;
    }
}
