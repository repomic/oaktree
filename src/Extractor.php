<?php

namespace Oaktree;

use Symfony\Component\Finder\Finder;

class Extractor
{
    private const CANDIDATE_PATTERN = '~' .
        '(?<=\s|[\'"\`<>]|^)' .               // Boundary start
        '(?:[a-zA-Z0-9_\-\.\/%\#\(\)]+:)*' .  // Variants (e.g., hover:, focus:, has(.form-invalid):)
        '!*' .                                // Important prefix (!)
        '-?' .                                // Negative prefix (-)
        '[a-zA-Z0-9_\-\.\/%\#]+' .            // Core utility (form-control-date)
        '(?:\[[^\]\s]+\])?' .                 // Arbitrary values ([#123])
        '(?:\/[a-zA-Z0-9_\-\.\/%\#%\[\]]+)?' . // Opacity/fraction modifiers (/80, /50)
        '(?=\s|[\'"\`>]|$)' .                 // Boundary end
        '~';

    public function scan(array $paths, array $extensions, array $excludeDirs = []): array
    {
        // Filter existing directories upfront to prevent Finder exceptions
        $validPaths = array_filter($paths, 'is_dir');
        if (empty($validPaths)) {
            return [];
        }

        $patterns = array_map(fn($ext) => '*.' . $ext, $extensions);

        $finder = new Finder;
        $finder->files()
            ->in($validPaths)
            ->name($patterns)
            ->exclude(array_merge([
                'tinymce',
                'vendor',
                'node_modules',
                'dist',
                'build',
            ], $excludeDirs))
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->ignoreUnreadableDirs(true);

        $candidates = [];

        foreach ($finder as $file) {

            // 1. Skip files over 250KB (Utility classes are written in source files, not huge bundles/libraries)
            if ($file->getSize() > 500 * 1024) {
                continue;
            }

            // 2. Skip minified files explicitly
            if (str_contains($file->getFilename(), '.min.')) {
                continue;
            }

            $content = $file->getContents();

            $content = preg_replace('/<\?php.*?\?>/s', '', $content);

            // Skip empty or tiny files immediately
            if (strlen($content) < 2) {
                continue;
            }

            preg_match_all(self::CANDIDATE_PATTERN, $content, $matches);

            foreach ($matches[0] as $match) {
                $cleaned = $this->sanitizeCandidate($match);

                if ($this->isValidCandidate($cleaned)) {
                    $candidates[$cleaned] = true; // Use array keys for fast deduplication
                }
            }
        }

        return array_keys($candidates);
    }

    /**
     * Cleans up edge-case trailing/leading tokens captured by greedy regex.
     */
    private function sanitizeCandidate(string $candidate): string
    {
        // Trim leading/trailing quotes or punctuation accidentally captured
        $candidate = trim($candidate, '\'"\`()');

        // Strip trailing colons or backslashes
        $candidate = rtrim($candidate, ':\\');

        return $candidate;
    }

    /**
     * Filters out obvious false positives like standard HTML tags, attribute names, etc.
     */
    private function isValidCandidate(string $candidate): bool
    {
        if ($candidate === '' || strlen($candidate) < 2) {
            return false;
        }

        // Ignore common HTML elements or raw strings if captured in isolation
        $ignored = [
            'div',
            'span',
            'class',
            'className',
            'href',
            'type',
            'value',
            'style',
            'script',
            'input',
            'button',
            'form',
            'label',
            'path',
            'svg',
        ];

        if (in_array(strtolower($candidate), $ignored, true)) {
            return false;
        }

        // Validate bracket balance for arbitrary properties e.g., bg-[#123]
        $openBrackets = substr_count($candidate, '[');
        $closeBrackets = substr_count($candidate, ']');
        if ($openBrackets !== $closeBrackets) {
            return false;
        }

        return true;
    }

    public function getDirectoryState(array $paths, array $extensions): array
    {
        $state = [];
        $extMap = array_flip($extensions);

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                /** @var \SplFileInfo $file */
                if (isset($extMap[$file->getExtension()])) {
                    $state[$file->getPathname()] = $file->getMTime();
                }
            }
        }

        return $state;
    }
}
