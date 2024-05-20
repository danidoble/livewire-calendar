<?php

namespace Danidoble\LivewireCalendar\Facades;

use Illuminate\Support\Facades\Facade;

class LivewireCalendarFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'livewire-calendar';
    }

    public static function scripts(): string
    {
        return app('livewire-calendar')->scripts();
    }
}