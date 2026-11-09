<?php

namespace Oaktree\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Oaktree\Compiler;
use Oaktree\CssCompressor;
use Oaktree\Extractor;
use Throwable;

#[Signature('css:build {--w|watch : Watch files for changes}')]
#[Description('Build CSS using native PHP engine')]
class Build extends Command
{
    protected ?Compiler $compiler = null;

    public function handle(Extractor $extractor, Compiler $compiler, CssCompressor $compressor): int
    {
        $watch_paths = config('oaktree.paths', []);
        $extensions = config('oaktree.extensions', []);

        $is_watching = (bool)$this->option('watch');

        $last_hashes = [];
        $first_run = true;

        while (true) {
            try {
                $current_hashes = $extractor->getDirectoryState($watch_paths, $extensions);

                // Check if any file mtime or addition/deletion changed
                if ($current_hashes !== $last_hashes) {
                    $this->warn('[' . date('H:i:s') . ']' . (!empty($last_hashes) ? ' File change detected. ' : ' ') . 'Rebuilding CSS...');

                    $startTime = microtime(true);
                    $this->compile($extractor, $compiler, $compressor, $watch_paths, $extensions);
                    $elapsed = round((microtime(true) - $startTime) * 1000, 2);

                    $this->info("Done in {$elapsed}ms.");

                    $last_hashes = $current_hashes;

                    if ($is_watching && $first_run) {
                        $this->info('Watching for file changes... (Press Ctrl+C to stop)');
                    }
                }
            } catch (Throwable $e) {
                $this->error('Build failed: ' . $e->getMessage() . ' in ' . $e->getFile() . ' ln(' . $e->getLine() . ')');

                if (!$is_watching) {
                    return Command::FAILURE;
                }

                // Prevent infinite exception looping: update hashes if we captured them
                if (isset($current_hashes)) {
                    $last_hashes = $current_hashes;
                }
            }

            if (!$is_watching) {
                break;
            }

            $first_run = false;
            usleep(500000);
        }

        return Command::SUCCESS;
    }

    public function compile(Extractor $extractor, Compiler $compiler, CssCompressor $compressor, array $watch_paths, array $extensions): int
    {
        $tokens = $extractor->scan($watch_paths, $extensions);

        // dd(array_search('form-control-date', $tokens));
        $internal_normalize_css = $this->getOptionalFile($this->getPackagePath('resources/normalize.css'));
        $internal_preflight_css = $this->getOptionalFile($this->getPackagePath('resources/css/base.css'));

        $normalize_css = $this->getOptionalFile(resource_path('css/normalize.css'));
        $preflight_css = $this->getOptionalFile(resource_path('css/base.css'));

        $generated_css = $compiler->compile($tokens);

        $full_css = implode("\n\n", array_filter([
            $internal_normalize_css,
            $normalize_css,
            $internal_preflight_css,
            $preflight_css,
            $generated_css,
        ]));

        $output_path = public_path('css/oaktree.css');

        if (app()->environment('production')) {
            $full_css = $compressor->compress($full_css);
        }

        File::ensureDirectoryExists(dirname($output_path));
        File::put($output_path, $full_css);

        $this->info("Compiled CSS to {$output_path}");

        return Command::SUCCESS;
    }

    protected function getPackagePath(string $path): string
    {
        return realpath(__DIR__ . '/../../' . $path);
    }

    protected function getOptionalFile(string $path): string
    {
        return File::exists($path) ? File::get($path) : '';
    }
}
