<?php

namespace Oaktree\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Finder\Finder;

class JitCompile
{
    protected $build_when = 'reload';

    public function handle(Request $request, Closure $next)
    {
        $watch_paths = config('oaktree.paths', []);
        $extensions = config('oaktree.extensions', []);

        if (App::environment('local')) {
            $cacheFile = storage_path('framework/_oaktree.txt');

            if ($this->build_when === 'reload') {
                if ($this->isFullReloadRequested($request)) {
                    Artisan::call('css:build');
                    file_put_contents($cacheFile, time());
                }
            } else {
                $lastCompileTime = file_exists($cacheFile) ? (int)file_get_contents($cacheFile) : 0;

                if ($this->shouldRecompile($watch_paths, $extensions, $lastCompileTime)) {
                    Artisan::call('css:build');
                    file_put_contents($cacheFile, time());
                }
            }
        }

        return $next($request);
    }

    protected function isFullReloadRequested(Request $request): bool
    {
        // Catches hard reloads (Ctrl+F5 / Cmd+Shift+R)
        if ($request->header('Cache-Control') === 'no-cache' || $request->header('Pragma') === 'no-cache') {
            return true;
        }

        // Catches standard browser page reloads / initial document loads via GET
        return $request->isMethod('GET')
            && $request->header('Sec-Fetch-Mode') === 'navigate'
            && $request->header('Sec-Fetch-Dest') === 'document';
    }

    public function shouldRecompile(array $paths, array $extensions, int $lastCompileTime): bool
    {
        $finder = new Finder;
        $finder->files()
            ->in($paths)
            ->name(array_map(fn($ext) => '*.' . $ext, $extensions))
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        foreach ($finder as $file) {
            if ($file->getMTime() > $lastCompileTime) {
                return true;
            }
        }

        return false;
    }
}
