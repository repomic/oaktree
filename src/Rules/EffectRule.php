<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;

class EffectRule implements RuleInterface
{
    public function handle(string $utility, Compiler $compiler): ?array
    {
        if (preg_match('/^shadow(-(2xl|xl|lg|md|sm|xs|2xs|none))(\/(\d+))?$/', $utility, $matches)) {
            $opacity = $matches[4] ?? null;

            return match ($matches[2]) {
                '2xl' => ['box-shadow' => '0 25px 25px -12px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . ')'],
                'xl' => ['box-shadow' => '0 20px 15px -5px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . '), 0 8px 10px -6px rgb(0 0 0 / 0.1)'],
                'lg' => ['box-shadow' => '0 10px 8px -3px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . '), 0 4px 6px -4px rgb(0 0 0 / 0.1)'],
                'md' => ['box-shadow' => '0 4px 6px -1px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . '), 0 2px 4px -2px rgb(0 0 0 / 0.1)'],
                'sm' => ['box-shadow' => '0 1px 3px 0 rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . '), 0 1px 2px -1px rgb(0 0 0 / 0.1)'],
                'xs' => ['box-shadow' => '0 1px 2px 0 rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . ')'],
                '2xs' => ['box-shadow' => '0 1px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . ')'],
                'none' => ['box-shadow' => 'none'],
                default => null
            };
        }

        if (preg_match('/^text-shadow(-(2xl|xl|lg|md|sm|xs|2xs|none))(\/(\d+))?$/', $utility, $matches)) {
            $opacity = $matches[4] ?? null;

            return match ($matches[2]) {
                '2xl' => ['text-shadow' => '0 25px 25px -12px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . ')'],
                'xl' => ['text-shadow' => '0 20px 15px -5px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . '), 0 8px 10px -6px rgb(0 0 0 / 0.1)'],
                'lg' => ['text-shadow' => '0 10px 8px -3px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . '), 0 4px 6px -4px rgb(0 0 0 / 0.1)'],
                'md' => ['text-shadow' => '0 4px 6px -1px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . '), 0 2px 4px -2px rgb(0 0 0 / 0.1)'],
                'sm' => ['text-shadow' => '0 1px 3px 0 rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . '), 0 1px 2px -1px rgb(0 0 0 / 0.1)'],
                'xs' => ['text-shadow' => '0 1px 2px 0 rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . ')'],
                '2xs' => ['text-shadow' => '0 1px rgb(0 0 0 / ' . (($opacity ?? 50) / 100) . ')'],
                'none' => ['text-shadow' => 'none'],
                default => null
            };
        }

        if (preg_match('/^opacity-(\d+)?$/', $utility, $matches)) {
            $value = (int)$matches[1];

            return ['opacity' => $value / 100];
        }

        if ($utility === 'drop-shadow') {
            return ['filter' => 'drop-shadow(0 0px 6px rgba(0, 0, 0, 0.2));'];
        }

        return null;
    }
}
