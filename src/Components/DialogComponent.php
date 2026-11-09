<?php

namespace Oaktree\Components;

use Oaktree\Compiler;
use Oaktree\Css;
use Oaktree\Rules\RuleInterface;

class DialogComponent implements RuleInterface
{
    protected $static = [
        'drawer' => [
            '@extend:border-0',
            '@extend:rounded-none',
            '@extend:shadow-lg',
            '@extend:z-100000',
            '@extend:bg-white',
            '@extend:p-8',
            '@extend:backdrop:backdrop-blur-3',
            '@extend:backdrop:backdrop-bg-[#000000/.1]',
            /* Layout */
            '@extend:flex',
            '@extend:flex-col',
            /* Dimensions & positioning */
            '@extend:fixed',
            'height' => '100vh',
            'max-width' => '100vw',
            'max-height' => '100vh',
            'inset' => '0 0 0 auto', /* Anchors to the right edge (top: 0, bottom: 0, right: 0, left: auto) */
            'margin' => '0',
            /* Off-screen initial state */
            'transform' => 'translateX(100%)',
            'opacity' => '0',

            /* Transition setup */
            'transition' => 'transform 0.15s ease-in-out,
                             opacity 0.15s ease-in-out, 
                             display 0.15s ease-in-out allow-discrete,
                             overlay 0.15s ease-in-out allow-discrete',

            '& > div' => [
                'max-height' => 'calc(100vh - 100px) !important;',
            ],

            /* Open state when shown via showModal() */
            '&[open]' => [
                'transform' => 'translateX(0)',
                'opacity' => '1',
            ],

            /* Starting style for open animation transition */
            '@starting-style' => [
                '&[open]' => [
                    'transform' => 'translateX(100%)',
                    'opacity' => '0',
                ],
                '&[open]::backdrop' => [
                    'opacity' => '0',
                ],
            ],

            /* Backdrop styling & transition */
            '&::backdrop' => [
                'opacity' => '0',
                'transition' => 'opacity 0.15s ease-in-out,
                                 display 0.15s ease-in-out allow-discrete,
                                 overlay 0.15s ease-in-out allow-discrete',
            ],
            '&[open]::backdrop' => [
                'opacity' => '1',
            ],

            '@media (width < 768px)' => [
                '&' => [
                    '@extend:fixed',
                    '@extend:w-full',
                    '@extend:block',
                    'left' => '0px',
                    'bottom' => '0px',
                    'right' => '0px',
                    'margin' => '0px',
                    'max-width' => 'unset',
                    'max-height' => 'unset',
                    'top' => 'unset',
                    'transform' => 'translateY(100%)',
                    'transition' => 'transform .2s',
                ],
                '&[open]' => [
                    'transform' => 'translateY(0)',
                ],
            ],
        ],
    ];

    public function handle(string $utility, Compiler $compiler): array|Css|null
    {
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        return null;
    }
}
