<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;

class SpacingRule implements RuleInterface
{
    /**
     * Fractional width mappings
     */
    protected array $static = [
        'w-auto' => ['width' => 'auto'],
        'h-auto' => ['height' => 'auto'],
        'w-min' => ['width' => 'min-content'],
        'w-max' => ['width' => 'max-content'],
        'h-min' => ['height' => 'min-content'],
        'h-max' => ['height' => 'max-content'],
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        // 1. Try resolving via standard Ruleset matching first
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        // 2. Check Auto Margins: mx-auto, my-auto, mt-auto, etc.
        if (preg_match('/^m(x|y|t|b|l|r)?-auto$/', $utility, $matches)) {
            $dir = $matches[1] ?? '';

            return match ($dir) {
                'x' => ['margin-left' => 'auto', 'margin-right' => 'auto'],
                'y' => ['margin-top' => 'auto', 'margin-bottom' => 'auto'],
                't' => ['margin-top' => 'auto'],
                'b' => ['margin-bottom' => 'auto'],
                'l' => ['margin-left' => 'auto'],
                'r' => ['margin-right' => 'auto'],
                '' => ['margin' => 'auto'],
                default => null,
            };
        }

        // 3. Check Negative Margins: -mt-4, -mx-2, -m-8
        if (preg_match('/^-m(x|y|t|b|l|r)?-(\d+)$/', $utility, $matches)) {
            $dir = $matches[1] ?? '';
            $rem = '-' . ($matches[2] * 0.25) . 'rem';

            return match ($dir) {
                'x' => ['margin-left' => $rem, 'margin-right' => $rem],
                'y' => ['margin-top' => $rem, 'margin-bottom' => $rem],
                't' => ['margin-top' => $rem],
                'b' => ['margin-bottom' => $rem],
                'l' => ['margin-left' => $rem],
                'r' => ['margin-right' => $rem],
                '' => ['margin' => $rem],
                default => null,
            };
        }

        // 3. Check Positive Margins: -mt-4, -mx-2, -m-8
        if (preg_match('/^m(x|y|t|b|l|r)?-(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
            $dir = $matches[1] ?? '';

            $value = $matches[2] === '' ? ($matches[3] * 0.25) . 'rem' : $matches[2];

            return match ($dir) {
                'x' => ['margin-left' => $value, 'margin-right' => $value],
                'y' => ['margin-top' => $value, 'margin-bottom' => $value],
                't' => ['margin-top' => $value],
                'b' => ['margin-bottom' => $value],
                'l' => ['margin-left' => $value],
                'r' => ['margin-right' => $value],
                '' => ['margin' => $value],
                default => null,
            };
        }

        // 3. Check Paddings: -mt-4, -mx-2, -m-8
        if (preg_match('/^p(x|y|t|b|l|r)?-(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
            $dir = $matches[1] ?? '';
            $value = $matches[2] === '' ? ($matches[3] * 0.25) . 'rem' : $matches[2];

            return match ($dir) {
                'x' => ['padding-left' => $value, 'padding-right' => $value],
                'y' => ['padding-top' => $value, 'padding-bottom' => $value],
                't' => ['padding-top' => $value],
                'b' => ['padding-bottom' => $value],
                'l' => ['padding-left' => $value],
                'r' => ['padding-right' => $value],
                '' => ['padding' => $value],
                default => null,
            };
        }

        // 3. Check Negative Margins: -mt-4, -mx-2, -m-8
        if (preg_match('/^-p(x|y|t|b|l|r)?-(\d+)$/', $utility, $matches)) {
            $dir = $matches[1] ?? '';
            $rem = '-' . ($matches[2] * 0.25) . 'rem';

            return match ($dir) {
                'x' => ['padding-left' => $rem, 'padding-right' => $rem],
                'y' => ['padding-top' => $rem, 'padding-bottom' => $rem],
                't' => ['padding-top' => $rem],
                'b' => ['padding-bottom' => $rem],
                'l' => ['padding-left' => $rem],
                'r' => ['padding-right' => $rem],
                '' => ['padding' => $rem],
                default => null,
            };
        }

        // 5. Space-Between Sibling Utilities: space-x-4, space-y-2
        if (preg_match('/^space-(x|y)-(\d+)$/', $utility, $matches)) {
            $axis = $matches[1];
            $rem = ($matches[2] * 0.25) . 'rem';

            // CSS variable pipeline to prevent child element style pollution
            if ($axis === 'x') {
                return [
                    'vars' => [
                        '--lc-space-x-reverse' => '0',
                    ],
                    'margin-right' => 'calc(' . $rem . ' * var(--lc-space-x-reverse))',
                    'margin-left' => 'calc(' . $rem . ' * calc(1 - var(--lc-space-x-reverse)))',
                ];
            }

            return [
                'vars' => [
                    '--lc-space-y-reverse' => '0',
                ],
                'margin-bottom' => 'calc(' . $rem . ' * var(--lc-space-y-reverse))',
                'margin-top' => 'calc(' . $rem . ' * calc(1 - var(--lc-space-y-reverse)))',
            ];
        }

        return null;
    }
}
