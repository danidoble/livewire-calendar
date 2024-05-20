<?php

uses(\Danidoble\LivewireCalendar\Tests\TestCase::class);
use Danidoble\LivewireCalendar\LivewireCalendar;
use Livewire\Features\SupportTesting\Testable;
use Livewire\LivewireManager;

function createComponent($parameters = []): Testable
{
    return app(LivewireManager::class)->test(LivewireCalendar::class, $parameters);
}

test('can build component', function () {
    //Arrange
    //Act
    $component = createComponent([]);

    //Assert
    expect($component)->not->toBeNull();
});

test('can navigate to next month', function () {
    //Arrange
    $component = createComponent([]);

    //Act
    $component->runAction('goToNextMonth');

    //Assert
    expect($component->get('startsAt'))->toEqual(today()->startOfMonth()->addMonthNoOverflow())
        ->and($component->get('endsAt'))->toEqual(today()->endOfMonth()->startOfDay()->addMonthNoOverflow());

});

test('can navigate to previous month', function () {
    //Arrange
    $component = createComponent([]);

    //Act
    $component->runAction('goToPreviousMonth');

    //Assert
    expect($component->get('startsAt'))->toEqual(today()->startOfMonth()->subMonthNoOverflow())
        ->and($component->get('endsAt'))->toEqual(today()->endOfMonth()->startOfDay()->subMonthNoOverflow());

});

test('can navigate to current month', function () {
    //Arrange
    $component = createComponent([]);

    $component->runAction('goToPreviousMonth');
    $component->runAction('goToPreviousMonth');
    $component->runAction('goToPreviousMonth');

    //Act
    $component->runAction('goToCurrentMonth');

    //Assert
    expect($component->get('startsAt'))->toEqual(today()->startOfMonth())
        ->and($component->get('endsAt'))->toEqual(today()->endOfMonth()->startOfDay());

});
