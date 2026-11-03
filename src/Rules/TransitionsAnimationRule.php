<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Traits\ArbitraryValues;

class TransitionsAnimationRule implements RuleInterface
{
    use ArbitraryValues;

    protected const DEFAULT_TRANSITION = [
        'transition-duration' => '150ms',
        'transition-timing-function' => 'cubic-bezier(0.4, 0, 0.2, 1)',
    ];

    protected array $transitionProperties = [
        'transition' => 'color, background-color, border-color, outline-color, text-decoration-color, fill, stroke, --lc-gradient-from, --lc-gradient-via, --lc-gradient-to, opacity, box-shadow, transform, translate, scale, rotate, filter, -webkit-backdrop-filter, backdrop-filter, display, content-visibility, overlay, pointer-events',
        'transition-all' => 'all',
        'transition-colors' => 'color, background-color, border-color, outline-color, text-decoration-color, fill, stroke, --lc-gradient-from, --lc-gradient-via, --lc-gradient-to',
        'transition-opacity' => 'opacity',
        'transition-shadow' => 'box-shadow',
        'transition-transform' => 'transform, translate, scale, rotate',
        'transition-none' => 'none',
    ];

    protected array $timingFunctions = [
        'ease-linear' => 'linear',
        'ease-in' => 'cubic-bezier(0.4, 0, 1, 1)',
        'ease-out' => 'cubic-bezier(0, 0, 0.2, 1)',
        'ease-in-out' => 'cubic-bezier(0.4, 0, 0.2, 1)',
        'ease-initial' => 'initial',
    ];

    protected array $keyframes = [
        'spin' => [
            'to' => ['transform' => 'rotate(360deg)'],
        ],
        'ping' => [
            '75%, 100%' => [
                'transform' => 'scale(2)',
                'opacity' => '0',
            ],
        ],
        'pulse' => [
            '50%' => ['opacity' => '.5'],
        ],
        'bounce' => [
            '0%, 100%' => [
                'transform' => 'translateY(-25%)',
                'animation-timing-function' => 'cubic-bezier(0.8, 0, 1, 1)',
            ],
            '50%' => [
                'transform' => 'none',
                'animation-timing-function' => 'cubic-bezier(0, 0, 0.2, 1)',
            ],
        ],
        'pop-in' => [
            'from' => [
                'opacity' => '0',
                'transform' => 'scale(.95) translateY(4px)',
            ],
            'to' => [
                'opacity' => '1',
                'transform' => 'scale(1) translateY(0)',
            ],
        ],
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        if ($utility === 'transition-normal' || $utility === 'transition-discrete') {
            return ['transition-behavior' => $utility === 'transition-normal' ? 'normal' : 'allow-discrete'];
        }

        if (isset($this->transitionProperties[$utility])) {
            $declarations = ['transition-property' => $this->transitionProperties[$utility]];

            return $utility === 'transition-none'
                ? $declarations
                : array_merge($declarations, self::DEFAULT_TRANSITION);
        }

        if (preg_match('/^transition-\((--[^)]+)\)$/', $utility, $matches)) {
            return $this->transitionDeclarations("var({$matches[1]})");
        }

        if (preg_match('/^transition-\[(.+)\]$/', $utility, $matches)) {
            return $this->transitionDeclarations($this->normalizeValue($matches[1]));
        }

        if (preg_match('/^duration-(?:\((--[^)]+)\)|\[([^\]]+)\]|(\d+(?:\.\d+)?|initial))$/', $utility, $matches)) {
            $customProperty = $matches[1] ?? '';
            $arbitraryValue = $matches[2] ?? '';
            $duration = $matches[3] ?? '';

            $value = $customProperty !== ''
                ? "var({$customProperty})"
                : ($arbitraryValue !== '' ? $this->normalizeValue($arbitraryValue) : $duration . 'ms');

            if ($duration === 'initial') {
                $value = 'initial';
            }

            return ['transition-duration' => $value];
        }

        if (preg_match('/^(-)?delay-(?:\((--[^)]+)\)|\[([^\]]+)\]|(\d+(?:\.\d+)?))$/', $utility, $matches)) {
            $negative = $matches[1] ?? '';
            $customProperty = $matches[2] ?? '';
            $arbitraryValue = $matches[3] ?? '';
            $delay = $matches[4] ?? '';
            $value = $customProperty !== ''
                ? "var({$customProperty})"
                : ($arbitraryValue !== '' ? $this->normalizeValue($arbitraryValue) : $delay . 'ms');

            if ($negative !== '') {
                if ($customProperty !== '') {
                    $value = 'calc(0s - ' . $value . ')';
                } elseif ($delay !== '') {
                    $value = '-' . $value;
                } else {
                    $value = 'calc(0s - ' . $value . ')';
                }
            }

            return ['transition-delay' => $value];
        }

        if (isset($this->timingFunctions[$utility])) {
            return ['transition-timing-function' => $this->timingFunctions[$utility]];
        }

        if (preg_match('/^ease-\((--[^)]+)\)$/', $utility, $matches)) {
            return ['transition-timing-function' => "var({$matches[1]})"];
        }

        if (preg_match('/^ease-\[(.+)\]$/', $utility, $matches)) {
            return ['transition-timing-function' => $this->normalizeValue($matches[1])];
        }

        $animations = [
            'animate-spin' => 'spin 1s linear infinite',
            'animate-ping' => 'ping 1s cubic-bezier(0, 0, 0.2, 1) infinite',
            'animate-pulse' => 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            'animate-bounce' => 'bounce 1s infinite',
            'animate-pop-in' => 'pop-in 200ms cubic-bezier(0, 0, 0.2, 1) both',
            'animate-none' => 'none',
        ];

        if (isset($animations[$utility])) {
            return ['animation' => $animations[$utility]];
        }

        if (preg_match('/^animate-\((--[^)]+)\)$/', $utility, $matches)) {
            return ['animation' => "var({$matches[1]})"];
        }

        if (preg_match('/^animate-\[(.+)\]$/', $utility, $matches)) {
            return ['animation' => $this->normalizeValue($matches[1])];
        }

        return null;
    }

    public function animation(string $name): ?array
    {
        return $this->keyframes[$name] ?? null;
    }

    private function transitionDeclarations(string $property): array
    {
        return array_merge(
            ['transition-property' => $property],
            self::DEFAULT_TRANSITION,
        );
    }
}
