<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Traits\ArbitraryValues;
use Oaktree\Traits\ColorTransform;

class FlexboxGridRule implements RuleInterface
{
    use ArbitraryValues, ColorTransform;

    protected array $static = [
        'flex-wrap' => ['flex-wrap' => 'wrap'],
        'flex-nowrap' => ['flex-wrap' => 'nowrap'],
        'flex-direction-row' => ['flex-direction' => 'row'],
        'flex-direction-col' => ['flex-direction' => 'column'],
        'flex-row' => ['flex-direction' => 'row'],
        'flex-col' => ['flex-direction' => 'column'],

        'justify-start' => ['justify-content' => 'flex-start'],
        'justify-end' => ['justify-content' => 'flex-end'],
        'justify-center' => ['justify-content' => 'center'],
        'justify-between' => ['justify-content' => 'space-between'],

        'justify-items-start' => ['justify-items' => 'start'],
        'justify-items-end' => ['justify-items' => 'end'],
        'justify-items-center' => ['justify-items' => 'center'],
        'justify-items-stretch' => ['justify-items' => 'stretch'],

        'justify-self-auto' => ['justify-self' => 'auto'],
        'justify-self-start' => ['justify-self' => 'start'],
        'justify-self-end' => ['justify-self' => 'end'],
        'justify-self-center' => ['justify-self' => 'center'],
        'justify-self-stretch' => ['justify-self' => 'stretch'],

        'justify-content-auto' => ['justify-content' => 'auto'],
        'justify-content-start' => ['justify-content' => 'start'],
        'justify-content-end' => ['justify-content' => 'end'],
        'justify-content-left' => ['justify-content' => 'left'],
        'justify-content-right' => ['justify-content' => 'right'],
        'justify-content-center' => ['justify-content' => 'center'],
        'justify-content-stretch' => ['justify-content' => 'stretch'],
        'justify-content-around' => ['justify-content' => 'space-around'],
        'justify-content-between' => ['justify-content' => 'space-between'],
        'justify-content-evenly' => ['justify-content' => 'space-evenly'],

        'align-items-start' => ['align-items' => 'flex-start'],
        'align-items-end' => ['align-items' => 'flex-end'],
        'align-items-center' => ['align-items' => 'center'],
        'align-items-baseline' => ['align-items' => 'baseline'],
        'align-items-stretch' => ['align-items' => 'stretch'],

        'align-content-start' => ['align-content' => 'flex-start'],
        'align-content-end' => ['align-content' => 'flex-end'],
        'align-content-center' => ['align-content' => 'center'],
        'align-content-between' => ['align-content' => 'space-between'],
        'align-content-around' => ['align-content' => 'space-around'],
        'align-content-stretch' => ['align-content' => 'stretch'],

        'align-self-auto' => ['align-self' => 'auto'],
        'align-self-start' => ['align-self' => 'flex-start'],
        'align-self-end' => ['align-self' => 'flex-end'],
        'align-self-center' => ['align-self' => 'center'],
        'align-self-stretch' => ['align-self' => 'stretch'],

        'flex-1' => ['flex' => '1 1 0%'],
        'flex-auto' => ['flex' => '1 1 auto'],
        'flex-initial' => ['flex' => '0 1 auto'],
        'flex-full' => ['flex' => '1 1 100%'],
        'flex-none' => ['flex' => 'none'],

        'col-auto' => ['grid-column' => 'auto'],
        'col-span-all' => ['grid-column' => '1 / -1'],

        'items-start' => ['align-items' => 'flex-start'],
        'items-center' => ['align-items' => 'center'],
        'items-end' => ['align-items' => 'flex-end'],

        'column-rule-solid' => ['column-rule-style' => 'solid'],
        'column-rule-dashed' => ['column-rule-style' => 'dashed'],
        'column-rule-dotted' => ['column-rule-style' => 'dotted'],
        'column-rule-double' => ['column-rule-style' => 'double'],
        'column-rule-none' => ['column-rule-style' => 'none'],

        'order-none' => ['order' => 'unset'],
        'order-first' => ['order' => '-9999'],
        'order-last' => ['order' => '9999'],

        'grid-flow-column' => ['grid-auto-flow' => 'column'],
        /*
columns
break-after
break-before
break-inside
box-decoration-break
box-sizing
isolation
object-fit
object-position
overflow
overscroll-behavior
visibility
        */
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        if (preg_match('/^(grid-)?cols-(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
            if (!empty($matches[2])) {
                return ['grid-template-columns' => $this->normalizeValue($matches[2])];
            }

            return ['grid-template-columns' => "repeat({$matches[3]}, minmax(0, 1fr))"];
        }

        if (preg_match('/^col-span-(\d+)$/', $utility, $matches)) {
            return ['grid-column-start' => "span {$matches[1]}"];
        }

        if (preg_match('/^(grid-)?rows-(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
            if (!empty($matches[2])) {
                return ['grid-template-rows' => $this->normalizeValue($matches[2])];
            }

            return ['grid-template-rows' => "repeat({$matches[3]}, minmax(0, 1fr))"];
        }

        if (preg_match('/^(row-span)-(\d+)$/', $utility, $matches)) {
            return ['grid-row' => "span {$matches[2]} / span {$matches[2]}"];
        }

        if (preg_match('/^(col|row)-(start|end)-(\d+|none|initial|auto)$/', $utility, $matches)) {
            $dir = $matches[1] == 'row' ? 'row' : 'column';
            $pos = $matches[2] == 'end' ? 'end' : 'start';

            return ['grid-' . $dir . '-' . $pos => $matches[3]];
        }

        if (preg_match('/^(row|col)-(\d+|none|initial|auto)$/', $utility, $matches)) {
            $dir = $matches[1] == 'row' ? 'row' : 'column';

            return ['grid-' . $dir => $matches[2]];
        }

        /*if (preg_match('/^grid-padding-(\d+|none)$/', $utility, $matches)) {
            return ['&.d-grid > *' => ['padding' => '.5rem']];
        }*/

        if (preg_match('/^flex(-(grow|shrink|basis))?-(\d+)$/', $utility, $matches)) {
            $prop = match ($matches[2]) {
                'grow' => 'flex-grow',
                'shrink' => 'flex-shrink',
                'basis' => 'flex-basis',
                default => 'flex',
            };

            if (!$prop) {
                return null;
            }

            $value = $matches[2] === 'basis' ? "{$matches[3]}px" : $matches[3];

            return [$prop => $value];
        }

        if (preg_match('/^(-)?order-(\d+)$/', $utility, $matches)) {
            if ($matches[1] === '-') {
                return ['order' => $matches[2] * -1];
            }

            return ['order' => $matches[2]];
        }

        if (preg_match('/^(gap(?:-[xy])?)-(?:\[(.+)\]|(\d+))$/', $utility, $matches, PREG_UNMATCHED_AS_NULL)) {
            $prop = $matches[1];
            $value = isset($matches[2]) ? $matches[2] : (($matches[3] * 0.25) . 'rem');

            return match ($prop) {
                'gap' => ['gap' => $value],
                'gap-x' => ['column-gap' => $value],
                'gap-y' => ['row-gap' => $value],
                default => null,
            };
        }

        if (preg_match('/^(grid-border-row)-(\d+)$/', $utility, $matches)) {
            return [
                // 'declarations' => ['grid-row-gap' => ($matches[2] * 0.25) . 'rem'],
                '& > *' => ['position' => 'relative'],
                '& > *:nth-child(n+2)::before' => [
                    'height' => ($matches[2] / 10) . 'rem',
                    'content' => '""',
                    'display' => 'block',
                    'position' => 'absolute',
                    'top' => '0px',
                    'left' => '0px',
                    'width' => '100%',
                    'background-color' => 'var(--ot-medium-tint)',
                ],
            ];
        }

        if (preg_match('/^grid-border-([a-z0-9\-]+)$/', $utility, $matches)) {
            $colorRaw = $matches[1];

            $rgb = $this->resolveColorName($colorRaw);

            if (!$rgb) {
                return null;
            }

            return [
                '& > *::before' => [
                    'background-color' => "{$rgb}",
                ],
            ];
        }

        return null;
    }
}
