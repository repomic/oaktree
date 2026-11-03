<?php

namespace Oaktree;

class CssCompressor
{
    public function compress(string $css): string
    {
        // 1. Strip comments (preserve /*! ... */)
        $css = preg_replace('#/\*(?!\!)(.*?)\*/#s', '', $css);

        // 2. Normalize & collapse whitespace
        $css = preg_replace('/\s+/', ' ', $css);

        // 3. Remove whitespace around structural characters
        $css = preg_replace('/\s*([{}|:;,>+~])\s*/', '$1', $css);

        // 4. Optimize numbers & zero units
        $css = preg_replace('/(?<=\s|:|,|\()0(?:\.0+)?(px|em|rem|%|pt|in|cm|mm|pc)/i', '0', $css);
        $css = preg_replace('/(?<=\s|:|,|\()0\.(\d+)/', '.$1', $css);

        // 5. Shorten 6-digit hex colors to 3-digit
        $css = preg_replace('/#([a-f0-9])\1([a-f0-9])\2([a-f0-9])\3\b/i', '#$1$2$3', $css);

        // 6. Strip unnecessary trailing semicolons before closing braces
        $css = str_replace(';}', '}', $css);

        return trim($css);
    }
}
