<?php

use Oaktree\Extractor;

beforeEach(function (): void {
    $this->directory = sys_get_temp_dir() . '/oaktree-extractor-' . uniqid('', true);
    mkdir($this->directory . '/vendor', 0777, true);
    mkdir($this->directory . '/nested', 0777, true);
});

afterEach(function (): void {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($this->directory, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST,
    );

    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }

    rmdir($this->directory);
});

it('scans supported files and filters noise and excluded directories', function (): void {
    file_put_contents($this->directory . '/view.php', '<div class="flex md:hover:bg-red-500 p-[20px]">Hello</div>');
    file_put_contents($this->directory . '/nested/app.js', 'const className = "grid gap-4";');
    file_put_contents($this->directory . '/nested/inline.html', '<script>element.className = "animate-pop-in";</script>');
    file_put_contents($this->directory . '/ignored.min.js', 'const className = "should-not-appear";');
    file_put_contents($this->directory . '/vendor/package.php', '<div class="vendor-class"></div>');

    $tokens = (new Extractor)->scan([$this->directory], ['php', 'js', 'html']);

    expect($tokens)->toContain('flex', 'md:hover:bg-red-500', 'p-[20px]', 'grid', 'gap-4', 'animate-pop-in')
        ->not->toContain('should-not-appear', 'vendor-class');
});

it('returns empty for missing directories', function (): void {
    expect((new Extractor)->scan([$this->directory . '/missing'], ['php']))->toBe([]);
});

it('reports file mtimes for supported files', function (): void {
    file_put_contents($this->directory . '/view.php', 'ok');

    $state = (new Extractor)->getDirectoryState([$this->directory], ['php']);

    expect($state)->toHaveKey($this->directory . '/view.php')
        ->and($state[$this->directory . '/view.php'])->toBeInt();
});
