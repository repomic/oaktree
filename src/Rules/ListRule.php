<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;

class ListRule implements RuleInterface
{
    protected array $styleTypes = [
        'none' => 'none',
        'disc' => 'disc',
        'decimal' => 'decimal',
        'square' => 'square',
        'circle' => 'circle',
        'roman' => 'lower-roman',
        'alpha' => 'lower-alpha',
    ];

    protected array $positions = [
        'inside' => 'inside',
        'outside' => 'outside',
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        // 1. list-none, list-disc, list-decimal, list-roman, etc.
        if (preg_match('/^list-(none|disc|decimal|square|circle|roman|alpha)$/', $utility, $matches)) {
            $type = $matches[1];

            return [
                'list-style-type' => $this->styleTypes[$type],
            ];
        }

        // 2. list-inside, list-outside
        if (preg_match('/^list-(inside|outside)$/', $utility, $matches)) {
            $pos = $matches[1];

            return [
                'list-style-position' => $this->positions[$pos],
            ];
        }

        // 3. Arbitrary image bullet: list-[url('/bullet.svg')] or list-[square]
        if (preg_match('/^list-\[(.+)\]$/', $utility, $matches)) {
            $val = $matches[1];

            // Check if it's an image/url or direct value
            $prop = str_contains($val, 'url(') ? 'list-style-image' : 'list-style-type';

            return [
                $prop => $val,
            ];
        }

        // 4. Custom Component Presets targeting nested list elements
        if ($utility === 'list-reset') {
            return [
                'list-style' => 'none',
                'padding-left' => '0',
                'margin' => '0',
            ];
        }

        // Custom styled list component with target selectors for internal `li`
        if ($utility === 'list-spaced') {
            return [
                'list-style' => 'none',
                'padding-left' => '0',
                '& > li' => [
                    'margin-bottom' => '0.5rem',
                    'display' => 'flex',
                    'align-items' => 'center',
                    'gap' => '0.5rem',
                ],
                '& > li:last-child' => [
                    'margin-bottom' => '0',
                ],
            ];
        }

        // Custom custom-marker container (targets ::marker)
        if ($utility === 'list-accent') {
            return [
                'list-style-position' => 'inside',
                '& li::marker' => [
                    'color' => '#2563eb', // Blue-600
                    'font-weight' => '600',
                ],
            ];
        }

        return null;
    }
}
