<?php

namespace Danidoble\LivewireCalendar\Tests;

use Danidoble\LivewireCalendar\Providers\LivewireCalendarServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseCase;
use Random\RandomException;

class TestCase extends BaseCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewireCalendarServiceProvider::class,
        ];
    }

    /**
     * @throws RandomException
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }
}
