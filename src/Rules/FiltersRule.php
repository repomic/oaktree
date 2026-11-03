<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Traits\ColorTransform;

class FiltersRule implements RuleInterface
{
    use ColorTransform;

    public function handle(string $utility, Compiler $compiler): ?array
    {
        $filters = [];

        if (preg_match('/^outline-filter-([a-z0-9-]+)$/', $utility, $matches)) {
            $filters['filter'] = $this->makeDefaultFilterString('filter');
            $rgb = $this->resolveColorName($matches[1]);

            if ($rgb === null) {
                return null;
            }

            $filters[] = "--lc-filter-drop-shadow: drop-shadow(.1rem 0 0 {$rgb}) drop-shadow(-.1rem 0 0 {$rgb}) drop-shadow(0 .1rem 0 {$rgb}) drop-shadow(0 -.1rem 0 {$rgb});";

            return $filters;
        }

        if ($utility === 'drop-shadow') {
            $filters['filter'] = $this->makeDefaultFilterString('filter');
            $filters[] = '--lc-filter-drop-shadow: drop-shadow(0 0px 6px rgba(0, 0, 0, 0.2));';

            return $filters;
        }

        if (preg_match('/^backdrop-(opacity|blur)-(\d+)$/', $utility, $matches)) {
            $type = 'backdrop-filter';
            $filter = $matches[1];

            $value = match ($filter) {
                'blur' => $matches[2] . 'px',
                'opacity' => $matches[2] / 100,
                default => null
            };

            if ($value) {
                $filters[$type] = $this->makeDefaultFilterString($type);
                $filters[] = '--lc-' . $type . '-' . $filter . ': ' . $filter . '(' . $value . ');';
            }

            return $filters;
        }

        // Matches bg-red-500, color-blue-500/50, bg-[#ff0000]/[0.8]
        // $pattern = '/^(bg|color|border|fill|stroke)-([a-z0-9]+(?:-[a-z0-9]+)*|\[[^\]]+\])(?:\/([0-9]+|\[[^\]]+\]))?$/';
        $pattern = '/^backdrop-bg-([a-z0-9]+(?:-[a-z0-9]+)*|\[[^\]]+\])(?:\/([0-9]+%?|\[[^\]]+\]))?$/';

        if (preg_match($pattern, $utility, $matches)) {
            $type = 'background-color';

            $colorRaw = $matches[1];
            $opacity = $matches[2] ?? null;

            $rgb = $this->resolveColorName(trim($colorRaw, '[]'));

            if (!$rgb && str_starts_with($colorRaw, '[')) {
                $rgb = $this->hexToRgb(trim($colorRaw, '[]'));
            }

            if (!$rgb) {
                return null;
            }

            if ($opacity !== null && $opacity !== '') {
                return [$type => $this->colorWithOpacity($rgb, $opacity)];
            }

            return [$type => str_starts_with($rgb, 'var(') || stristr($rgb, 'rgb') ? $rgb : "rgb({$rgb})"];
        }

        return null;
    }

    protected function makeDefaultFilterString(string $filter)
    {
        return 'var(--lc-' . $filter . '-blur, ) var(--lc-' . $filter . '-brightness, ) var(--lc-' . $filter . '-contrast, ) var(--lc-' . $filter . '-grayscale, ) var(--lc-' . $filter . '-hue-rotate, ) var(--lc-' . $filter . '-invert, ) var(--lc-' . $filter . '-saturate, ) var(--lc-' . $filter . '-sepia, ) var(--lc-' . $filter . '-drop-shadow, ) var(--lc-' . $filter . '-opacity, );';
    }
}
