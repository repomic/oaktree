<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;

class LayoutRule implements RuleInterface
{
    protected array $static = [
        'aspect-square' => ['aspect-ratio' => '1/1'],
        'aspect-video' => ['aspect-ratio' => 'var(--lc-aspect-video, 16/9)'],
        'aspect-auto' => ['aspect-ratio' => 'auto'],

        'd-block' => ['display' => 'block'],
        'd-inline-block' => ['display' => 'inline-block'],
        'd-inline' => ['display' => 'inline'],
        'd-flex' => ['display' => 'flex'],
        'd-grid' => ['display' => 'grid'],
        'd-inline-grid' => ['display' => 'inline-grid'],
        'd-inline-flex' => ['display' => 'inline-flex'],
        'd-none' => ['display' => 'none'],

        'block' => ['display' => 'block'],
        'inline-block' => ['display' => 'inline-block'],
        'inline' => ['display' => 'inline'],
        'flex' => ['display' => 'flex'],
        'grid' => ['display' => 'grid'],
        'inline-grid' => ['display' => 'inline-grid'],
        'inline-flex' => ['display' => 'inline-flex'],

        'float-left' => ['float' => 'left'],
        'float-right' => ['float' => 'right'],
        'float-start' => ['float' => 'inline-start'],
        'float-end' => ['float' => 'inline-end'],
        'float-none' => ['float' => 'none'],

        'clear-left' => ['clear' => 'left'],
        'clear-right' => ['clear' => 'right'],
        'clear-start' => ['clear' => 'inline-start'],
        'clear-end' => ['clear' => 'inline-end'],
        'clear-none' => ['clear' => 'none'],

        'overflow-auto' => ['overflow' => 'auto'],
        'overflow-hidden' => ['overflow' => 'hidden'],
        'overflow-visible' => ['overflow' => 'visible'],
        'overflow-scroll' => ['overflow' => 'scroll'],
        'overflow-x-auto' => ['overflow-x' => 'auto'],
        'overflow-x-hidden' => ['overflow-x' => 'hidden'],
        'overflow-x-visible' => ['overflow-x' => 'visible'],
        'overflow-x-scroll' => ['overflow-x' => 'scroll'],
        'overflow-y-auto' => ['overflow-y' => 'auto'],
        'overflow-y-hidden' => ['overflow-y' => 'hidden'],
        'overflow-y-visible' => ['overflow-y' => 'visible'],
        'overflow-y-scroll' => ['overflow-y' => 'scroll'],

        'overscroll-auto' => ['overscroll-behavior' => 'auto'],
        'overscroll-contain' => ['overscroll-behavior' => 'contain'],
        'overscroll-none' => ['overscroll-behavior' => 'none'],
        'overscroll-x-auto' => ['overscroll-behavior-x' => 'auto'],
        'overscroll-x-contain' => ['overscroll-behavior-x' => 'contain'],
        'overscroll-x-none' => ['overscroll-behavior-x' => 'visible'],
        'overscroll-y-auto' => ['overscroll-behavior-y' => 'auto'],
        'overscroll-y-contain' => ['overscroll-behavior-y' => 'contain'],
        'overscroll-y-none' => ['overscroll-behavior-y' => 'none'],

        'block' => ['display' => 'block'],
        'inline-block' => ['display' => 'inline-block'],
        'inline' => ['display' => 'inline'],
        'flex' => ['display' => 'flex'],
        'inline-flex' => ['display' => 'inline-flex'],
        'grid' => ['display' => 'grid'],
        'hidden' => ['display' => 'none'],

        'visible' => ['visibility' => 'visible'],
        'invisible' => ['visibility' => 'hidden'],
        'collapse' => ['visibility' => 'collapse'],

        'box-border' => ['box-sizing' => 'content-box'],
        'box-content' => ['box-sizing' => 'content-box'],

        'object-contain' => ['object-fit' => 'contain'],
        'object-cover' => ['object-fit' => 'cover'],
        'object-fill' => ['object-fit' => 'fill'],
        'object-none' => ['object-fit' => 'none'],
        'object-scale-down' => ['object-fit' => 'scale-down'],

        'object-top-left' => ['object-position' => 'top left'],
        'object-top' => ['object-position' => 'top'],
        'object-top-right' => ['object-position' => 'top right'],
        'object-left' => ['object-position' => 'left'],
        'object-center' => ['object-position' => 'center'],
        'object-right' => ['object-position' => 'right'],
        'object-bottom-left' => ['object-position' => 'bottom left'],
        'object-bottom' => ['object-position' => 'bottom'],
        'object-bottom-right' => ['object-position' => 'right'],

        'isolate' => ['isolation' => 'isolate'],
        'isolation-auto' => ['isolation' => 'auto'],

        /*
columns
break-after
break-before
break-inside
box-decoration-break
        */
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        if (preg_match('/^(position-)?(static|relative|absolute|fixed|sticky)$/', $utility, $matches)) {
            return ['position' => $matches[2]];
        }

        if (preg_match('/^z-(\d+|auto)$/', $utility, $matches)) {
            return ['z-index' => $matches[1]];
        }

        // Handle top, bottom, left, right with arbitrary values: top-[20px], bottom-[10%], left-[5rem], right-[2em]
        if (preg_match('/^(top|bottom|left|right)-\[(.+)\]$/', $utility, $matches)) {
            $prop = $matches[1];
            $value = $matches[2];

            return match ($prop) {
                'top' => ['top' => $value],
                'bottom' => ['bottom' => $value],
                'left' => ['left' => $value],
                'right' => ['right' => $value],
                'gap' => ['gap' => $value],
                default => null,
            };
        }

        // Handle top, bottom, left, right with arbitrary values: top-1, bottom-2, left-5, right-4
        if (preg_match('/^(top|bottom|left|right)-(\d+)$/', $utility, $matches)) {
            $prop = $matches[1] ?? '';
            $rem = ($matches[2] * 0.25) . 'rem';

            return match ($prop) {
                'top' => ['top' => $rem],
                'bottom' => ['bottom' => $rem],
                'left' => ['left' => $rem],
                'right' => ['right' => $rem],
                'gap' => ['gap' => $rem],
                default => null
            };
        }

        // Handle top, bottom, left, right with arbitrary values: top-1, bottom-2, left-5, right-4
        if (preg_match('/^aspect-(\d+)\/(\d+)$/', $utility, $matches)) {
            return ['aspect-ratio' => $matches[1] . ' / ' . $matches[2]];
        }

        return null;
    }
}
