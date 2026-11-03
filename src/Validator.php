<?php

namespace Oaktree;

class Validator
{
    /**
     * Validates if a string is a syntactically valid CSS class name.
     *
     * * @param string $className The class name to test (without the leading dot)
     */
    public static function isValid(string $className): bool
    {
        // 1. Length check: Must be between 1 and 4096 characters
        $length = strlen($className);
        if ($length === 0 || $length > 4096) {
            return false;
        }

        /**
         * CSS Identifier Syntax Rules (W3C):
         * - Can contain a-z, A-Z, 0-9, hyphens (-), underscores (_), and non-ASCII characters (U+00A0 and above).
         * - Can contain valid hex escapes (e.g., \26, \000026) or escaped characters (e.g., \:).
         * - Cannot start with an unescaped digit.
         * - Cannot start with two hyphens (reserved) unless escaped.
         * - Cannot start with a hyphen followed by a digit.
         */
        $hexEscape = '\\[0-9a-fA-F]{1,6}\s?';
        $charEscape = '\\\\[^\r\n\f0-9a-fA-F]';
        $escape = "(?:{$hexEscape}|{$charEscape})";

        $nmstart = "(?:[_a-zA-Z]|[\x{00A0}-\x{10FFFF}]|{$escape})";
        $nmchar = "(?:[_a-zA-Z0-9-]|[\x{00A0}-\x{10FFFF}]|{$escape})";

        // Invalid start checks (2 hyphens, or hyphen + digit)
        if (preg_match('/^-{2}/', $className) || preg_match('/^-[0-9]/', $className)) {
            return false;
        }

        $pattern = "/^{$nmstart}{$nmchar}*$/u";

        return preg_match($pattern, $className) === 1;
    }

    /**
     * Strict validator for standard project conventions (no escapes, clean class names).
     * Matches typical class names like: "btn", "nav-link_active", "bg-red-500", "col-12".
     */
    public static function isValidStrict(string $className): bool
    {
        // Must start with letter/underscore, followed by letters, digits, hyphens, or underscores
        return preg_match('/^[_a-zA-Z][a-zA-Z0-9_-]*$/', $className) === 1;
    }
}
