<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;

class InteractivityRule implements RuleInterface
{
    protected array $static = [
        'appearance-none' => ['appearance' => 'none', '-webkit-appearance' => 'none'],

        'pointer-events-none' => ['pointer-events' => 'none'],
        'pointer-events-auto' => ['pointer-events' => 'auto'],

        'select-none' => ['user-select' => 'none'],
        'select-text' => ['user-select' => 'text'],
        'select-all' => ['user-select' => 'all'],
        'select-auto' => ['user-select' => 'auto'],

        'scroll-auto' => ['scroll-behavior' => 'auto'],
        'scroll-smooth' => ['scroll-behavior' => 'smooth'],

        'resize-none' => ['resize' => 'none'],
        'resize-x' => ['resize' => 'horizontal'],
        'resize-y' => ['resize' => 'vertical'],
        'resize' => ['resize' => 'both'],

        'touch-auto' => ['touch-action' => 'auto'],
        'touch-none' => ['touch-action' => 'none'],
        'touch-pan-x' => ['touch-action' => 'pan-x'],
        'touch-pan-y' => ['touch-action' => 'pan-y'],
        'touch-pan-left' => ['touch-action' => 'pan-left'],
        'touch-pan-right' => ['touch-action' => 'pan-right'],
        'touch-pan-up' => ['touch-action' => 'pan-up'],
        'touch-pan-down' => ['touch-action' => 'pan-down'],
        'touch-pinch-zoom' => ['touch-action' => 'pinch-zoom'],
        'touch-manipulation' => ['touch-action' => 'manipulation'],
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        if (preg_match('/^cursor-\[(.+)\]$/', $utility, $matches)) {
            return ['cursor' => $matches[1]];
        }

        if (preg_match('/^cursor-(.+)$/', $utility, $matches)) {
            return ['cursor' => $matches[1]];
        }

        if (preg_match('/^appearance-(.+)$/', $utility, $matches)) {
            return ['appearance' => $matches[1], '-webkit-appearance' => $matches[1]];
        }

        if (preg_match('/^pointer-events-(.+)$/', $utility, $matches)) {
            return ['pointer-events' => $matches[1]];
        }

        if (preg_match('/^select-(.+)$/', $utility, $matches)) {
            return ['user-select' => $matches[1]];
        }

        if (preg_match('/^scroll-(.+)$/', $utility, $matches)) {
            return ['scroll-behavior' => $matches[1]];
        }

        if (preg_match('/^resize-(.+)$/', $utility, $matches)) {
            $resize = match ($matches[1]) {
                'x' => 'horizontal',
                'y' => 'vertical',
                default => $matches[1],
            };

            return ['resize' => $resize];
        }

        if (preg_match('/^touch-(.+)$/', $utility, $matches)) {
            return ['touch-action' => $matches[1]];
        }

        return null;
    }
}
