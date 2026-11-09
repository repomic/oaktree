<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;

class TableRule implements RuleInterface
{
    protected array $static = [
        // Display Table family
        'table' => ['display' => 'table'],
        'inline-table' => ['display' => 'inline-table'],
        'table-cell' => ['display' => 'table-cell'],
        'table-column' => ['display' => 'table-column'],
        'table-column-group' => ['display' => 'table-column-group'],
        'table-row' => ['display' => 'table-row'],
        'table-row-group' => ['display' => 'table-row-group'],
        'table-header-group' => ['display' => 'table-header-group'],
        'table-footer-group' => ['display' => 'table-footer-group'],
        'table-caption' => ['display' => 'table-caption'],

        // Table Layout
        'table-auto' => ['table-layout' => 'auto'],
        'table-fixed' => ['table-layout' => 'fixed'],

        // Border Collapse
        'border-collapse' => ['border-collapse' => 'collapse'],
        'border-separate' => ['border-collapse' => 'separate'],

        // Caption Positioning
        'caption-top' => ['caption-side' => 'top'],
        'caption-bottom' => ['caption-side' => 'bottom'],

        // Empty Cells
        'empty-cells-show' => ['empty-cells' => 'show'],
        'empty-cells-hide' => ['empty-cells' => 'hide'],
    ];

    public function handle(string $utility, Compiler $compiler): ?array
    {
        // 1. Static mappings
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        // Cell Padding from Parent Table: cell-p-4, cell-px-2, cell-py-[10px]
        if (preg_match('/^cell-p(x|y|t|r|b|l)?-(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
            $dir = $matches[1] ?? '';
            $rawVal = $matches[2] !== '' ? $matches[2] : ($matches[3] ?? '');

            if ($rawVal === '') {
                return null;
            }

            $val = is_numeric($rawVal) ? ($rawVal * 0.25) . 'rem' : $rawVal;

            // Target direct table cells, rows' cells, or tbody/thead/tfoot descendants
            $cellTarget = '& > .table-cell, & > th, & > .table-row > .table-cell, & > tr > th, & > .table-row-group > .table-row > .table-cell, & > tbody > tr > th, & > thead > tr > th, & > tfoot > tr > td';

            $prop = match ($dir) {
                'x' => ['padding-left' => $val, 'padding-right' => $val],
                'y' => ['padding-top' => $val, 'padding-bottom' => $val],
                't' => ['padding-top' => $val],
                'r' => ['padding-right' => $val],
                'b' => ['padding-bottom' => $val],
                'l' => ['padding-left' => $val],
                '' => ['padding' => $val],
                default => null,
            };

            if (!$prop) {
                return null;
            }

            return [
                $cellTarget => $prop,
            ];
        }

        // 2. Border Spacing (for border-separate tables): border-spacing-2, border-spacing-x-4, border-spacing-[12px]
        if (preg_match('/^border-spacing-(?:(x|y)-)?(?:\[(.*)\]|(\d+))$/', $utility, $matches)) {
            $axis = $matches[1] ?? '';
            $rawVal = $matches[2] !== '' ? $matches[2] : ($matches[3] ?? '');

            if ($rawVal === '') {
                return null;
            }

            // Convert numeric scale values (e.g. 4 -> 1rem or 4px based on your sizing scale conventions)
            $value = is_numeric($rawVal) ? ($rawVal * 0.25) . 'rem' : $rawVal;

            return match ($axis) {
                'x' => ['--ot-border-spacing-x' => $value, 'border-spacing' => 'var(--ot-border-spacing-x, 0) var(--ot-border-spacing-y, 0)'],
                'y' => ['--ot-border-spacing-y' => $value, 'border-spacing' => 'var(--tlc-border-spacing-x, 0) var(--ot-border-spacing-y, 0)'],
                default => [
                    '--ot-border-spacing-x' => $value,
                    '--ot-border-spacing-y' => $value,
                    'border-spacing' => 'var(--ot-border-spacing-x, 0) var(--ot-border-spacing-y, 0)',
                ],
            };
        }

        return null;
    }
}
