<?php

namespace Oaktree\Rules;

use Exception;
use Oaktree\Compiler;
use Oaktree\Traits\ColorTransform;

class TypographyRule implements RuleInterface
{
    use ColorTransform;

    protected array $static = [
        'text-left' => ['text-align' => 'left'],
        'text-center' => ['text-align' => 'center'],
        'text-right' => ['text-align' => 'right'],
        'text-justify' => ['text-align' => 'justify'],
        'text-start' => ['text-align' => 'start'],
        'text-end' => ['text-align' => 'end'],
        'text-nowrap' => ['white-space' => 'nowrap'],
        'text-wrap' => ['white-space' => 'wrap'],
        'text-ellipsis' => ['overflow' => 'hidden', 'text-overflow' => 'ellipsis', 'white-space' => 'nowrap'],
        'text-normal-case' => ['text-transform' => 'none'],
        'text-uppercase' => ['text-transform' => 'uppercase'],
        'text-lowercase' => ['text-transform' => 'lowercase'],
        'text-capitalize' => ['text-transform' => 'capitalize'],
        'text-underline' => ['text-decoration' => 'underline'],
        'text-line-through' => ['text-decoration' => 'line-through'],
        'text-overline' => ['text-decoration' => 'overline'],

        'normal-case' => ['text-transform' => 'none'],
        'uppercase' => ['text-transform' => 'uppercase'],
        'lowercase' => ['text-transform' => 'lowercase'],
        'capitalize' => ['text-transform' => 'capitalize'],
        'underline' => ['text-decoration' => 'underline'],
        'line-through' => ['text-decoration' => 'line-through'],

        'text-decoration-none' => ['text-decoration' => 'none'],
        'text-nodecor' => ['text-decoration' => 'none'], // temp,

        'line-inherit' => ['line-height' => 'inherit'],

        'font-thin' => ['font-weight' => '100'],
        'font-extralight' => ['font-weight' => '200'],
        'font-light' => ['font-weight' => '300'],
        'font-normal' => ['font-weight' => '400'],
        'font-medium' => ['font-weight' => '500'],
        'font-semibold' => ['font-weight' => '600'],
        'font-bold' => ['font-weight' => '700'],
        'font-extrabold' => ['font-weight' => '800'],
        'font-black' => ['font-weight' => '900'],

        'font-100' => ['font-weight' => '100'],
        'font-200' => ['font-weight' => '200'],
        'font-300' => ['font-weight' => '300'],
        'font-400' => ['font-weight' => '400'],
        'font-500' => ['font-weight' => '500'],
        'font-600' => ['font-weight' => '600'],
        'font-700' => ['font-weight' => '700'],
        'font-800' => ['font-weight' => '800'],
        'font-900' => ['font-weight' => '900'],

        'hyphens-none' => ['hyphens' => 'none'],
        'hyphens-manual' => ['hyphens' => 'manual'],
        'hyphens-auto' => ['hyphens' => 'auto'],
    ];

    protected array $font_sizes = [
        'base' => 'var(--ot-text-base)',
        '4xl' => 'var(--ot-text-4xl)',
        '3xl' => 'var(--ot-text-3xl)',
        '2xl' => 'var(--ot-text-2xl)',
        'xl' => 'var(--ot-text-xl)',
        'lg' => 'var(--ot-text-lg)',
        'md' => 'var(--ot-text-md)',
        'sm' => 'var(--ot-text-sm)',
        'xs' => 'var(--ot-text-xs)',
        '2xs' => 'var(--ot-text-2xs)',
    ];

    protected array $line_heights = [
        'base' => 'calc(var(--ot-text-base) * 1.25)',
        '4xl' => 'calc(var(--ot-text-4xl) * 1.25)',
        '3xl' => 'calc(var(--ot-text-3xl) * 1.25)',
        '2xl' => 'calc(var(--ot-text-2xl) * 1.25)',
        'xl' => 'calc(var(--ot-text-xl) * 1.25)',
        'lg' => 'calc(var(--ot-text-lg) * 1.25)',
        'md' => 'calc(var(--ot-text-md) * 1.25)',
        'sm' => 'calc(var(--ot-text-sm) * 1.25)',
        'xs' => 'calc(var(--ot-text-xs) * 1.25)',
        '2xs' => 'calc(var(--ot-text-2xs) * 1.25)',
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        if (preg_match('/^text-(' . implode('|', array_keys($this->font_sizes)) . ')$/', $utility, $matches)) {
            $size = $matches[1];

            return ['font-size' => $this->font_sizes[$size]];
        }

        try {
            if (preg_match('/^(?:-)?indent-(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
                $rawVal = $matches[2] ?? $matches[1];

                if ($rawVal === '') {
                    return null;
                }

                if (is_numeric($rawVal)) {
                    $size = ($rawVal * ($utility[0] === '-' ? -1 : 1)) . 'px';
                } else {
                    $size = $rawVal;
                }

                return ['text-indent' => $size];
            }

            if (preg_match('/^text-(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
                $rawVal = $matches[1] ?? $matches[2];

                if ($rawVal === '') {
                    return null;
                }

                $size = is_numeric($rawVal) ? "{$rawVal}px" : $rawVal;

                return ['font-size' => $size];
            }

            if (preg_match('/^line-(' . implode('|', array_keys($this->line_heights)) . ')$/', $utility, $matches)) {
                $size = $matches[1];

                return ['line-height' => $this->line_heights[$size]];
            }

            if (preg_match('/^line-(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
                $size = $matches[1] ?? $matches[2] ?? '';

                if ($size === '') {
                    return null;
                }

                $size = is_numeric($size) ? "{$size}px" : $size;

                return ['line-height' => $size];
            }
        } catch (Exception $e) {
            dd($e->getMessage(), $utility, $matches);
        }

        // Matches bg-red-500, color-blue-500/50, bg-[#ff0000]/[0.8]
        // $pattern = '/^(bg|color|border|fill|stroke)-([a-z0-9]+(?:-[a-z0-9]+)*|\[[^\]]+\])(?:\/([0-9]+|\[[^\]]+\]))?$/';
        $pattern = '/^(color)-([a-z0-9]+(?:-[a-z0-9]+)*|\[[^\]]+\])(?:\/([0-9]+%?|\[[^\]]+\]))?$/';

        if (preg_match($pattern, $utility, $m)) {
            $type = 'color';
            $colorRaw = $m[2];
            $opacity = $m[3] ?? null;

            // Resolve base RGB values
            $rgb = $this->resolveColorName($colorRaw);

            if (!$rgb && str_starts_with($colorRaw, '[')) {
                $rgb = $this->hexToRgb(trim($colorRaw, '[]'));
            }

            if (!$rgb) {
                return null;
            }

            if ($opacity) {
                return [
                    $type => $this->colorWithOpacity($rgb, $opacity),
                ];
            }

            return [
                $type => $rgb,
            ];
        }

        return null;
    }
}
