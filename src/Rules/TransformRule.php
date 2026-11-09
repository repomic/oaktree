<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Traits\ArbitraryValues;

class TransformRule implements RuleInterface
{
    use ArbitraryValues;

    protected array $static = [
        // Backface visibility
        'backface-visible' => ['backface-visibility' => 'visible'],
        'backface-hidden' => ['backface-visibility' => 'hidden'],

        // Perspective
        'perspective-none' => ['perspective' => 'none'],

        // Transform style
        'transform-flat' => ['transform-style' => 'flat'],
        'transform-preserve-3d' => ['transform-style' => 'preserve-3d'],

        // Transform base presets
        'transform-none' => ['transform' => 'none'],
        'transform-gpu' => [
            'transform' => 'translate3d(var(--ot-translate-x, 0), var(--ot-translate-y, 0), 0) rotate(var(--ot-rotate, 0)) skewX(var(--ot-skew-x, 0)) skewY(var(--ot-skew-y, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))',
        ],

        // Transform origins
        'origin-center' => ['transform-origin' => 'center'],
        'origin-top' => ['transform-origin' => 'top'],
        'origin-top-right' => ['transform-origin' => 'top right'],
        'origin-right' => ['transform-origin' => 'right'],
        'origin-bottom-right' => ['transform-origin' => 'bottom right'],
        'origin-bottom' => ['transform-origin' => 'bottom'],
        'origin-bottom-left' => ['transform-origin' => 'bottom left'],
        'origin-left' => ['transform-origin' => 'left'],
        'origin-top-left' => ['transform-origin' => 'top left'],
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        // 1. Static lookups
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        // 2. Perspective: perspective-1000, perspective-[500px]
        if (preg_match('/^perspective-(.+)$/', $utility, $m)) {
            $val = $this->resolveValue($m[1]);

            return $val ? ['perspective' => $val] : null;
        }

        // 3. Perspective Origin: perspective-origin-top, perspective-origin-[20%_30%]
        if (preg_match('/^perspective-origin-(.+)$/', $utility, $m)) {
            $val = $this->resolveValue($m[1]);

            return $val ? ['perspective-origin' => $val] : null;
        }

        $transform = [
            'transform' => 'translate(var(--ot-translate-x, 0), var(--ot-translate-y, 0)) rotate(var(--ot-rotate, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))',
        ];

        if (preg_match('/^translate-(x|y)-(?:\[([^\]]+)\]|(\d+))$/', $utility, $matches)) {
            $value = $matches[2] !== ''
                ? $matches[2]
                : 'calc(var(--ot-spacing) * ' . $matches[3] . ')';

            return $transform + ['--ot-translate-' . $matches[1] => $value];
        }

        if (preg_match('/^(-?)rotate-(?:\[([^\]]+)\]|(\d+))$/', $utility, $matches)) {
            $value = $matches[2] !== '' ? $matches[2] : $matches[3] . 'deg';

            if ($matches[1] === '-') {
                $value = 'calc(0deg - ' . $value . ')';
            }

            return $transform + ['--ot-rotate' => $value];
        }

        if (preg_match('/^scale(?:-(x|y))?-(?:\[([^\]]+)\]|(\d+))$/', $utility, $matches)) {
            $value = $matches[2] !== '' ? $matches[2] : (string)($matches[3] / 100);
            $axis = $matches[1] ?? '';

            return $transform + match ($axis) {
                'x' => ['--ot-scale-x' => $value],
                'y' => ['--ot-scale-y' => $value],
                default => ['--ot-scale-x' => $value, '--ot-scale-y' => $value],
            };
        }

        return null;
    }
}
