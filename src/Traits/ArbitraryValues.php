<?php

namespace Oaktree\Traits;

trait ArbitraryValues
{
    protected function normalizeValue(string $value): string
    {
        // Matches either an escaped underscore (\_) OR an unescaped underscore (_)
        return preg_replace_callback('/\\\\_|(?<!\\\\)_/', function ($matches) {
            // If it was escaped (\_), return a literal underscore without the backslash
            if ($matches[0] === '\_') {
                return '_';
            }

            // Otherwise, convert unescaped underscore to space
            return ' ';
        }, $value);
    }

    /**
     * Resolves raw utility values, supporting arbitrary brackets [...] and fractions/spacing scale.
     */
    protected function resolveValue(string $raw): ?string
    {
        // Handle arbitrary bracket values e.g. [17deg], [1.5], [10px]
        if (str_starts_with($raw, '[') && str_ends_with($raw, ']')) {
            $inner = trim($raw, '[]');

            return str_replace('_', ' ', $inner);
        }

        // Handle standard numeric values or fractions (e.g. 1/2 -> 50%)
        if (str_contains($raw, '/')) {
            [$numerator, $denominator] = explode('/', $raw);
            if (is_numeric($numerator) && is_numeric($denominator) && $denominator != 0) {
                return round(($numerator / $denominator) * 100, 4) . '%';
            }
        }

        // Numeric scale / spacing tokens (e.g. 4 -> 1rem assuming base spacing factor)
        if (is_numeric($raw)) {
            return ($raw * 0.25) . 'rem';
        }

        // Keyword tokens (full, screen, auto, none, etc.)
        return match ($raw) {
            'full' => '100%',
            'screen' => '100vw',
            'half' => '50%',
            'px' => '1px',
            'none' => 'none',
            'normal' => 'normal',
            default => $raw,
        };
    }
}
