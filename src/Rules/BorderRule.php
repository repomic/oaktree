<?php

namespace Oaktree\Rules;

use Oaktree\Colors;
use Oaktree\Compiler;
use Oaktree\Traits\ColorTransform;

class BorderRule implements RuleInterface
{
    use ColorTransform;

    protected array $static = [
        'border-solid' => ['border-style' => 'solid'],
        'border-dashed' => ['border-style' => 'dashed'],
        'border-dotted' => ['border-style' => 'dotted'],
        'border-double' => ['border-style' => 'double'],
        'border-none' => ['border-style' => 'none'],

        'outline' => ['outline-width' => '1px'],
        'outline-solid' => ['outline-style' => 'solid'],
        'outline-dashed' => ['outline-style' => 'dashed'],
        'outline-dotted' => ['outline-style' => 'dotted'],
        'outline-double' => ['outline-style' => 'double'],
        'outline-none' => ['outline-style' => 'none'],
        'outline-hidden' => ['outline' => '2px solid transparent', 'outline-offset' => '2px'],
    ];

    /**
     * Standard Border Radius scale in rems
     */
    protected array $radii = [
        'none' => '0px',
        'sm' => '0.125rem',
        'DEFAULT' => '0.25rem',
        'md' => '0.375rem',
        'lg' => '0.5rem',
        'xl' => '0.75rem',
        '2xl' => '1rem',
        '3xl' => '1.5rem',
        'full' => '100%',
        'pill' => '9999px',
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        // 1. Check static maps first
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        // 2. Default border shorthand: 'border'
        if ($utility === 'border') {
            return [
                'border-style' => 'solid',
                'border-width' => '1px',
            ];
        }

        // 3. Border Widths & Side Widths: border-2, border-t-4, border-l-[3px], border-0
        if (preg_match('/^border-(t|r|b|l)?-?(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
            $side = $matches[1] ?? '';
            $rawVal = $matches[2] !== '' ? $matches[2] : ($matches[3] ?? '');

            if ($rawVal === '') {
                return null;
            }

            $width = is_numeric($rawVal) ? "{$rawVal}px" : $rawVal;

            $prop = match ($side) {
                't' => 'border-top-width',
                'r' => 'border-right-width',
                'b' => 'border-bottom-width',
                'l' => 'border-left-width',
                '' => 'border-width',
                default => null,
            };

            if (!$prop) {
                return null;
            }

            $style = match ($side) {
                't' => ['border-top-style' => 'solid'],
                'r' => ['border-right-style' => 'solid'],
                'b' => ['border-bottom-style' => 'solid'],
                'l' => ['border-left-style' => 'solid'],
                '' => ['border-style' => 'solid'],
                default => [],
            };

            return [
                ...$style,
                $prop => $width,
            ];
        }

        // 4. Border Radius (Rounded): rounded, rounded-lg, rounded-t-xl, rounded-[10px]
        if (preg_match('/^rounded(?:-(t|r|b|l|tl|tr|br|bl))?(?:-(none|sm|md|lg|xl|2xl|3xl|full|pill|\[.*\]))?$/', $utility, $matches)) {
            $side = $matches[1] ?? '';
            $sizeRaw = $matches[2] ?? 'DEFAULT';

            // Resolve radius value
            $val = null;
            if (str_starts_with($sizeRaw, '[')) {
                $val = trim($sizeRaw, '[]');
            } else {
                $val = $this->radii[$sizeRaw] ?? null;
            }

            if (!$val) {
                return null;
            }

            $props = match ($side) {
                't' => ['border-top-left-radius' => $val, 'border-top-right-radius' => $val],
                'r' => ['border-top-right-radius' => $val, 'border-bottom-right-radius' => $val],
                'b' => ['border-bottom-left-radius' => $val, 'border-bottom-right-radius' => $val],
                'l' => ['border-top-left-radius' => $val, 'border-bottom-left-radius' => $val],
                'tl' => ['border-top-left-radius' => $val],
                'tr' => ['border-top-right-radius' => $val],
                'br' => ['border-bottom-right-radius' => $val],
                'bl' => ['border-bottom-left-radius' => $val],
                '' => ['border-radius' => $val],
                default => [],
            };

            return $props;
        }

        // 5. Border Colors & Opacity: border-red-500, border-blue-500/50, border-[#ccc]
        if (preg_match('/^border-([a-z0-9\-]+|\[#[a-fA-F0-9]{3,6}\])(?:\/(.+))?$/', $utility, $matches)) {
            $colorRaw = $matches[1];
            $opacity = $matches[2] ?? null;

            // Transparent check
            if ($colorRaw === 'transparent') {
                return ['border-color' => 'transparent'];
            }

            // Resolve RGB value
            $rgb = $this->resolveColorName($colorRaw);

            if (!$rgb && str_starts_with($colorRaw, '[')) {
                $rgb = $this->hexToRgb(trim($colorRaw, '[]'));
            }

            if (!$rgb) {
                return null;
            }

            if ($opacity) {
                return [
                    'border-color' => $this->colorWithOpacity($rgb, $opacity),
                ];
            }

            return [
                'border-color' => "{$rgb}",
            ];
        }

        /*
        // 6. Divide Widths
        if (preg_match('/^divide-(?:(x|y)-)?(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
            $axis = $matches[1] ?? '';
            $rawVal = $matches[2] !== '' ? $matches[2] : ($matches[3] ?? '');

            if ($rawVal === '') {
                return null;
            }

            $width = is_numeric($rawVal) ? "{$rawVal}px" : $rawVal;
            $childSelector = '& > :not([hidden]) ~ :not([hidden])';

            return match ($axis) {
                'x' => [
                    'column-rule-width' => $width,
                    'column-rule-style' => 'solid',
                    '@supports not (column-rule-width: 1px)' => [
                        $childSelector => [
                            'border-left-width' => $width,
                            'border-left-style' => 'solid',
                        ],
                    ],
                ],
                'y' => [
                    'row-rule-width' => $width,
                    'row-rule-style' => 'solid',
                    '@supports not (row-rule-width: 1px)' => [
                        $childSelector => [
                            'border-top-width' => $width,
                            'border-top-style' => 'solid',
                        ],
                    ],
                ],
                default => [
                    'column-rule-width' => $width,
                    'column-rule-style' => 'solid',
                    'row-rule-width' => $width,
                    'row-rule-style' => 'solid',
                    '@supports not (row-rule-width: 1px)' => [
                        $childSelector => [
                            'border-top-width' => $width,
                            'border-left-width' => $width,
                            'border-top-style' => 'solid',
                            'border-left-style' => 'solid',
                        ],
                    ],
                ],
            };
        }

        // 7. Divide Colors & Opacity
        if (preg_match('/^divide-(?:(x|y)-)?([a-z0-9\-]+|\[#[a-fA-F0-9]{3,6}\])(?:\/(.+))?$/', $utility, $matches)) {
            $axis = $matches[1] ?? '';
            $colorRaw = $matches[2];
            $opacity = $matches[3] ?? null;

            $rgb = $colorRaw === 'transparent' ? 'transparent' : $this->resolveColorName($colorRaw);

            if (!$rgb && str_starts_with($colorRaw, '[')) {
                $rgb = $this->hexToRgb(trim($colorRaw, '[]'));
            }

            if (!$rgb) {
                return null;
            }

            if ($opacity && $rgb !== 'transparent') {
                $alpha = str_ends_with($opacity, '%')
                    ? ((float)$opacity / 100)
                    : trim($opacity, '[]');

                $rgb = "rgb({$rgb} / {$alpha})";
            }

            $childSelector = '& > :not([hidden]) ~ :not([hidden])';

            return match($axis) {
                default => [
                    'position' => 'relative',
                    $childSelector.'::before' => [
                        'content' => '',
                        'position' => 'absolute',

                    ]
                ]
            }

            return match ($axis) {
                'x' => [
                    'column-rule-color' => $rgb,
                    '@supports not (column-rule-color: red)' => [
                        $childSelector => [
                            'border-left-color' => $rgb,
                        ],
                    ],
                ],
                'y' => [
                    'row-rule-color' => $rgb,
                    '@supports not (row-rule-color: red)' => [
                        $childSelector => [
                            'border-top-color' => $rgb,
                        ],
                    ],
                ],
                default => [
                    'column-rule-color' => $rgb,
                    'row-rule-color' => $rgb,
                    '@supports not (row-rule-color: red)' => [
                        $childSelector => [
                            'border-color' => $rgb,
                        ],
                    ],
                ],
            };
        }*/

        return null;
    }
}
