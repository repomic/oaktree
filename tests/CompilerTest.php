<?php

use Oaktree\Compiler;
use Oaktree\Components\DialogComponent;
use Oaktree\RuleLoader;

function compiler(): Compiler
{
    return new Compiler(...(new RuleLoader)->getRules());
}

it('compiles static dynamic and arbitrary utilities', function (): void {
    $css = compiler()->compile([
        'flex',
        'w-1/2',
        'p-[20px]',
        'bg-[#ff0000/.8]',
        'bg-[#ff0000/90%]',
        'bg-[#00ff00]',
        'text-lg',
        'cursor-pointer',
    ]);

    $this->assertStringContainsString(".flex {\ndisplay: flex;\n}", $css);
    $this->assertStringContainsString(".w-1\\/2 {\nwidth: 50%;\n}", $css);
    $this->assertStringContainsString(".p-\\[20px\\] {\npadding: 20px;\n}", $css);
    $this->assertStringContainsString('background-color: rgb(255 0 0 / 0.8);', $css);
    $this->assertStringContainsString('background-color: rgb(255 0 0 / 0.9);', $css);
    $this->assertStringContainsString('background-color: rgb(0 255 0);', $css);
    $this->assertStringContainsString('font-size: var(--ot-text-lg);', $css);
    $this->assertStringContainsString('cursor: pointer;', $css);
});

it('compiles a gradient from a semantic color to purple', function (): void {
    $css = compiler()->compile(['bg-gradient-to-tr', 'from-primary-500', 'to-purple-600']);

    $this->assertStringContainsString('background-image: linear-gradient(to top right, var(--ot-gradient-stops));', $css);
    $this->assertStringContainsString('--ot-gradient-from: var(--ot-primary) var(--ot-gradient-from-position);', $css);
    $this->assertStringContainsString('--ot-gradient-to: var(--ot-purple-600, oklch(0.558 0.288 302.321)) var(--ot-gradient-to-position);', $css);
});

it('applies states and breakpoints to the full selector', function (): void {
    $css = compiler()->compile(['md:hover:bg-primary']);

    $this->assertStringContainsString('@media (min-width: 768px)', $css);
    $this->assertStringContainsString('.md\\:hover\\:bg-primary:hover {', $css);
    $this->assertStringContainsString('background-color: var(--ot-primary);', $css);
});

it('compiles component media rules without requiring a breakpoint utility class', function (): void {
    $css = compiler()
        ->registerRule(new DialogComponent)
        ->compile(['drawer']);
    $mediaQuery = '@media (width < 768px)';
    $mediaQueryPosition = strpos($css, $mediaQuery);

    expect($mediaQueryPosition)->not->toBeFalse();
    expect(substr($css, 0, $mediaQueryPosition))->not->toContain('width: 100%;');
    expect(substr($css, $mediaQueryPosition))->toContain('.drawer {');
    expect(substr($css, $mediaQueryPosition))->toContain('width: 100%;');
    expect(substr($css, $mediaQueryPosition))->toContain('transform: translateY(100%);');
    expect(substr($css, $mediaQueryPosition))->toContain('.drawer[open] {');
    expect(substr($css, $mediaQueryPosition))->toContain('transform: translateY(0);');
});

it('provides the default rules to the compiler', function (): void {
    $css = (new Compiler(...(new RuleLoader)->getRules()))->compile(['flex', 'drawer']);

    expect($css)->toContain(".flex {\ndisplay: flex;");
    expect($css)->toContain('.drawer {');
});

it('wraps starting-style selectors in the starting-style at-rule', function (): void {
    $css = compiler()
        ->registerRule(new DialogComponent)
        ->compile(['drawer']);

    expect($css)->toContain("@starting-style {\n  .drawer[open] {");
    expect($css)->toContain('.drawer[open]::backdrop {');
    expect($css)->not->toContain('.drawer @starting-style[open]');
});

it('flattens nested component rules', function (): void {
    $css = compiler()->compile(['btn']);

    $this->assertStringContainsString('.btn {', $css);
    $this->assertStringContainsString('.btn:hover {', $css);
    $this->assertStringContainsString('background-color: var(--ot-primary-tint);', $css);
    $this->assertStringContainsString('.btn.icon-only {', $css);
});

it('resolves extends and only loads referenced keyframes', function (): void {
    $css = compiler()->compile(['message']);

    $this->assertStringContainsString('box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.5)', $css);
    $this->assertStringContainsString('@keyframes enterAndStay {', $css);
    $this->assertStringNotContainsString('@keyframes cssAnimation {', $css);
});

it('loads keyframes only for built-in animations used by the compiled utilities', function (): void {
    $css = compiler()->compile(['animate-spin', 'animate-pop-in', 'animate-none']);

    $this->assertStringContainsString('animation: spin 1s linear infinite;', $css);
    $this->assertStringContainsString('@keyframes spin {', $css);
    $this->assertStringContainsString('animation: pop-in 200ms cubic-bezier(0, 0, 0.2, 1) both;', $css);
    $this->assertStringContainsString('@keyframes pop-in {', $css);
    $this->assertStringNotContainsString('@keyframes ping {', $css);
});

it('ignores unknown tokens and resets keyframes between builds', function (): void {
    $compiler = compiler();

    $this->assertSame('', $compiler->compile(['does-not-exist']));
    $this->assertStringContainsString('@keyframes enterAndStay {', $compiler->compile(['message']));
    $this->assertStringNotContainsString('@keyframes enterAndStay {', $compiler->compile(['d-block']));
});
