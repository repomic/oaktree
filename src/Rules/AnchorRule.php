<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Css;

class AnchorRule implements RuleInterface
{
    /**
     * Static utility mappings for CSS Anchor Positioning.
     */
    protected array $static = [
        // Scope & Anchor Name setups
        'anchor-scope' => ['anchor-scope' => 'all'],

        // Base positioning setup required on the floating element (e.g. dropdown)
        'anchor-popover' => [
            'position' => 'fixed',
            'inset' => 'auto',
            'margin' => '0',
            'position-try-order' => 'most-height',
        ],

        // Directional placement with automatic viewport collision fallbacks
        // Using `position-area` (modern standard for inset-area)
        'anchor-bottom' => [
            'position-area' => 'bottom span-right',
            'position-try-fallbacks' => 'top span-right, bottom span-left, top span-left',
        ],
        'anchor-bottom-left' => [
            'position-area' => 'bottom span-left',
            'position-try-fallbacks' => 'top span-left, bottom span-right, top span-right',
        ],
        'anchor-top' => [
            'position-area' => 'top span-right',
            'position-try-fallbacks' => 'bottom span-right, top span-left, bottom span-left',
        ],
        'anchor-top-left' => [
            'position-area' => 'top span-left',
            'position-try-fallbacks' => 'bottom span-left, top span-right, bottom span-right',
        ],
        'anchor-left' => [
            'position-area' => 'left span-down',
            'position-try-fallbacks' => 'right span-down, left span-up, right span-up',
        ],
        'anchor-right' => [
            'position-area' => 'right span-down',
            'position-try-fallbacks' => 'left span-down, right span-up, left span-up',
        ],

        // Fine-grained alignment & positioning helpers using anchor()
        'anchor-align-left' => ['left' => 'anchor(left)'],
        'anchor-align-right' => ['right' => 'anchor(right)'],
        'anchor-align-top' => ['top' => 'anchor(top)'],
        'anchor-align-bottom' => ['bottom' => 'anchor(bottom)'],
    ];

    public function handle(string $utility, Compiler $compiler): array|Css|null
    {
        // 1. Check static utilities first
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        // 2. Handle dynamic anchor-name: anchor-name-[--btn] -> anchor-name: --btn
        if (str_starts_with($utility, 'anchor-name-[')) {
            $name = $this->extractArbitraryValue($utility, 'anchor-name-[');
            if ($name) {
                return ['anchor-name' => $name];
            }
        }

        // 3. Handle dynamic position-anchor: position-anchor-[--btn] -> position-anchor: --btn
        if (str_starts_with($utility, 'position-anchor-[')) {
            $anchor = $this->extractArbitraryValue($utility, 'position-anchor-[');
            if ($anchor) {
                return ['position-anchor' => $anchor];
            }
        }

        return null;
    }

    /**
     * Helper to extract arbitrary values like [--my-anchor] from utility string.
     */
    protected function extractArbitraryValue(string $utility, string $prefix): ?string
    {
        $value = substr($utility, strlen($prefix), -1);

        return $value !== '' ? $value : null;
    }
}
