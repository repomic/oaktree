<?php

declare(strict_types=1);

namespace Oaktree\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;

final class JitCompile
{
    private const string LOCK_KEY = 'oaktree:jit-compile';

    private const int LOCK_TTL_SECONDS = 30;

    private const int LOCK_WAIT_SECONDS = 10;

    protected bool $buildOnReload;

    /** @var array<int, string> */
    protected array $paths = [];

    /** @var array<int, string> */
    protected array $extensions = [];

    protected string $cachePath;

    public function __construct()
    {
        $this->buildOnReload = (bool)config('oaktree.build_on_reload', false);
        $this->paths = array_values(array_filter(config('oaktree.paths', []), is_dir(...)));
        $this->extensions = config('oaktree.extensions', []);
        $this->cachePath = storage_path('framework/_oaktree.txt');
    }

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!App::environment('local')) {
            return $next($request);
        }

        if ($this->shouldRebuild($request)) {
            $this->rebuild();
        }

        return $next($request);
    }

    protected function shouldRebuild(Request $request): bool
    {
        if ($this->buildOnReload) {
            return $this->isFullReloadRequested($request);
        }

        return $this->hasModifiedSourceFiles();
    }

    protected function hasModifiedSourceFiles(): bool
    {
        if ($this->paths === [] || $this->extensions === []) {
            return false;
        }

        $lastCompileTime = $this->getLastCompileTime();

        $files = Finder::create()
            ->files()
            ->in($this->paths)
            ->name(array_map(static fn(string $ext): string => "*.{$ext}", $this->extensions))
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        foreach ($files as $file) {
            if ($file->getMTime() > $lastCompileTime) {
                return true;
            }
        }

        return false;
    }

    protected function rebuild()
    {
        Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS)
            ->block(self::LOCK_WAIT_SECONDS, function (): void {
                // Re-check inside the lock: a concurrent request may have
                // already finished rebuilding while we were waiting.
                if (!$this->buildOnReload && !$this->hasModifiedSourceFiles()) {
                    return;
                }

                Artisan::call('css:build');
                file_put_contents($this->cachePath, time(), LOCK_EX);
            });
    }

    protected function getLastCompileTime(): int
    {
        return is_file($this->cachePath) ? (int)file_get_contents($this->cachePath) : 0;
    }

    protected function isFullReloadRequested(Request $request): bool
    {
        // Catches hard reloads (Ctrl+F5 / Cmd+Shift+R)
        return $request->header('Cache-Control') === 'no-cache'
            || $request->header('Pragma') === 'no-cache';
    }
}
