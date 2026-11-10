# Oaktree CSS for Laravel

Oaktree is a Laravel-friendly utility CSS framework that lets you style applications directly in markup without writing custom CSS for every component.

It scans your Blade, Vue, and JavaScript files for utility classes, compiles only the classes you actually use, and emits a single `public/css/oaktree.css` stylesheet. The goal is to feel familiar to utility-first developers while staying light and PHP-native.

## Why Oaktree?

- Utility classes in your templates, not a separate CSS authoring workflow
- Compiles from real project markup, so you only ship used CSS
- Designed for Laravel projects and works naturally with Blade views
- Includes common layout, spacing, sizing, typography, border, background, form, and interaction utilities
- Ships with a simple command-line build/watch workflow

## Requirements

- PHP 8.2+
- Laravel 13+

## Installation

Install the package in your Laravel app:

```bash
composer require repomic/oaktree
```

Register the service provider if package discovery is not enabled in your application:

```php
// bootstrap/providers.php
return [
    // ...
    Oaktree\ServiceProvider::class,
];
```

The package also registers the `css:build` artisan command and pushes a JIT compile middleware into the `web` middleware group for local development.

## Configuration

The default package config is:

```php
<?php

return [
    'paths' => [
        resource_path('views'),
        resource_path('js'),
        public_path('js'),
    ],
    'extensions' => ['php', 'blade.php', 'vue', 'js', 'html'],
];
```

You can adjust the directories scanned for utility classes or the file extensions used during extraction.

## Build CSS

Compile the stylesheet once:

```bash
php artisan css:build
```

Watch for changes while developing:

```bash
php artisan css:build --watch
```

This writes the generated CSS to:

```text
public/css/oaktree.css
```

## Usage

Add utilities directly to your HTML elements:

```blade
<div class="container mx-auto px-6 py-10">
    <h1 class="text-4xl font-bold text-slate-900">Welcome</h1>
    <button class="btn-primary mt-6 rounded-lg px-5 py-3">Get started</button>
</div>
```

The library includes utilities such as:

- spacing: `p-4`, `mt-6`, `gap-2`, `space-y-4`
- sizing: `w-full`, `max-w-lg`, `h-12`
- typography: `text-sm`, `font-bold`, `leading-tight`
- colors: `bg-primary`, `text-red-500`, `border-slate-200`
- layout: `flex`, `grid`, `col-span-3`, `container-md`
- forms: `form-text`, `form-select`, `form-checkbox`
- interactions: `hover:opacity-100`, `cursor-pointer`, `transition-colors`

## Example CSS import

In your app CSS bundle:

```css
@import "oaktree.css";
```

## Development notes

The package is intentionally lightweight and keeps generation logic in PHP. It can be extended by adding more utility rules and component definitions under the `src/Rules` and `src/Components` directories.

## License

MIT
