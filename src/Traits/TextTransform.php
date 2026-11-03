<?php

namespace Oaktree\Traits;

trait TextTransform
{
    public function toSnakeCase(string $value): string
    {
        $value = preg_replace('/([a-z\d])([A-Z])/', '$1_$2', trim($value)) ?? $value;
        $value = preg_replace('/[^\pL\pN]+/u', '_', $value) ?? $value;

        return strtolower(trim($value, '_'));
    }

    public function toKebabCase(string $value): string
    {
        return str_replace('_', '-', $this->toSnakeCase($value));
    }

    public function toCamelCase(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', trim($value));
        $value = ucwords(strtolower($value));

        return lcfirst(str_replace(' ', '', $value));
    }

    public function toStudlyCase(string $value): string
    {
        return ucfirst($this->toCamelCase($value));
    }

    public function toPascalCase(string $value): string
    {
        return preg_replace_callback(
            '/(^|[-_\s]+)([a-z0-9])/i',
            static fn(array $match): string => strtoupper($match[2]),
            trim($value)
        );
    }
}
