<?php

use Oaktree\Compiler;
use Oaktree\Components\ButtonComponet;
use Oaktree\Components\MessageComponent;
use Oaktree\Rules\BackgroundRule;
use Oaktree\Rules\BorderRule;
use Oaktree\Rules\ContainerRule;
use Oaktree\Rules\EffectRule;
use Oaktree\Rules\FiltersRule;
use Oaktree\Rules\FlexboxGridRule;
use Oaktree\Rules\FormRule;
use Oaktree\Rules\InteractivityRule;
use Oaktree\Rules\LayoutRule;
use Oaktree\Rules\ListRule;
use Oaktree\Rules\SizingRule;
use Oaktree\Rules\SpacingRule;
use Oaktree\Rules\TransformRule;
use Oaktree\Rules\TransitionsAnimationRule;
use Oaktree\Rules\TypographyRule;

dataset('rules', [
    'background' => [new BackgroundRule, 'bg-primary', ['background-color' => 'var(--ot-primary)']],
    'background black' => [new BackgroundRule, 'bg-black', ['background-color' => 'var(--ot-black, #000)']],
    'background inherit' => [new BackgroundRule, 'bg-inherit', ['background-color' => 'inherit']],
    'background oaktree palette' => [new BackgroundRule, 'bg-red-500', ['background-color' => 'var(--ot-red-500, oklch(0.637 0.237 25.331))']],
    'background extended oaktree palette' => [new BackgroundRule, 'bg-mist-500', ['background-color' => 'var(--ot-mist-500, oklch(56% 0.021 213.5))']],
    'background palette opacity' => [new BackgroundRule, 'bg-blue-500/50', ['background-color' => 'color-mix(in srgb, var(--ot-blue-500, oklch(0.623 0.214 259.815)) 50%, transparent)']],
    'background gradient direction' => [new BackgroundRule, 'bg-gradient-to-tr', ['background-image' => 'linear-gradient(to top right, var(--ot-gradient-stops))']],
    'background gradient from color' => [new BackgroundRule, 'from-primary-500', ['--ot-gradient-from' => 'var(--ot-primary) var(--ot-gradient-from-position)', '--ot-gradient-to' => 'rgb(255 255 255 / 0) var(--ot-gradient-to-position)', '--ot-gradient-stops' => 'var(--ot-gradient-from), var(--ot-gradient-to)']],
    'background gradient to color' => [new BackgroundRule, 'to-purple-600', ['--ot-gradient-to' => 'var(--ot-purple-600, oklch(0.558 0.288 302.321)) var(--ot-gradient-to-position)']],
    'border' => [new BorderRule, 'border-2', ['border-style' => 'solid', 'border-width' => '2px']],
    'border palette opacity' => [new BorderRule, 'border-emerald-700/25', ['border-color' => 'color-mix(in srgb, var(--ot-emerald-700, oklch(0.508 0.118 165.612)) 25%, transparent)']],
    'button' => [new ButtonComponet, 'btn-primary', ['background-color' => 'var(--ot-primary)', 'color' => 'var(--ot-primary-contrast)', '&:hover' => ['background-color' => 'var(--ot-primary-tint)', 'color' => 'var(--ot-primary-contrast)']]],
    'container' => [new ContainerRule, 'container-md', ['width' => '100%', 'max-width' => '768px']],
    'effect' => [new EffectRule, 'opacity-50', ['opacity' => 0.5]],
    'filters' => [new FiltersRule, 'backdrop-blur-4', ['backdrop-filter' => 'var(--ot-backdrop-filter-blur, ) var(--ot-backdrop-filter-brightness, ) var(--ot-backdrop-filter-contrast, ) var(--ot-backdrop-filter-grayscale, ) var(--ot-backdrop-filter-hue-rotate, ) var(--ot-backdrop-filter-invert, ) var(--ot-backdrop-filter-saturate, ) var(--ot-backdrop-filter-sepia, ) var(--ot-backdrop-filter-drop-shadow, ) var(--ot-backdrop-filter-opacity, );', 0 => '--ot-backdrop-filter-blur: blur(4px);']],
    'filter outline palette' => [new FiltersRule, 'outline-filter-rose-500', ['filter' => 'var(--ot-filter-blur, ) var(--ot-filter-brightness, ) var(--ot-filter-contrast, ) var(--ot-filter-grayscale, ) var(--ot-filter-hue-rotate, ) var(--ot-filter-invert, ) var(--ot-filter-saturate, ) var(--ot-filter-sepia, ) var(--ot-filter-drop-shadow, ) var(--ot-filter-opacity, );', 0 => '--ot-filter-drop-shadow: drop-shadow(.1rem 0 0 var(--ot-rose-500, oklch(0.645 0.246 16.439))) drop-shadow(-.1rem 0 0 var(--ot-rose-500, oklch(0.645 0.246 16.439))) drop-shadow(0 .1rem 0 var(--ot-rose-500, oklch(0.645 0.246 16.439))) drop-shadow(0 -.1rem 0 var(--ot-rose-500, oklch(0.645 0.246 16.439)));']],
    'flexbox grid' => [new FlexboxGridRule, 'col-span-3', ['grid-column-start' => 'span 3']],
    'flexbox gap' => [new FlexboxGridRule, 'gap-2', ['gap' => '0.5rem']],
    'flexbox horizontal gap' => [new FlexboxGridRule, 'gap-x-2', ['column-gap' => '0.5rem']],
    'flexbox vertical gap' => [new FlexboxGridRule, 'gap-y-2', ['row-gap' => '0.5rem']],
    'flexbox arbitrary horizontal gap' => [new FlexboxGridRule, 'gap-x-[12px]', ['column-gap' => '12px']],
    'flexbox arbitrary vertical gap' => [new FlexboxGridRule, 'gap-y-[1.25rem]', ['row-gap' => '1.25rem']],
    'form' => [new FormRule, 'form-text', ['font-size' => 'var(--ot-text-sm)', 'line-height' => '1.25']],
    'interactivity cursor' => [new InteractivityRule, 'cursor-pointer', ['cursor' => 'pointer']],
    'interactivity appearance' => [new InteractivityRule, 'appearance-none', ['appearance' => 'none', '-webkit-appearance' => 'none']],
    'interactivity pointer-events' => [new InteractivityRule, 'pointer-events-none', ['pointer-events' => 'none']],
    'interactivity select' => [new InteractivityRule, 'select-none', ['user-select' => 'none']],
    'interactivity scroll' => [new InteractivityRule, 'scroll-smooth', ['scroll-behavior' => 'smooth']],
    'interactivity resize' => [new InteractivityRule, 'resize-y', ['resize' => 'vertical']],
    'layout' => [new LayoutRule, 'position-absolute', ['position' => 'absolute']],
    'list' => [new ListRule, 'list-disc', ['list-style-type' => 'disc']],
    'message' => [new MessageComponent, 'btn-lg', ['font-size' => 'var(--ot-text-lg)', 'padding' => '0.75rem 1.5rem']],
    'sizing' => [new SizingRule, 'w-1/2', ['width' => '50%']],
    'spacing' => [new SpacingRule, '-mt-4', ['margin-top' => '-1rem']],
    'transform' => [new TransformRule, 'scale-50', ['transform' => 'translate(var(--ot-translate-x, 0), var(--ot-translate-y, 0)) rotate(var(--ot-rotate, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))', '--ot-scale-x' => '0.5', '--ot-scale-y' => '0.5']],
    'transform translate' => [new TransformRule, 'translate-x-4', ['transform' => 'translate(var(--ot-translate-x, 0), var(--ot-translate-y, 0)) rotate(var(--ot-rotate, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))', '--ot-translate-x' => 'calc(var(--ot-spacing) * 4)']],
    'transform arbitrary translate' => [new TransformRule, 'translate-y-[25%]', ['transform' => 'translate(var(--ot-translate-x, 0), var(--ot-translate-y, 0)) rotate(var(--ot-rotate, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))', '--ot-translate-y' => '25%']],
    'transform rotate' => [new TransformRule, 'rotate-45', ['transform' => 'translate(var(--ot-translate-x, 0), var(--ot-translate-y, 0)) rotate(var(--ot-rotate, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))', '--ot-rotate' => '45deg']],
    'transform arbitrary rotate' => [new TransformRule, 'rotate-[1turn]', ['transform' => 'translate(var(--ot-translate-x, 0), var(--ot-translate-y, 0)) rotate(var(--ot-rotate, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))', '--ot-rotate' => '1turn']],
    'transform negative rotate' => [new TransformRule, '-rotate-45', ['transform' => 'translate(var(--ot-translate-x, 0), var(--ot-translate-y, 0)) rotate(var(--ot-rotate, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))', '--ot-rotate' => 'calc(0deg - 45deg)']],
    'transform scale x' => [new TransformRule, 'scale-x-75', ['transform' => 'translate(var(--ot-translate-x, 0), var(--ot-translate-y, 0)) rotate(var(--ot-rotate, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))', '--ot-scale-x' => '0.75']],
    'transform scale y' => [new TransformRule, 'scale-y-125', ['transform' => 'translate(var(--ot-translate-x, 0), var(--ot-translate-y, 0)) rotate(var(--ot-rotate, 0)) scaleX(var(--ot-scale-x, 1)) scaleY(var(--ot-scale-y, 1))', '--ot-scale-y' => '1.25']],
    'transition' => [new TransitionsAnimationRule, 'transition-colors', ['transition-property' => 'color, background-color, border-color, outline-color, text-decoration-color, fill, stroke, --ot-gradient-from, --ot-gradient-via, --ot-gradient-to', 'transition-duration' => '150ms', 'transition-timing-function' => 'cubic-bezier(0.4, 0, 0.2, 1)']],
    'transition property custom value' => [new TransitionsAnimationRule, 'transition-[opacity,transform]', ['transition-property' => 'opacity,transform', 'transition-duration' => '150ms', 'transition-timing-function' => 'cubic-bezier(0.4, 0, 0.2, 1)']],
    'transition behavior' => [new TransitionsAnimationRule, 'transition-discrete', ['transition-behavior' => 'allow-discrete']],
    'transition duration' => [new TransitionsAnimationRule, 'duration-300', ['transition-duration' => '300ms']],
    'transition duration custom property' => [new TransitionsAnimationRule, 'duration-(--my-duration)', ['transition-duration' => 'var(--my-duration)']],
    'transition delay' => [new TransitionsAnimationRule, 'delay-150', ['transition-delay' => '150ms']],
    'negative transition delay' => [new TransitionsAnimationRule, '-delay-75', ['transition-delay' => '-75ms']],
    'transition timing function' => [new TransitionsAnimationRule, 'ease-in-out', ['transition-timing-function' => 'cubic-bezier(0.4, 0, 0.2, 1)']],
    'animation' => [new TransitionsAnimationRule, 'animate-spin', ['animation' => 'spin 1s linear infinite']],
    'pop in animation' => [new TransitionsAnimationRule, 'animate-pop-in', ['animation' => 'pop-in 200ms cubic-bezier(0, 0, 0.2, 1) both']],
    'custom animation' => [new TransitionsAnimationRule, 'animate-[wiggle_1s_ease-in-out_infinite]', ['animation' => 'wiggle 1s ease-in-out infinite']],
    'typography palette opacity' => [new TypographyRule, 'color-violet-600/50', ['color' => 'color-mix(in srgb, var(--ot-violet-600, oklch(0.541 0.281 293.009)) 50%, transparent)']],
    'typography' => [new TypographyRule, 'text-2xl', ['font-size' => 'var(--ot-text-2xl)']],
]);

it('compiles utilities to CSS rules', function (object $rule, string $utility, array $expected): void {
    expect($rule->handle($utility, new Compiler))->toBe($expected);

    $compiled = (new Compiler($rule))->compile([$utility]);

    $normalize = static function (mixed $value) use (&$normalize): string {
        if (is_array($value)) {
            $value = implode(' ', array_map(
                static fn(mixed $item, int|string $key): string => is_int($key)
                    ? $normalize($item)
                    : "$key: " . $normalize($item) . ';',
                $value,
                array_keys($value),
            ));
        }

        return preg_replace('/;+/', ';', preg_replace('/\s+/', ' ', trim((string)$value)));
    };
    $normalizedCompiled = $normalize($compiled);

    foreach ($expected as $property => $value) {
        if (is_int($property)) {
            continue;
        }

        if (is_array($value)) {
            foreach ($value as $nestedProperty => $nestedValue) {
                expect($normalizedCompiled)->toContain($normalize("$nestedProperty: $nestedValue;"));
            }

            continue;
        }

        expect($normalizedCompiled)->toContain($normalize("$property: $value;"));
    }
})->with('rules');

it('handles utilities with dark: modifier', function (): void {
    $compiler = new Compiler(new BackgroundRule);
    expect($compiler->compile(['dark:bg-primary']))->toBe(
        "@media (prefers-color-scheme: dark) {\n  .dark\:bg-primary {\n  background-color: var(--ot-primary);\n  }\n}"
    );
});

it('resolves every oaktree palette shade through its lc custom property', function (): void {
    $rule = new BackgroundRule;
    $palettes = [
        'red',
        'orange',
        'amber',
        'yellow',
        'lime',
        'green',
        'emerald',
        'teal',
        'cyan',
        'sky',
        'blue',
        'indigo',
        'violet',
        'purple',
        'fuchsia',
        'pink',
        'rose',
        'slate',
        'gray',
        'zinc',
        'neutral',
        'stone',
        'taupe',
        'mauve',
        'mist',
        'olive',
    ];
    $shades = ['50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950'];

    foreach ($palettes as $palette) {
        foreach ($shades as $shade) {
            $result = $rule->handle("bg-{$palette}-{$shade}", new Compiler);

            expect($result)->toBeArray();
            expect($result['background-color'])->toStartWith("var(--ot-{$palette}-{$shade}, oklch(");
        }
    }
});

it('returns null for unhandled utilities', function (object $rule): void {
    expect($rule->handle('not-a-utility', new Compiler))->toBeNull();
})->with('rules');
