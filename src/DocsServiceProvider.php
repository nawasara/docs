<?php

namespace Nawasara\Docs;

use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Illuminate\Support\Str;
use Nawasara\Docs\Support\ApiCatalog;
use Nawasara\Docs\Support\ComponentCatalog;
use Nawasara\Docs\Support\DocsNavigation;
use Nawasara\Docs\Support\GuideRenderer;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider;

class DocsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nawasara-docs');

        $this->registerComponents();
        $this->registerLivewire();
    }

    public function register(): void
    {
        // Katalog dipakai lintas halaman dalam satu request (indeks membaca
        // statistiknya, halaman detail membaca isinya), jadi singleton
        // menghindari pemindaian file yang sama berulang kali.
        $this->app->singleton(ComponentCatalog::class);
        $this->app->singleton(ApiCatalog::class);
        $this->app->singleton(GuideRenderer::class);
        $this->app->singleton(DocsNavigation::class);
        $this->app->singleton(\Nawasara\Docs\Support\ComponentPreviews::class);
        $this->app->singleton(\Nawasara\Docs\Support\ApiExamples::class);
    }

    /**
     * Daftarkan komponen anonim milik docs (shell, code).
     *
     * Dibungkus is_dir() — memanggil anonymousComponentPath dengan folder yang
     * tidak ada akan membuat `view:cache` gagal saat deploy, bukan saat
     * halaman dibuka.
     */
    protected function registerComponents(): void
    {
        $path = __DIR__.'/../resources/views/components';

        if (is_dir($path)) {
            Blade::anonymousComponentPath($path, 'nawasara-docs');
        }
    }

    public function registerLivewire(): void
    {
        $namespace = 'Nawasara\\Docs\\Livewire';
        $basePath = __DIR__.'/Livewire';

        if (! is_dir($basePath)) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in($basePath)->name('*.php');

        foreach ($finder as $file) {
            $relativePath = str_replace('/', '\\', $file->getRelativePathname());
            $class = $namespace.'\\'.Str::beforeLast($relativePath, '.php');

            if (class_exists($class)) {
                $alias = 'nawasara-docs.'.
                    Str::of($relativePath)
                        ->replace('.php', '')
                        ->replace('\\', '.')
                        ->replace('/', '.')
                        ->explode('.')
                        ->map(fn ($segment) => Str::kebab($segment))
                        ->join('.');

                Livewire::component($alias, $class);
            }
        }
    }
}
