<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Traits\ColorTransform;

class BackgroundRule implements RuleInterface
{
    protected array $static = [
        'bg-auto' => ['background-size' => 'auto'],
        'bg-cover' => ['background-size' => 'cover'],
        'bg-contain' => ['background-size' => 'contain'],

        'bg-repeat' => ['background-repeat' => 'repeat'],
        'bg-repeat-x' => ['background-repeat' => 'repeat-x'],
        'bg-repeat-y' => ['background-repeat' => 'repeat-y'],
        'bg-repeat-space' => ['background-repeat' => 'space'],
        'bg-repeat-round' => ['background-repeat' => 'round'],
        'bg-no-repeat' => ['background-repeat' => 'no-repeat'],

        'bg-top-left' => ['background-position' => 'top left'],
        'bg-top' => ['background-position' => 'top'],
        'bg-top-right' => ['background-position' => 'top right'],
        'bg-left' => ['background-position' => 'left'],
        'bg-center' => ['background-position' => 'center'],
        'bg-right' => ['background-position' => 'right'],
        'bg-bottom-left' => ['background-position' => 'bottom left'],
        'bg-bottom' => ['background-position' => 'bottom'],
        'bg-bottom-right' => ['background-position' => 'bottom right'],

        'bg-gradient-to-t' => ['background-image' => 'linear-gradient(to top, var(--lc-gradient-stops))'],
        'bg-gradient-to-tr' => ['background-image' => 'linear-gradient(to top right, var(--lc-gradient-stops))'],
        'bg-gradient-to-r' => ['background-image' => 'linear-gradient(to right, var(--lc-gradient-stops))'],
        'bg-gradient-to-br' => ['background-image' => 'linear-gradient(to bottom right, var(--lc-gradient-stops))'],
        'bg-gradient-to-b' => ['background-image' => 'linear-gradient(to bottom, var(--lc-gradient-stops))'],
        'bg-gradient-to-bl' => ['background-image' => 'linear-gradient(to bottom left, var(--lc-gradient-stops))'],
        'bg-gradient-to-l' => ['background-image' => 'linear-gradient(to left, var(--lc-gradient-stops))'],
        'bg-gradient-to-tl' => ['background-image' => 'linear-gradient(to top left, var(--lc-gradient-stops))'],
        'bg-none' => ['background-image' => 'none'],

        'bg-linear-to-t' => ['background-image' => 'linear-gradient(to top, var(--lc-gradient-stops))'],
        'bg-linear-to-tr' => ['background-image' => 'linear-gradient(to top right, var(--lc-gradient-stops))'],
        'bg-linear-to-r' => ['background-image' => 'linear-gradient(to right, var(--lc-gradient-stops))'],
        'bg-linear-to-br' => ['background-image' => 'linear-gradient(to bottom right, var(--lc-gradient-stops))'],
        'bg-linear-to-b' => ['background-image' => 'linear-gradient(to bottom, var(--lc-gradient-stops))'],
        'bg-linear-to-bl' => ['background-image' => 'linear-gradient(to bottom left, var(--lc-gradient-stops))'],
        'bg-linear-to-l' => ['background-image' => 'linear-gradient(to left, var(--lc-gradient-stops))'],
        'bg-linear-to-tl' => ['background-image' => 'linear-gradient(to top left, var(--lc-gradient-stops))'],
    ];

    use ColorTransform;

    public function handle(string $utility, Compiler $compiler): ?array
    {
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        if (preg_match('/^(from|via|to)-(\d+(?:\.\d+)?%)$/', $utility, $matches)) {
            return ["--lc-gradient-{$matches[1]}-position" => $matches[2]];
        }

        if (preg_match('/^(from|via|to)-(.+)$/', $utility, $matches)) {
            return $this->resolveGradientStop($matches[1], $matches[2]);
        }

        // Handle arbitrary background-size: bg-size-[100%_50%]
        if (preg_match('/^bg-size-\[([^\]]+)\]$/', $utility, $m)) {
            return ['background-size' => $m[1]];
        }

        if (preg_match('/^bg-linear-\[(.+)\]$/', $utility, $m)) {
            return ['background-image' => 'linear-gradient(' . str_replace('_', ' ', $m[1]) . ')'];
        }

        // Handle arbitrary background-image: bg-[url(...)]
        if (preg_match('/^bg-\[(url\(.+\))\]$/', $utility, $m)) {
            return ['background-image' => $m[1]];
        }

        // Handle background-color: bg-red-500, bg-[#ff0000]/80, bg-red-500/20
        if (preg_match('/^bg-([a-z0-9-]+|\[[^\]]+\])(?:\/([0-9%]+|\[[^\]]+\]))?$/', $utility, $m)) {
            return $this->resolveBackgroundColor($m[1], $m[2] ?? null);
        }

        return null;
    }

    private function resolveGradientStop(string $stop, string $rawValue): ?array
    {
        $rawParts = explode('/', $rawValue, 2);
        $rawColor = $rawParts[0];
        $rawAlpha = $rawParts[1] ?? null;
        $color = $this->resolveGradientColor($rawColor);

        if ($color === null) {
            return null;
        }

        if ($rawAlpha !== null) {
            $color = $this->colorWithOpacity($color, $rawAlpha);
        }

        return match ($stop) {
            'from' => [
                '--lc-gradient-from' => "{$color} var(--lc-gradient-from-position)",
                '--lc-gradient-to' => 'rgb(255 255 255 / 0) var(--lc-gradient-to-position)',
                '--lc-gradient-stops' => 'var(--lc-gradient-from), var(--lc-gradient-to)',
            ],
            'via' => [
                '--lc-gradient-to' => 'rgb(255 255 255 / 0) var(--lc-gradient-to-position)',
                '--lc-gradient-stops' => "var(--lc-gradient-from), {$color} var(--lc-gradient-via-position), var(--lc-gradient-to)",
            ],
            'to' => [
                '--lc-gradient-to' => "{$color} var(--lc-gradient-to-position)",
            ],
        };
    }

    private function resolveGradientColor(string $rawColor): ?string
    {
        $cleanColor = trim($rawColor, '[]');
        $color = $this->resolveColorName($cleanColor);

        if ($color !== null) {
            return $color;
        }

        if (str_starts_with($rawColor, '[')) {
            return $cleanColor;
        }

        return null;
    }

    private function resolveBackgroundColor(string $rawColor, ?string $rawAlpha): ?array
    {
        $cleanColor = trim($rawColor, '[]');

        $rgb = $this->resolveColorName($cleanColor);

        if (!$rgb && str_starts_with($rawColor, '[')) {
            $parsed = $this->parseColorValue($cleanColor);
            if ($parsed) {
                $rgb = $parsed['rgb'];
                $rawAlpha = $rawAlpha ?? $parsed['alpha'];
            } else {
                // Direct arbitrary fallback (e.g. var(--my-color))
                return ['background-color' => $cleanColor];
            }
        }

        if (!$rgb) {
            return null;
        }

        if ($rawAlpha !== null && $rawAlpha !== '') {
            if (str_starts_with($rgb, 'var(')) {
                return ['background-color' => $this->colorWithOpacity($rgb, $rawAlpha)];
            }

            return ['background-color' => "rgb({$rgb} / {$rawAlpha})"];
        }

        if (str_starts_with($rgb, 'var(') || stristr($rgb, 'rgb') || in_array($rgb, ['currentColor', 'inherit'], true)) {
            return ['background-color' => $rgb];
        }

        return ['background-color' => "rgb({$rgb})"];
    }
}
