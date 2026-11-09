<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Traits\Fractions;

class SizingRule implements RuleInterface
{
    use Fractions;

    protected array $static = [
        'w-full' => ['width' => '100%'],
        'h-full' => ['height' => '100%'],
        'h-screen' => ['height' => '100vh'],
        'w-screen' => ['width' => '100vw'],
        'max-w-full' => ['max-width' => '100%'],
        'max-h-full' => ['max-height' => '100%'],
        'w-auto' => ['width' => 'auto'],
        'h-auto' => ['height' => 'auto'],
        'w-min' => ['width' => 'min-content'],
        'w-max' => ['width' => 'max-content'],
        'h-min' => ['height' => 'min-content'],
        'h-max' => ['height' => 'max-content'],
        'min-w-fit' => ['min-width' => 'fit-content'],
        'min-h-fit' => ['min-height' => 'fit-content'],
        'min-w-0' => ['min-width' => '0'],
        'min-h-0' => ['min-height' => '0'],
        'max-w-0' => ['max-width' => '0'],
        'max-h-0' => ['max-height' => '0'],
        'min-w-full' => ['min-width' => '100%'],
        'min-h-full' => ['min-height' => '100%'],
        'max-w-screen' => ['max-width' => '100vw'],
        'max-h-screen' => ['max-height' => '100vh'],
        'w-fit' => ['width' => 'fit-content'],
        'h-fit' => ['height' => 'fit-content'],
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        // Handle top, bottom, left, right with arbitrary values: top-[20px], bottom-[10%], left-[5rem], right-[2em]
        if (preg_match('/^((min|max)-(w|h))-\[(.+)\]$/', $utility, $matches)) {

            $prop = $matches[1];
            $value = preg_replace('/\s*(\*|\+|\-|\/)\s*/', ' $1 ', $matches[4]);

            return match ($prop) {
                'min-w' => ['min-width' => $value],
                'min-h' => ['min-height' => $value],
                'max-w' => ['max-width' => $value],
                'max-h' => ['max-height' => $value],
                default => [],
            };
        }

        // 4. Width / Height Fractions: w-1/2, w-3/4, h-1/2
        if (preg_match('/^(w|h)-(\d+\/\d+)$/', $utility, $matches)) {
            $prop = $matches[1] === 'w' ? 'width' : 'height';
            $frac = $matches[2];

            if ($percent = $this->fractionToPercent($frac)) {
                return [$prop => $percent];
            }
        }

        // Handle top, bottom, left, right with arbitrary values: top-[20px], bottom-[10%], left-[5rem], right-[2em]
        if (preg_match('/^(w|h)-(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
            $prop = $matches[1] === 'w' ? 'width' : 'height';
            $value = $matches[2] === '' ? ($matches[3] * 0.25) . 'rem' : $matches[2];

            if ($value) {
                return [$prop => $value];
            }
        }

        return null;
    }
}
