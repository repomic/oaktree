<?php

namespace Oaktree\Components;

use Oaktree\Compiler;
use Oaktree\Rules\RuleInterface;

class ButtonComponet implements RuleInterface
{
    /**
     * Pre-defined component abstractions.
     * Can map to raw CSS declarations OR compositions of existing utilities.
     */
    protected array $components = [
        'btn' => [
            'display' => 'inline-flex',
            'align-items' => 'center',
            'justify-content' => 'center',
            'padding' => '0.5rem 1rem',
            'border-radius' => '0.375rem',
            'font-weight' => '500',
            'transition' => 'background-color 0.15s ease-in-out, border-color 0.15s ease-in-out',
            'background-color' => 'var(--ot-primary)',
            'color' => 'var(--ot-primary-contrast)',
            'text-align' => 'center',
            'cursor' => 'pointer',
            'border' => '1px solid var(--ot-primary)',
            'position' => 'relative',
            '&:hover' => [
                'background-color' => 'var(--ot-primary-tint)',
                'color' => 'var(--ot-primary-contrast)',
                'text-decoration' => 'none',
            ],
            '&.outline' => [
                'background-color' => 'transparent',
                'color' => 'var(--ot-primary)',
                'border-color' => 'var(--ot-primary)',
                'border-width' => '1px',
                'border-style' => 'solid',
                '&:hover' => [
                    'background-color' => 'var(--ot-primary-tint)',
                    'color' => 'var(--ot-primary-contrast)',
                ],
            ],
            '& > i' => ['line-height: 1.25rem'],
            '&.icon-only' => ['font-size' => '1.25rem'],
        ],
        'btn-dropdown' => [
            'background-color' => 'var(--ot-light-shade)',
            'border-color' => 'var(--ot-secondary)',
            'color' => 'var(--ot-secondary-contrast)',
            '&:not(.appearance-none):hover' => [
                'background-color' => 'var(--ot-medium-tint)',
                'color' => 'var(--ot-secondary-contrast)',
                'border-color' => 'var(--ot-medium-tint)',
            ],
            '&.active:not(.appearance-none)' => [
                'background-color' => 'var(--ot-medium-tint)',
                'color' => 'var(--ot-secondary-contrast)',
                'border-color' => 'var(--ot-medium-tint)',
            ],
            '&.btn-outline' => [
                'background-color' => 'transparent',
                'border-color' => 'var(--ot-primary)',
                'color' => 'var(--ot-primary)',
                '&:not(.appearance-none):hover' => [
                    'background-color' => 'var(--ot-primary)',
                    'color' => 'var(--ot-primary-contrast)',
                    'border-color' => 'var(--ot-primary)',
                ],
                '&.active:not(.appearance-none)' => [
                    'background-color' => 'var(--ot-primary)',
                    'color' => 'var(--ot-primary-contrast)',
                    'border-color' => 'var(--ot-primary)',
                ],
            ],
        ],
        'btn-outline' => [
            'background-color' => 'transparent',
            'border-color' => 'var(--ot-primary)',
            'color' => 'var(--ot-primary)',
            '&:not(.appearance-none):hover' => [
                'background-color' => 'var(--ot-primary)',
                'color' => 'var(--ot-primary-contrast)',
                'border-color' => 'var(--ot-primary)',
            ],
            '&.active:not(.appearance-none)' => [
                'background-color' => 'var(--ot-primary)',
                'color' => 'var(--ot-primary-contrast)',
                'border-color' => 'var(--ot-primary)',
            ],
        ],
        'btn-ghost' => [
            'background-color' => 'transparent',
            'border-color' => 'transparent',
            'color' => 'var(--ot-primary)',
            '&:not(.appearance-none):hover' => [
                'background-color' => 'var(--ot-primary)',
                'color' => 'var(--ot-primary-contrast)',
                'border-color' => 'var(--ot-primary)',
            ],
            '&.active:not(.appearance-none)' => [
                'background-color' => 'var(--ot-primary)',
                'color' => 'var(--ot-primary-contrast)',
                'border-color' => 'var(--ot-primary)',
            ],
        ],
        'btn-tertiary' => [
            'background-color' => 'var(--ot-tertiary-shade)',
            'border-color' => 'var(--ot-tertiary-shade)',
            'color' => 'var(--ot-tertiary-contrast)',
            '&:not(.appearance-none):hover' => [
                'background-color' => 'var(--ot-tertiary)',
                'border-color' => 'var(--ot-tertiary)',
                'color' => 'var(--ot-tertiary-contrast)',
            ],
            '&.active:not(.appearance-none)' => [
                'background-color' => 'var(--ot-tertiary)',
                'border-color' => 'var(--ot-tertiary)',
                'color' => 'var(--ot-tertiary-contrast)',
            ],
        ],
        'btn-secondary' => [
            'background-color' => 'var(--ot-light-shade)',
            'border-color' => 'var(--ot-secondary)',
            'color' => 'var(--ot-secondary-contrast)',
            '&:not(.appearance-none):hover' => [
                'background-color' => 'var(--ot-medium-tint)',
                'color' => 'var(--ot-secondary-contrast)',
                'border-color' => 'var(--ot-medium-tint)',
            ],
            '&.active:not(.appearance-none)' => [
                'background-color' => 'var(--ot-medium-tint)',
                'color' => 'var(--ot-secondary-contrast)',
                'border-color' => 'var(--ot-medium-tint)',
            ],
        ],
        'btn-primary' => [
            'background-color' => 'var(--ot-primary)',
            'color' => 'var(--ot-primary-contrast)',
            // Child / state declarations
            '&:hover' => [
                'background-color' => 'var(--ot-primary-tint)',
                'color' => 'var(--ot-primary-contrast)',
            ],
        ],

        'btn-danger' => [
            'background-color' => 'var(--ot-danger)',
            'border-color' => 'var(--ot-danger)',
            'color' => 'var(--ot-danger-contrast)',

            '&:not(.appearance-none):hover, &.active:not(.appearance-none)' => [
                'background-color' => 'var(--ot-danger-tint)',
                'border-color' => 'var(--ot-danger-tint)',
                'color' => 'var(--ot-danger-contrast)',
            ],
        ],

        'btn-warning' => [
            'background-color' => 'var(--ot-warning)',
            'border-color' => 'var(--ot-warning)',
            'color' => 'var(--ot-warning-contrast)',

            '&:not(.appearance-none):hover, &.active:not(.appearance-none)' => [
                'background-color' => 'var(--ot-warning-tint)',
                'border-color' => 'var(--ot-warning-tint)',
                'color' => 'var(--ot-warning-contrast)',
            ],
        ],
        'card' => [
            'background-color' => '#ffffff',
            'border-radius' => '0.5rem',
            'padding' => '1.5rem',
            'box-shadow' => '0 1px 3px 0 rgba(0, 0, 0, 0.1)',
        ],
        'btn-group' => [
            'display' => 'inline-flex',
            'gap' => '0',
            'position' => 'relative',

            '& .btn' => [
                'flex' => '1 1 auto',
                'padding-left' => 'calc(var(--ot-spacing) * 2)',
                'padding-right' => 'calc(var(--ot-spacing) * 2)',
            ],

            '& .btn:not(:first-of-type)' => [
                'border-top-left-radius' => '0px',
                'border-bottom-left-radius' => '0px',
            ],

            '& .btn:not(:last-of-type)' => [
                'border-top-right-radius' => '0px',
                'border-bottom-right-radius' => '0px',
            ],

            '& .btn-group .btn' => [
                'padding-left' => 'calc(var(--ot-spacing) * 2)',
                'padding-right' => 'calc(var(--ot-spacing) * 2)',
                'flex' => '1 1 auto',
            ],

            '& .btn + .btn-group .btn' => [
                'border-top-left-radius' => '0px',
                'border-bottom-left-radius' => '0px',
            ],

            '& .btn-group:not(:first-of-type) .btn' => [
                'border-top-left-radius' => '0px',
                'border-bottom-left-radius' => '0px',
            ],

            '& .btn:has(+ .btn-group)' => [
                'border-top-right-radius' => '0px',
                'border-bottom-right-radius' => '0px',
            ],

            '& .btn-group:not(:last-of-type) .btn' => [
                'border-top-right-radius' => '0px',
                'border-bottom-right-radius' => '0px',
            ],

            '&.narrow-divider' => [
                '& .btn' => [
                    'padding-left' => 'calc(var(--ot-spacing) * 2)',
                    'padding-right' => 'calc(var(--ot-spacing) * 2)',
                ],
            ],
        ],
        'dropdown-content' => [
            'border-radius' => '0.5rem',
            'padding' => '0px',
            'min-width' => '275px',
            'max-width' => 'calc(100vw - 4rem)',
            'filter' => 'drop-shadow(0 0px 6px rgba(0, 0, 0, 0.2))',
            'z-index' => 100000,
            'align-self' => 'auto',
            'border' => '0px none',
            // 'position' => 'absolute',
            // 'bottom' => '0',
            // 'left' => '100%',
            // 'transform' => 'translateX(-100%) translateY(100%)',
            'margin' => '0',

            '&::backdrop' => [
                'background-color' => '#00000033',
                'opacity' => 1,
                'backdrop-filter' => 'blur(4px)',
            ],
            '& *' => [
                'max-width' => '100% !important',
                'box-sizing' => 'border-box',
            ],
            '& p' => [
                'margin' => '0',
                'padding' => '.5rem',
                'font-weight' => 'normal',
            ],
            '& hr' => [
                'margin-top' => '.5rem',
                'margin-bottom' => '.5rem',
                'border-color' => 'var(--ot-light-shade)',
                'display' => 'none',
            ],
            '& * + hr' => [
                'display' => 'block',
            ],
            '&.left-sided' => [
                'transform' => 'translateX(0%) translateY(100%)',
                'left' => '0px',
            ],
            '& .dropdown-item' => [
                'padding' => '.5rem',
                'border-radius' => '.5rem',
                'gap' => '.5rem',
                'text-decoration' => 'none',
                'position' => 'relative',
                'z-index' => 2,
                'line-height' => 'normal',
                'display' => 'grid',
                'margin-left' => 'calc(20px + 0.5rem)',
                'text-align' => 'initial',
                'white-space' => 'wrap',
                '&:hover' => [
                    'background-color' => 'var(--ot-light)',
                    'text-decoration' => 'none',
                ],
                '& small' => [
                    'font-weight' => 'normal',
                    'text-decoration' => 'none',
                ],

                '& *[class*="fa-"]' => [
                    'margin-top' => '.1rem',
                    'vertical-align' => 'middle',
                ],
                '&:has(i[class*="fa"])' => [
                    'grid-template-columns' => '20px minmax(0px, 1fr)',
                    'margin-left' => '0px',
                ],
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
}
