<?php

namespace Oaktree\Rules;

use Oaktree\Compiler;
use Oaktree\Css;
use Oaktree\Traits\TextTransform;

class FormRule implements RuleInterface
{
    use TextTransform;

    protected array $static = [
        'form-label' => [
            'font-size' => 'var(--ot-text-sm)',
            'margin-bottom' => 'var(--ot-spacing)',
            'display' => 'inline-block',

            '&::after' => [
                'content' => 'var(--optional, "(optional)")',
                'color' => 'var(--base-tint)',
                'margin-left' => '0.5rem',
                'font-weight' => 'normal',
            ],

            '&.no-optional::after' => [
                'display' => 'none',
            ],
            '&:has(~ [required]), &:has(~ * [required]), &:has(~ * * [required]), &.no-tag' => [
                '&::after' => [
                    'content' => 'unset',
                    'display' => 'unset',
                ],
            ],
        ],
        'form-help' => [
            'font-family' => 'monospace',
            'display' => 'inline-block',
            'font-size' => 'var(--ot-text-xs)',
            'line-height' => '1.25',
            'border-radius' => '100%',
            'width' => '1.25rem',
            'height' => '1.25rem',
            'background' => '#ddd',
            'font-weight' => 'bold',
            'text-align' => 'center',
            'margin-left' => '0.5rem',
            'vertical-align' => '0.1rem',
        ],
        'form-text' => [
            'font-size' => 'var(--ot-text-sm)',
            'line-height' => '1.25',
        ],
        'form-control' => [
            'width' => '100%',
            'padding' => '.5rem',
            'display' => 'inline-block',
            'font-size' => '1rem',
            'line-height' => '1.25rem',
            'font-weight' => '400',
            'appearance' => 'none',
            'background-clip' => 'padding-box',
            'border-radius' => '.375rem',
            'transition' => 'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out',
            'outline' => '0px none',
            'color' => 'var(--ot-base)',
            'background-color' => 'var(--ot-light)',
            'border' => '1px solid var(--ot-light-shade)',
            '-webkit-appearance' => 'none',
            '-moz-appearance' => 'none',

            '&.form-control-contrast' => [
                'background-color' => 'var(--ot-body)',
                'border-color' => 'var(--ot-medium)',
            ],

            '&:focus' => [
                'box-shadow' => '0 0 0 0.1rem rgba(10, 90, 209, 0.514)',
                'background-color' => 'var(--ot-body)',
            ],
            '&:read-only' => [
                'color' => 'var(--ot-base)',
            ],
            '&:disabled' => [
                'background-color' => 'var(--ot-light)',
                'color' => 'var(--ot-medium-tint)',
                '&::placeholder' => [
                    'color' => 'var(--ot-medium-tint)',
                ],
            ],
            '&[size]' => [
                'max-width' => 'calc(1rem * attr(size number, 1))',
            ],
            '&[type="time"]' => [
                'width' => 'auto',
            ],
            '&[autosize]' => [
                'resize' => 'none',
            ],
        ],
        'form-select' => [
            'display' => 'block',
            'width' => '100%',
            'padding' => '.5rem',
            'padding-right' => '2.25rem',
            'font-weight' => '400',
            'font-size' => '1rem',
            'line-height' => '1.25rem',
            'border-radius' => '.375rem',
            'border' => '1px solid var(--ot-light-shade)',
            'color' => 'var(--ot-base)',
            '-webkit-appearance' => 'none',
            '-moz-appearance' => 'none',
            'appearance' => 'none',
            'background-color' => 'var(--ot-light)',
            'background-image' => 'var(--ot-form-select-dd-icon)',
            'background-repeat' => 'no-repeat',
            'background-position' => 'right 0.75rem center',
            'background-size' => '16px 12px',
            'transition' => 'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out',
            'outline' => '0px none',

            '&:after' => [
                'display' => 'block',
                'clear' => 'both',
                'content' => '""',
            ],

            '&:focus' => [
                'box-shadow' => '0 0 0 0.1rem rgba(10, 90, 209, 0.514)',
            ],

            '&:disabled' => [
                'background-color' => 'var(--ot-light)',
                'color' => 'var(--ot-medium-tint)',

                '&::placeholder' => [
                    'color' => 'var(--ot-medium-tint)',
                ],
            ],

            '&.text-sm' => [
                'font-size' => '.8rem',
            ],
        ],
        'form-check-input' => [
            'width' => '1.25rem',
            'height' => '1.25rem',
            'min-width' => '1.25rem',
            'min-height' => '1.25rem',
            'vertical-align' => 'middle',
            'background-color' => 'var(--ot-light-tint)',
            'background-repeat' => 'no-repeat',
            'background-position' => 'center',
            'background-size' => 'contain',
            'border' => '1px solid rgba(0, 0, 0, 0.25)',
            '-webkit-appearance' => 'none',
            '-moz-appearance' => 'none',
            'appearance' => 'none',
            '-webkit-print-color-adjust' => 'exact',
            'print-color-adjust' => 'exact',

            '&[type="checkbox"]' => [
                'border-radius' => '.375rem',
            ],

            '&[type="radio"]' => [
                'border-radius' => '100%',
            ],

            '&:checked' => [
                'background-color' => 'var(--ot-primary)',
                'border-color' => 'var(--ot-primary)',
                'fill' => 'var(--ot-primary-contrast)',
                'color' => 'var(--ot-primary-contrast)',

                '&[type="checkbox"]' => [
                    'background-image' => 'var(--ot-form-checbox-icon)',
                ],

                '&[type="radio"]' => [
                    'background-image' => 'var(--ot-form-checbox-icon)',
                ],
            ],

            '&:disabled' => [
                'background-color' => 'var(--ot-medium-tint)',
                'border-color' => 'var(--ot-medium-tint)',
            ],
        ],

        'form-check-label' => [
            'position' => 'relative',
            'cursor' => 'pointer',
            'line-height' => '1.25rem',

            '&:has(div)' => [
                'display' => 'flex',
                'gap' => '0.5rem',
            ],

            'form-check-input' => [
                'margin-top' => '0',
            ],

            '&:has(~ [required]), &:has(~ * [required]), &:has(~ * * [required]), &.no-tag' => [
                '&::after' => [
                    'content' => 'unset',
                    'display' => 'unset',
                ],
            ],
        ],
        'form-control-date' => [
            '&::-webkit-calendar-picker-indicator' => [
                '-webkit-mask-image' => 'url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z"/></svg>\')',
                'mask-image' => 'url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z"/></svg>\')',
                '-webkit-mask-repeat' => 'no-repeat',
                'mask-repeat' => 'no-repeat',
                '-webkit-mask-position' => 'center',
                'mask-position' => 'center',

                'background-color' => 'var(--ot-primary)',
                'cursor' => 'pointer',
                'height' => '.8rem',
                'width' => '.8rem',

                '&:hover' => [
                    'background-color' => 'var(--ot-primary-shade)',
                ],
            ],
        ],
        'input-collection' => [
            'position' => 'relative',
            'display' => 'flex',
            'align-items' => 'stretch',
            'flex-wrap' => 'wrap',
        ],
        'input-group' => [
            'position' => 'relative',
            'display' => 'flex',
            'align-items' => 'stretch',
            // 'flex-wrap' => 'wrap',
            'border-radius' => '.375rem',

            '&:focus-within' => [
                'box-shadow' => '0 0 0 0.1rem rgba(10, 90, 209, 0.514)',
            ],

            '&:has(*[size])' => [
                'width' => 'fit-content',
                'max-width' => '100%',
            ],

            '&.d-grid' => [
                'display' => 'grid',

                '.form-check-label + .form-check-label' => [
                    'margin-left' => 'unset',
                ],
            ],

            '.form-control' => [
                'flex' => '1 1 fit-content',
                'width' => '1%',
                'min-width' => '0',
                'position' => 'relative',
                'box-shadow' => 'none',
            ],

            '.form-control + .input-group-text:has(+ .form-control)' => [
                'background-color' => 'var(--ot-base-contrast)',
                'border-left' => '0px none',
                'border-right' => '0px none',
            ],

            '.form-control + .form-select' => [
                'width' => 'max-content',
                'background-color' => 'var(--ot-dark-shade)',
                'z-index' => '10',
            ],

            '.form-select:has(+ .form-control)' => [
                'width' => 'max-content',
            ],

            '.input-group-text:has(+ .form-control)' => [
                'border-top-right-radius' => '0',
                'border-bottom-right-radius' => '0',
                'border-right-width' => '0',
            ],
            '.input-group-text:has(+ .form-control:focus)' => [
                'border-top-right-radius' => '0',
                'border-bottom-right-radius' => '0',
                'border-right-width' => '0',
                'background-color' => 'var(--ot-body)',
            ],
            '.form-control:has( + .input-group-text)' => [
                'border-top-right-radius' => '0',
                'border-bottom-right-radius' => '0',
                'border-right-width' => '0',
                'z-index' => '20',
            ],
            '.input-group-text + .form-control' => [
                'border-top-left-radius' => '0',
                'border-bottom-left-radius' => '0',
                'border-left-width' => '0',
            ],
            '.form-control + .input-group-text' => [
                'border-top-left-radius' => '0',
                'border-bottom-left-radius' => '0',
            ],
            '.form-control:focus + .input-group-text' => [
                'border-top-left-radius' => '0',
                'border-bottom-left-radius' => '0',
                'background-color' => 'var(--ot-body)',
            ],
            '&:has(input[type="date"]), &:has(input[type="time"])' => [
                'width' => 'fit-content',
            ],

            '*:not(.slider)' => [
                'box-shadow' => 'none',
                'outline' => '0px none !important',
            ],

            '*:last-child:not(.slider):not(:has(input[type="radio"])):not(:has(input[type="checkbox"])):not(:first-child),
                *:last-child:not(.form-datepicker):not(:has(input[type="radio"])):not(:has(input[type="checkbox"])):not(:first-child)' => [
                'border-top-left-radius' => '0',
                'border-bottom-left-radius' => '0',
                'border-right-width' => '1px',
                'border-left-width' => '0px',
            ],

            '.form-control-search:has(+ button)' => [
                'border-top-right-radius' => '0',
                'border-bottom-right-radius' => '0',
            ],
            '.form-control-search + button' => [
                'border-top-right-radius' => '.375rem',
                'border-bottom-right-radius' => '.375rem',
            ],
        ],
        'input-group-text' => [
            'padding' => 'var(--ot-spacing) calc(var(--ot-spacing) * 2)',
            'border-radius' => '.375rem',
            'font-size' => 'inherit',
            'line-height' => 'inherit',

            'display' => 'flex',
            'align-items' => 'center',
            'font-weight' => '400',
            'text-align' => 'center',
            'white-space' => 'nowrap',
            'z-index' => '10',
            'position' => 'relative',

            'color' => 'color-mix(in srgb, var(--ot-base), transparent 40%)',
            'background-color' => 'var(--ot-light)',
            'border' => '1px solid var(--ot-light-shade)',
        ],
    ];

    public function handle(string $utility, Compiler $compiler): array|Css|null
    {
        if (isset($this->static[$utility])) {
            return $this->static[$utility];
        }

        $pascal = $this->toPascalCase($utility);
        if (empty($pascal)) {
            return null;
        }

        $utility_name = 'handle' . $pascal;
        if (method_exists($this, $utility_name)) {
            return call_user_func([$this, $utility_name]);
        }

        return null;
    }

    public function _base()
    {
        return <<<'CSS'
        form {
            margin: 0;

            fieldset {
                margin: 0px;
                padding: 0px;
                border: 0px none;
                display: grid;
                gap: 1rem;
            }

            input,
            button,
            select,
            textarea {
                margin: 0;
                font-family: inherit;
                font-size: inherit;
                line-height: inherit;
            }

            textarea[autosize] {
                overflow: hidden;
                overflow-x: hidden;
                overflow-wrap: break-word;
                text-align: start;
                resize: none;
            }

            .field-label {
                display: block;
            }

            .form-label {
                font-size: 0.9rem;
                margin-bottom: .25rem;
                display: inline-block;

                &::after {
                    content: var(--optional, "(optional)");
                    color: var(--base-tint);
                    margin-left: 0.5rem;
                    font-weight: normal;
                }

                .form-help {
                    font-family: monospace;
                    display: inline-block;
                    font-size: 0.7rem;
                    line-height: 1rem;
                    border-radius: 100%;
                    width: 1rem;
                    height: 1rem;
                    background: #ddd;
                    font-weight: bold;
                    text-align: center;
                    margin-left: 0.5rem;
                    vertical-align: 0.1rem;
                }

                &.no-optional::after {
                    display: none;
                }
            }

            .form-text {
                font-size: 0.8rem;
                margin-top: 0.25;
            }

            .form-label:has(~ [required]),
            .form-label:has(~ * [required]),
            .form-label:has(~ * * [required]),
            .form-label.no-tag {
                &::after {
                    content: unset;
                    display: unset;
                }
            }

            .form-control {
                width: 100%;
                padding: .5rem;
                display: inline-block;;
                font-size: 1rem;
                line-height: 1rem;
                font-weight: 400;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                background-clip: padding-box;
                border-radius: .5rem;
                transition:
                    border-color 0.15s ease-in-out,
                    box-shadow 0.15s ease-in-out;
                outline: 0px none;

                color: var(--ot-base);
                background-color: var(--ot-light);
                border: 1px solid var(--ot-light);

                > textarea,
                > input {
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    appearance: none;
                    background: transparent;
                    border: 0px none;
                    padding: 0;
                    width: 100%;
                }

                &:focus {
                    box-shadow: 0 0 0 0.1rem rgba(10, 90, 209, 0.514);
                }

                &:read-only {
                    color: var(--ot-base);
                }

                &:disabled {
                    background-color: var(--ot-light);
                    color: var(--ot-medium-tint);

                    &::placeholder {
                        color: var(--ot-medium-tint);
                    }
                }

                &[size] {
                    max-width: attr(size rem);
                    max-width: calc(1rem * attr(size number, 1));
                }

                &[type="time"] {
                    width: auto;
                }
            }

            .form-control-search:has(+ button) {
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
            }

            .form-field-inlay {
                font-weight: 400;

                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                background-clip: padding-box;

                transition:
                    border-color 0.15s ease-in-out,
                    box-shadow 0.15s ease-in-out;
                outline: 0px none;

                color: var(--ot-base);
                background-color: var(--ot-light);
                border: 1px solid var(--ot-light);
                border-radius: .5rem;
                padding: .5rem;

                .form-label {
                    margin-bottom: .5rem;
                    font-weight: normal;
                    font-size: 0.8rem;
                }

                .form-control {
                    padding: 0;

                    &:focus {
                        box-shadow: none;
                    }
                }

                .input-group:focus-within {
                    box-shadow: none;
                }

                &:focus-within {
                    box-shadow: 0 0 0 0.1rem rgba(10, 90, 209, 0.514);
                }
            }

            .form-select {
                display: block;
                width: 100%;
                padding: .5rem;
                padding-right: 2.25rem;
                _padding: 0.375rem 2.25rem 0.375rem 0.75rem;
                font-weight: 400;
                font-size: 1rem;
                line-height: 1rem;
                border-radius: .5rem;
                color: var(--ot-base);
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                background-color: var(--ot-light);
                background-image:
                    var(--form-select-bg-img), var(--bs-form-select-bg-icon, none);
                background-repeat: no-repeat;
                background-position: right 0.75rem center;
                background-size: 16px 12px;
                border: 1px solid var(--ot-light);
                transition:
                    border-color 0.15s ease-in-out,
                    box-shadow 0.15s ease-in-out;
                outline: 0px none;

                &:after {
                    display: block;
                    clear: both;
                    content: "";
                }

                &:focus {
                    box-shadow: 0 0 0 0.1rem rgba(10, 90, 209, 0.514);
                }

                &:disabled {
                    background-color: var(--ot-light);
                    color: var(--ot-medium-tint);

                    &::placeholder {
                        color: var(--ot-medium-tint);
                    }
                }

                &.text-sm {
                    font-size: .8rem;
                }
            }

            .form-check-input {
                width: 1.2rem;
                height: 1.2rem;
                min-width: 1.2rem;
                min-height: 1.2rem;
                margin-top: 0.25em;
                vertical-align: top;
                background-color: var(--ot-light-tint);
                background-repeat: no-repeat;
                background-position: center;
                background-size: contain;
                border: 1px solid rgba(0, 0, 0, 0.25);
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;

                &[type="checkbox"] {
                    border-radius: .5rem;
                }

                &[type="radio"] {
                    border-radius: 100%;
                }

                &:checked {
                    background-color: var(--ot-primary);
                    border-color: var(--ot-primary);

                    &[type="checkbox"] {
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%238600ef' d='M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z'/%3E%3C/svg%3E");
                    }

                    &[type="radio"] {
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%238600ef' d='M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z'/%3E%3C/svg%3E");
                    }
                }

                &:disabled {
                    background-color: var(--ot-medium-tint);
                    border-color: var(--ot-medium-tint);
                }
            }

            .form-check-label {
                position: relative;
                _align-items: top;
                cursor: pointer;

                &:has(div) {
                    display: flex;
                    gap: 0.5rem;
                }

                .form-check-input {
                    margin-top: 0;
                }

                &:has(~ [required]),
                &:has(~ * [required]),
                &:has(~ * * [required]),
                &.no-tag {
                    &::after {
                        content: unset;
                        display: unset;
                    }
                }
            }

            .form-check-multiple {
                .form-check-label {
                    padding: 0.375rem 0.75rem;
                    border: 1px solid var(--ot-light);
                    border-radius: .5rem;
                    cursor: pointer;
                    position: relative;

                    &:has(div) {
                        display: flex;
                        gap: 0.5rem;
                    }

                    &:has(input:checked) {
                        padding: 0.3rem 0.675rem;
                        border-color: var(--ot-primary);
                        border-width: calc(1px + 0.075rem);
                        background-color: var(--ot-primary-tint);
                        background-color: color-mix(
                            in oklab,
                            var(--ot-primary-tint) 10%,
                            transparent
                        );
                    }

                    input {
                        float: right;
                    }
                }
            }

            .form-check-label + .form-check-label {
                margin-left: 1rem;
            }

            .form-switch {
                .form-switch-label {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    width: 100%;

                    .divider {
                        flex-grow: 1;
                        height: 1rem;
                        border-bottom: 2px dotted var(--ot-light);
                    }

                    .form-switch-input {
                        position: absolute;
                        opacity: 0;
                        width: 0;
                        height: 0;

                        & + .slider {
                            position: relative;
                            width: 3rem;
                            height: 1.5rem;
                            background-color: var(--ot-medium-tint);
                            transition: background-color 0.2s;
                            cursor: pointer;
                            margin-left: auto;
                            box-shadow: inset 0 0 2px rgba(0, 0, 0, 0.2);

                            &::before {
                                content: "";
                                position: absolute;
                                height: calc(1.5rem - 4px);
                                width: calc(1.5rem - 4px);
                                left: 2px;
                                bottom: 2px;
                                background-color: white;
                                border-radius: 50%;
                                transition: transform 0.2s;
                            }
                        }

                        &:checked + .slider {
                            background-color: var(--ot-primary);

                            &::before {
                                transform: translateX(1.5rem);
                            }
                        }
                    }
                }
            }

            .form-inline {
                display: flex;
                gap: 1rem;
                align-items: center;

                .form-select {
                    background-color: transparent;
                    background-image:
                        var(--form-select-bg-img), var(--bs-form-select-bg-icon, none);
                    background-position: right 0.05rem center;
                    padding: 0;
                    width: fit-content;
                    border: 0px none;
                    font-size: 0.9rem;
                    line-height: inherit;
                }

                .input-group .form-control {
                    width: fit-content;
                    font-size: 0.9rem;
                    line-height: inherit;
                }

                @supports (field-sizing: content) {
                    .form-select {
                        field-sizing: content;
                        padding: 0 1.05rem 0 0;
                    }
                }

                .input-group {
                    display: inline;
                    width: unset;
                }
            }

            .form-datepicker {
                width: 100%;
                padding: .25rem;
                display: inline-block;;
                font-size: 1rem;
                line-height: 1rem;
                border-radius: .5rem;
                font-weight: 400;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                background-clip: padding-box;
                transition:
                    border-color 0.15s ease-in-out,
                    box-shadow 0.15s ease-in-out;
                outline: 0px none;

                color: var(--ot-base);
                background-color: var(--ot-light);
                border: 1px solid var(--ot-light);
            }

            .d-grid
                .form-check-label
                + .form-check-label
                .stacked
                .form-check-label
                + .form-check-label {
                margin-left: unset;
            }

            textarea.form-control {
                min-height: calc(1.5em + 0.75rem + calc(1px * 2));
            }

            textarea {
                resize: vertical;
            }

            .input-collection {
                position: relative;
                display: flex;
                align-items: stretch;
                width: 100%;
                flex-wrap: wrap;

                .input-group {
                    width: auto;
                }
            }

            .input-group {
                position: relative;
                display: flex;
                align-items: stretch;
                flex-wrap: wrap;

                .input-group {
                    width: auto;
                }

                &.d-grid {
                    display: grid;

                    .form-check-label + .form-check-label {
                        margin-left: unset;
                    }
                }

                &.stacked {
                    gap: 0.2rem;
                    flex-flow: column;

                    .form-check-label + .form-check-label {
                        margin-left: unset;
                    }
                }

                &:has(input[type="date"]) {
                    width: fit-content;
                }

                .input-group-text {
                    padding: .25rem;
                    border-radius: .5rem;
                    font-size: 1rem;
                    line-height: inherit;

                    display: flex;
                    align-items: center;
                    font-weight: 400;
                    text-align: center;
                    white-space: nowrap;
                    z-index: 10;
                    position: relative;

                    color: color-mix(in srgb, var(--ot-base), transparent 40%);
                    background-color: var(--ot-body-shade);
                    border: 1px solid var(--ot-body-shade);
                }

                .input-group-subtext {
                    font-size: .8rem;
                    color: var(--ot-light);
                    display: block;
                }

                .form-control {
                    flex: 1 1 auto;
                    width: 1%;
                    min-width: 0;
                    position: relative;
                    box-shadow: none;
                }

                .form-control-date,
                .form-control-time {
                    flex: 0 0 fit-content;
                }

                .form-control:has(+ .input-button),
                .form-control:has(+ .input-group-text) {
                    border-bottom-right-radius: 0px;
                    border-top-right-radius: 0px;
                }

                .form-control + .input-button,
                .form-control + .input-group-text {
                    border-bottom-left-radius: 0px;
                    border-top-left-radius: 0px;
                }

                .form-control + .input-group-text:has(+ .form-control) {
                    background-color: var(--ot-base-contrast);
                    border-left: 0px none;
                    border-right: 0px none;
                }

                .form-control + .form-select {
                    width: max-content;
                    background-color: var(--ot-dark-shade);
                    z-index: 10;
                }

                .form-select:has(+ .form-control) {
                    width: max-content;
                }

                &:focus-within {
                    border-radius: .5rem;
                    box-shadow: 0 0 0 0.1rem rgba(10, 90, 209, 0.514);
                }

                &:has(*[size]) {
                    width: fit-content;
                    max-width: 100%;
                }

                &:has(input[type="radio"]:focus),
                &:has(input[type="checkbox"]:focus) {
                    box-shadow: unset;
                }

                *:not(.slider) {
                    box-shadow: none;
                    outline: 0px none !important;
                }

                *:last-child:not(.slider):not(:has(input[type="radio"])):not(:has(input[type="checkbox"])):not(:first-child),
                *:last-child:not(.form-datepicker):not(:has(input[type="radio"])):not(:has(input[type="checkbox"])):not(:first-child) {
                    border-radius: .5rem;
                    border-top-left-radius: 0;
                    border-bottom-left-radius: 0;
                    border-right-width: 1px;
                }
            }

            .input-group.rounded-pill {
                &:focus-within {
                    border-radius: 100rem;
                }

                .form-control {
                    border-radius: 100rem;
                }

                .form-control:has(+ *) {
                    border-radius: 100rem 0 0 100rem;
                }

                *:last-child:not(input[type="radio"]):not(input[type="checkbox"]) {
                    border-radius: 0 100rem 100rem 0;
                }
            }

            .invalid-feedback {
                margin-top: 0.25rem;
                line-height: 1.3;
                font-size: 0.85rem;
                border: 0px none;
                color: red;
            }

            .all-optional .form-label::after {
                content: var(--optional, "(optional)");
                display: none;
            }
        }
        CSS;

        return 'form { background: red; }';
    }
}
