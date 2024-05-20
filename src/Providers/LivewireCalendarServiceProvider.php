<?php

declare(strict_types=1);

namespace Danidoble\LivewireCalendar\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class LivewireCalendarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'livewire-calendar');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../resources/views' => $this->app->resourcePath('views/vendor/livewire-calendar'),
            ], 'livewire-calendar');
        }
        $this->addDirectives();
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'livewire-calendar');
    }

    private function addDirectives(): void
    {
        Blade::directive('livewireCalendarScripts', function () {
            return view('livewire-calendar::Components.scripts')->render();
        });
    }
}
