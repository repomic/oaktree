<?php

namespace Oaktree;

use Oaktree\Rules\RuleInterface;

class Compiler
{
    /** @var RuleInterface[] */
    protected array $rules = [];

    /** Active keyframes collected during compilation run */
    protected array $usedKeyframes = [];

    /** Track active extends to prevent circular references */
    protected array $extendingStack = [];

    protected array $breakpoints = [
        'xs' => 390,
        'sm' => 640,
        'md' => 768,
        'lg' => 1024,
        'xl' => 1280,
        '2xl' => 1536,
    ];

    protected array $states = [
        'hover' => ':hover',
        'focus' => ':focus',
        'active' => ':active',
        'group-hover' => '.group:hover',
        'group-focus' => '.group:focus',
        'backdrop' => '::backdrop',
        'first' => ':first-child',
        'last' => ':last-child',
        'only' => ':only-child',
        'first-of-type' => ':first-of-type',
        'last-of-type' => ':last-of-type',
        'only-of-type' => ':only-of-type',
        'empty' => ':empty',
        'not-first' => ':not(:first-child)',
        'not-last' => ':not(:last-child)',
        'not-only' => ':not(:only-child)',
        'odd' => ':nth-child(odd)',
        'even' => ':nth-child(even)',
        'visited' => ':visited',
        'checked' => ':checked',
        'disabled' => ':disabled',
        'enabled' => ':enabled',
        'focus-within' => ':focus-within',
        'focus-visible' => ':focus-visible',
        'placeholder' => '::placeholder',
        'required' => ':required',
        'optional' => ':optional',
        'valid' => ':valid',
        'invalid' => ':invalid',
        'default' => ':default',
        'indeterminate' => ':indeterminate',
        'autofill' => ':-webkit-autofill',
    ];

    protected array $mediaModifiers = [
        'dark' => '(prefers-color-scheme: dark)',
        'light' => '(prefers-color-scheme: light)',
        'motion-reduce' => '(prefers-reduced-motion: reduce)',
        'print' => 'print',
    ];

    public function __construct(RuleInterface ...$rules)
    {
        foreach ($rules as $rule) {
            $this->registerRule($rule);
        }
    }

    public function breakpoints()
    {
        return $this->breakpoints;
    }

    public function registerRule(RuleInterface $rule): self
    {
        $this->rules[] = $rule;

        return $this;
    }

    public function compile(array $tokens): string
    {
        $baseRules = [];
        $mediaRules = [];
        $this->usedKeyframes = [];

        foreach ($tokens as $token) {
            $parts = explode(':', $token);
            $utility = trim(array_pop($parts));
            $modifiers = $parts;

            $ruleData = $this->resolveUtility($utility);
            if (!$ruleData) {
                continue;
            }

            $this->resolveAnimationsLazily($ruleData);

            $activeConditions = [];
            $stateModifiers = [];

            foreach ($modifiers as $mod) {
                if (isset($this->breakpoints[$mod])) {
                    $activeConditions[] = $this->resolveMediaCondition($mod);
                } elseif (preg_match('/^(min|max)-(.+)$/', $mod, $matches) && isset($this->breakpoints[$matches[2]])) {
                    $activeConditions[] = $this->resolveMediaCondition($mod);
                } elseif (isset($this->mediaModifiers[$mod])) {
                    // RESOLVE IT HERE: Pass $mod so it turns into (prefers-color-scheme: dark)
                    $activeConditions[] = $this->resolveMediaCondition($mod);
                } else {
                    $stateModifiers[] = $mod;
                }
            }

            $cssString = $this->formatCssRule(
                $this->formatSelector($this->escapeSelector($token), $stateModifiers),
                $ruleData
            );

            if (empty(trim($cssString))) {
                continue;
            }

            if (!empty($activeConditions)) {
                // Combine multiple media queries with 'and' (e.g., (min-width: 640px) and (max-width: 640px))
                $mediaKey = implode(' and ', $activeConditions);
                $mediaRules[$mediaKey][] = $cssString;
            } else {
                $baseRules[] = $cssString;
            }
        }

        return $this->buildFinalCss($baseRules, $mediaRules);
    }

    protected function resolveUtility(string $utility): ?array
    {
        foreach ($this->rules as $rule) {
            $result = $rule->handle($utility, $this);
            if ($result !== null) {
                return $this->resolveExtends($result, $utility);
            }
        }

        return null;
    }

    protected function resolveExtends(array $ruleData, string $currentContext = ''): array
    {
        $expandedData = [];

        foreach ($ruleData as $key => $value) {
            $extendTarget = null;

            if (is_string($key) && str_starts_with($key, '@extend')) {
                if ($key === '@extend') {
                    $extendTarget = $value;
                } else {
                    $extendTarget = trim(str_replace('@extend:', '', $key));
                }
            } elseif (is_string($value) && str_starts_with($value, '@extend:')) {
                $extendTarget = trim(str_replace('@extend:', '', $value));
            }

            if ($extendTarget !== null) {
                $targets = is_array($extendTarget) ? $extendTarget : [$extendTarget];

                foreach ($targets as $target) {
                    if (in_array($target, $this->extendingStack, true)) {
                        continue;
                    }

                    $this->extendingStack[] = $target;
                    $inheritedRules = $this->resolveUtility($target);
                    array_pop($this->extendingStack);

                    if ($inheritedRules !== null) {
                        $expandedData = $this->mergeCssRules($expandedData, $inheritedRules);
                    }
                }

                continue;
            }

            if (is_array($value)) {
                $expandedValue = $this->resolveExtends($value, $currentContext);
                if (isset($expandedData[$key]) && is_array($expandedData[$key])) {
                    $expandedData[$key] = $this->mergeCssRules($expandedData[$key], $expandedValue);
                } else {
                    $expandedData[$key] = $expandedValue;
                }
            } else {
                $expandedData[$key] = $value;
            }
        }

        return $expandedData;
    }

    protected function mergeCssRules(array $base, array $incoming): array
    {
        foreach ($incoming as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                $base[$key] = $this->mergeCssRules($base[$key], $value);
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }

    protected function escapeSelector(string $selector): string
    {
        return preg_replace('/([:\[\]\/#%.\,\@\(\)])/', '\\\\$1', $selector);
    }

    protected function formatSelector(string $escapedUtility, array $modifiers): string
    {
        $baseSelector = ".{$escapedUtility}";
        $pseudo = '';
        $wrapper = '%s';

        foreach ($modifiers as $mod) {
            if ($mod === 'group-hover') {
                $wrapper = '.group:hover ' . $wrapper;
            } elseif ($mod === 'group-focus') {
                $wrapper = '.group:focus ' . $wrapper;
            } elseif ($mod === 'dark') {
                $wrapper = '.dark ' . $wrapper;
            } elseif (preg_match('/^has\((.+)\)$/', $mod, $matches)) {
                // Extract inner selector (e.g. .form-invalid or input:invalid) and escape it safely
                $innerSelector = trim($matches[1]);
                $pseudo .= ":has({$innerSelector})";
            } elseif (isset($this->states[$mod])) {
                $pseudo .= $this->states[$mod];
            }
        }

        return sprintf($wrapper, $baseSelector . $pseudo);
    }

    protected function formatCssRule(string $selector = '', array $data = []): string
    {
        $flattened = $this->flattenCssRules($selector, $data);
        $grouped = [];
        $supportsGrouped = [];

        foreach ($flattened as $sel => $declarations) {
            if (empty($declarations)) {
                continue;
            }

            $body = [];
            foreach ($declarations as $prop => $val) {
                if ($prop === '@supports') {
                    continue; // Handled separately
                }
                if (is_numeric($prop)) {
                    $body[] = "{$val};";
                } elseif ($val instanceof CSS) {
                    $sel .= $val->state;
                    $body[] = "{$prop}: {$val->toString()};";
                } else {
                    $body[] = "{$prop}: {$val};";
                }
            }

            if (!empty($body)) {
                $body = array_map(fn($item) => rtrim($item, ';') . ';', $body);
                $bodyString = implode("\n", $body);

                if (isset($declarations['@supports'])) {
                    $supportsCondition = $declarations['@supports'];
                    $supportsGrouped[$supportsCondition][$bodyString][] = $sel;
                } else {
                    $grouped[$bodyString][] = $sel;
                }
            }
        }

        $output = [];
        foreach ($grouped as $bodyString => $selectors) {
            $combinedSelectors = implode(",\n", $selectors);
            $output[] = "{$combinedSelectors} {\n{$bodyString}\n}";
        }

        // Render @supports blocks
        foreach ($supportsGrouped as $supportsCondition => $bodies) {
            $supportsBlocks = [];
            foreach ($bodies as $bodyString => $selectors) {
                $combinedSelectors = implode(",\n", $selectors);
                $indentedBody = '  ' . str_replace("\n", "\n  ", $bodyString);
                $supportsBlocks[] = "  {$combinedSelectors} {\n{$indentedBody}\n  }";
            }
            $innerCss = implode("\n\n", $supportsBlocks);
            $output[] = "@supports {$supportsCondition} {\n{$innerCss}\n}";
        }

        return implode("\n\n", $output);
    }

    private function flattenCssRules(string $parentSelector, array $data, ?string $currentSupports = null): array
    {
        $declarations = [];
        $nestedResults = [];

        $data = $this->ensureRulePolyfills($data);

        foreach ($data as $prop => $val) {
            if (is_string($prop) && str_starts_with($prop, '@supports')) {
                $supportsCondition = trim(str_replace('@supports', '', $prop));
                if (is_array($val)) {
                    foreach ($this->flattenCssRules($parentSelector, $val, $supportsCondition) as $childSel => $childDecls) {
                        foreach ($childDecls as $dProp => $dVal) {
                            $nestedResults[$childSel][$dProp] = $dVal;
                        }
                    }
                }

                continue;
            }

            if (is_array($val)) {
                $propParts = array_filter(array_map('trim', explode(',', $prop)));

                $parentParts = !empty(trim($parentSelector))
                    ? array_filter(array_map('trim', explode(',', $parentSelector)))
                    : [''];

                foreach ($parentParts as $pPart) {
                    foreach ($propParts as $cPart) {
                        $fullSelector = $this->resolveSelector($pPart, $cPart);

                        foreach ($this->flattenCssRules($fullSelector, $val, $currentSupports) as $childSel => $childDecls) {
                            foreach ($childDecls as $dProp => $dVal) {
                                $nestedResults[$childSel][$dProp] = $dVal;
                            }
                        }
                    }
                }
            } else {
                $declarations[$prop] = $val;
            }
        }

        $result = [];
        $cleanParent = trim($parentSelector);

        if (!empty($cleanParent) && !empty($declarations)) {
            if ($currentSupports !== null) {
                $declarations['@supports'] = $currentSupports;
            }
            $result[$cleanParent] = $declarations;
        }

        foreach ($nestedResults as $childSel => $childDecls) {
            if (!isset($result[$childSel])) {
                $result[$childSel] = [];
            }
            $result[$childSel] = array_merge($result[$childSel], $childDecls);
        }

        return $result;
    }

    /**
     * Inspects properties and adds missing polyfill child rules if row-rule-*
     * or column-rule-* declarations are present without their child equivalents.
     */
    protected function ensureRulePolyfills(array $data): array
    {
        $childKey = '& > :not([hidden]) ~ :not([hidden])';
        $mapping = [
            'row-rule-width' => ['border-top-width', 'row-rule-width: 1px'],
            'row-rule-style' => ['border-top-style', 'row-rule-style: solid'],
            'row-rule-color' => ['border-top-color', 'row-rule-color: red'],
            'column-rule-width' => ['border-left-width', 'column-rule-width: 1px'],
            'column-rule-style' => ['border-left-style', 'column-rule-style: solid'],
            'column-rule-color' => ['border-left-color', 'column-rule-color: red'],
        ];

        foreach ($data as $prop => $val) {
            if (is_string($prop) && isset($mapping[$prop])) {
                [$mappedBorderProp, $supportsTest] = $mapping[$prop];
                $supportsKey = "@supports not ({$supportsTest})";

                if (!isset($data[$supportsKey][$childKey][$mappedBorderProp])) {
                    $data[$supportsKey][$childKey][$mappedBorderProp] = $val;
                }
            }
        }

        return $data;
    }

    private function resolveSelector(string $parent, string $child): string
    {
        $parent = trim($parent);
        $child = trim($child);

        if (empty($parent)) {
            return str_starts_with($child, '&') ? substr($child, 1) : $child;
        }

        if (str_contains($child, '&')) {
            return str_replace('&', $parent, $child);
        }

        return $parent . ' ' . $child;
    }

    protected function buildFinalCss(array $baseRules, array $mediaRules): string
    {
        $css = "\n\n";

        $css .= implode("\n\n", array_filter($baseRules)) . "\n\n";

        uksort($mediaRules, fn($a, $b) => $this->resolveBreakpointValue($a) <=> $this->resolveBreakpointValue($b));

        foreach ($mediaRules as $bp => $rules) {
            $mediaCondition = $this->resolveMediaCondition($bp);

            $css .= "@media {$mediaCondition} {\n";
            foreach ($rules as $rule) {
                $css .= '  ' . str_replace("\n", "\n  ", $rule) . "\n";
            }
            $css .= "}\n\n";
        }

        if (!empty($this->usedKeyframes)) {
            $css .= $this->formatKeyframesCss() . "\n\n";
        }

        return trim($css);
    }

    protected function resolveBreakpointValue(string $key): int
    {
        // If it's a compound query (e.g., contains 'and'), parse its parts or assign a weight
        if (str_contains($key, ' and ')) {
            // Take the max or min of the individual parts for sorting weight
            $parts = explode(' and ', $key);

            return max(array_map([$this, 'resolveBreakpointValue'], $parts));
        }

        if (isset($this->mediaModifiers[$key]) || str_starts_with($key, '(')) {
            // Distinguish between color-scheme/print vs max/min-width queries if needed,
            // or return a standard weight for media queries
            if (str_contains($key, 'prefers-color-scheme') || str_contains($key, 'print')) {
                return 10000;
            }
            if (preg_match('/min-width:\s*(\d+)px/', $key, $m)) {
                return (int)$m[1];
            }
            if (preg_match('/max-width:\s*(\d+)px/', $key, $m)) {
                return (int)$m[1];
            }

            return 10000;
        }

        if (isset($this->breakpoints[$key])) {
            return (int)$this->breakpoints[$key];
        }

        if (preg_match('/^(?:min|max)-\[(?:min-width:)?(\d+)(px|rem|em)?\]$/', $key, $matches)) {
            $val = (int)$matches[1];
            $unit = $matches[2] ?? 'px';

            return ($unit === 'rem' || $unit === 'em') ? $val * 16 : $val;
        }

        return 99999;
    }

    protected function resolveMediaCondition(string $key): string
    {
        // Check media modifiers first (e.g. dark, light, print, motion-reduce)
        if (isset($this->mediaModifiers[$key])) {
            return $this->mediaModifiers[$key];
        }

        if (str_starts_with($key, '(') || $key === 'print') {
            return $key;
        }

        if (isset($this->breakpoints[$key])) {
            return "(min-width: {$this->breakpoints[$key]}px)";
        }

        if (preg_match('/^(min|max)-(.+)$/', $key, $matches) && isset($this->breakpoints[$matches[2]])) {
            $type = $matches[1];
            $bpValue = $this->breakpoints[$matches[2]];

            // For max-width, oaktree standard is usually max-width minus 1px or direct value depending on setup,
            // but keeping your existing logic:
            return "({$type}-width: {$bpValue}px)";
        }

        if (preg_match('/^(min|max)-\[([^\]]+)\]$/', $key, $matches)) {
            $type = $matches[1] === 'min' ? 'min-width' : 'max-width';
            $value = str_replace('_', ' ', $matches[2]);

            return "({$type}: {$value})";
        }

        return trim($key, '[]');
    }

    protected function resolveAnimationsLazily(array $ruleData): void
    {
        array_walk_recursive($ruleData, function ($value, $key) {
            if ($key === 'animation' || $key === 'animation-name') {
                $this->lazyLoadKeyframeFromValue((string)$value);
            }
        });
    }

    protected function lazyLoadKeyframeFromValue(string $animationValue): void
    {
        $tokens = preg_split('/\s+/', trim($animationValue));

        foreach ($tokens as $token) {
            if (preg_match('/^(\d+(\.\d+)?(s|ms)|infinite|forwards|backwards|both|ease|ease-in|ease-out|ease-in-out|linear|none)$/i', $token)) {
                continue;
            }

            if (isset($this->usedKeyframes[$token])) {
                continue;
            }

            foreach ($this->rules as $rule) {
                if (method_exists($rule, 'animation') && $keyframe = $rule->animation($token)) {
                    $this->usedKeyframes[$token] = $keyframe;
                    break;
                }
            }
        }
    }

    protected function formatKeyframesCss(): string
    {
        $output = [];

        foreach ($this->usedKeyframes as $name => $steps) {
            $stepBlocks = [];
            foreach ($steps as $stepSelector => $props) {
                $propLines = [];
                foreach ($props as $prop => $val) {
                    $propLines[] = "    {$prop}: {$val};";
                }
                $propsString = implode("\n", $propLines);
                $stepBlocks[] = "  {$stepSelector} {\n{$propsString}\n  }";
            }

            $stepsCss = implode("\n", $stepBlocks);
            $output[] = "@keyframes {$name} {\n{$stepsCss}\n}";
        }

        return implode("\n\n", $output);
    }
}
