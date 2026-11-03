<?php

use Oaktree\CssCompressor;
use Oaktree\Validator;

it('accepts valid css identifiers and rejects invalid starts', function (): void {
    expect(Validator::isValid('bg-red-500'))->toBeTrue();
    expect(Validator::isValid('hover\\:bg-red'))->toBeTrue();
    expect(Validator::isValid('naïve'))->toBeTrue();
    expect(Validator::isValid(''))->toBeFalse();
    expect(Validator::isValid('2xl'))->toBeFalse();
    expect(Validator::isValid('--reserved'))->toBeFalse();
    expect(Validator::isValid('-2rem'))->toBeFalse();
});

it('strict validation allows only project style class names', function (): void {
    expect(Validator::isValidStrict('nav-link_active'))->toBeTrue();
    expect(Validator::isValidStrict('col-12'))->toBeTrue();
    expect(Validator::isValidStrict('hover:bg-red'))->toBeFalse();
    expect(Validator::isValidStrict('2xl'))->toBeFalse();
});

it('compressor removes comments whitespace units and redundant hex digits', function (): void {
    $css = (new CssCompressor)->compress('/* remove */ .a { color: #aabbcc; margin: 0px 0.50rem; } /*! keep */');

    expect($css)->toBe('.a{color:#abc;margin:0 .50rem}/*! keep */');
});
