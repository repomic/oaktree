<?php

namespace Oaktree\Components;

use Oaktree\Compiler;
use Oaktree\Rules\RuleInterface;

class MessageComponent implements RuleInterface
{
    /**
     * Pre-defined component abstractions.
     * Can map to raw CSS declarations OR compositions of existing utilities.
     */
    protected array $components = [
        'message' => [
            '@extend:shadow-md',
            'border-radius' => '.5rem',
            'padding' => '1rem 1.5rem',
            'position' => 'fixed',
            'bottom' => '3rem',
            'left' => '50%',
            'transform' => 'translateX(-50%)',
            'animation' => 'enterAndStay 4s ease-out forwards',
            'backdrop-filter' => 'blur(4px)',
            'z-index' => '1000',

            '&.message-info' => [
                'background' => '#3498dbcc',
                'color' => '#fff',
            ],

            '&.message-success' => [
                'background-color' => '#2ecc71cc',
                'color' => '#fff',
            ],

            '&.message-error' => [
                'background' => '#e74c3ccc',
                'color' => '#fff',
            ],

            '&.message-warning' => [
                'background' => '#e67e22cc',
                'color' => '#fff',
            ],
        ],
    ];

    protected array $keyframes = [
        'cssAnimation' => [
            'from' => ['opacity' => '1'],
            'to' => ['opacity' => '0'],
        ],
        'enterAndStay' => [
            '0%' => [
                'opacity' => '0',
                'transform' => 'translateX(-50%) translateY(10px)',
                'z-index' => '1000',
            ],
            '10%' => [
                'opacity' => '1',
                'transform' => 'translateX(-50%) translateY(0)',
            ],
            '90%' => [
                'opacity' => '1',
                'transform' => 'translateX(-50%) translateY(0)',
            ],
            '100%' => [
                'opacity' => '0',
                'transform' => 'translateX(-50%) translateY(0)',
                'z-index' => '-1',
            ],
        ],
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        // 1. Direct match in components map
        if (isset($this->components[$utility])) {
            return $this->components[$utility];
        }

        // 2. Dynamic component modifier matching (e.g., btn-red, btn-lg)
        if (preg_match('/^btn(-(sm|md|lg|xl))?$/', $utility, $matches)) {
            $size = $matches[2] ?? 'md';
            $padding = match ($size) {
                'sm' => '0.25rem 0.65rem',
                'md' => '0.5rem 1rem',
                'lg' => '0.75rem 1.5rem',
                'xl' => '1rem 2rem',
            };

            $font_size = match ($size) {
                'sm' => 'var(--ot-text-sm)',
                'md' => 'var(--ot-text-md)',
                'lg' => 'var(--ot-text-lg)',
                'xl' => 'var(--ot-text-xl)',
            };

            return [
                'font-size' => $font_size,
                'padding' => $padding,
            ];
        }

        return null;
    }

    public function animation(string $name): ?array
    {
        return $this->keyframes[$name] ?? null;
    }
}
