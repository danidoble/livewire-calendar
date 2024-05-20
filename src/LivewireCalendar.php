<?php

namespace Danidoble\LivewireCalendar;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Throwable;

class LivewireCalendar extends Component
{
    public Carbon $startsAt;

    public Carbon $endsAt;

    public Carbon|CarbonInterface $gridStartsAt;

    public Carbon|CarbonInterface $gridEndsAt;

    public ?int $weekStartsAt = null;

    public ?int $weekEndsAt = null;

    public ?string $calendarView = null;

    public ?string $dayView = null;

    public ?string $eventView = null;

    public ?string $dayOfWeekView = null;

    public ?string $dragAndDropClasses = null;

    public ?string $beforeCalendarView = null;

    public ?string $afterCalendarView = null;

    public ?int $pollMillis = null;

    public ?string $pollAction = null;

    public bool $dragAndDropEnabled = true;

    public bool $dayClickEnabled = true;

    public bool $eventClickEnabled = true;

    public bool $hideWeekends = false;

    public bool $defaultHeading = false;

    protected array $casts = [
        'startsAt' => 'date',
        'endsAt' => 'date',
        'gridStartsAt' => 'date',
        'gridEndsAt' => 'date',
    ];

    public function mount(
        $initialYear = null,
        $initialMonth = null,
        $weekStartsAt = null,
        $calendarView = null,
        $dayView = null,
        $eventView = null,
        $dayOfWeekView = null,
        $dragAndDropClasses = null,
        $beforeCalendarView = null,
        $afterCalendarView = null,
        $pollMillis = null,
        $pollAction = null,
        $dragAndDropEnabled = true,
        $dayClickEnabled = true,
        $eventClickEnabled = true,
        $hideWeekends = false,
        $defaultHeading = false,
        $extras = []): void
    {
        $this->weekStartsAt = $weekStartsAt ?? Carbon::SUNDAY;
        $this->weekEndsAt = $this->weekStartsAt == Carbon::SUNDAY
            ? Carbon::SATURDAY
            : collect([0, 1, 2, 3, 4, 5, 6])->get($this->weekStartsAt + 6 - 7);

        $initialYear = $initialYear ?? Carbon::today()->year;
        $initialMonth = $initialMonth ?? Carbon::today()->month;

        $this->startsAt = Carbon::createFromDate($initialYear, $initialMonth, 1)->startOfDay();
        $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();

        $this->calculateGridStartsEnds();

        $this->defaultHeading = $defaultHeading ?? false;

        $this->setupViews($calendarView, $dayView, $eventView, $dayOfWeekView, $beforeCalendarView, $afterCalendarView);

        $this->setupPoll($pollMillis, $pollAction);

        $this->dragAndDropEnabled = $dragAndDropEnabled;
        $this->dragAndDropClasses = $dragAndDropClasses ?? 'border border-blue-400 border-4';

        $this->dayClickEnabled = $dayClickEnabled;
        $this->eventClickEnabled = $eventClickEnabled;

        $this->hideWeekends = $hideWeekends;

        $this->afterMount($extras);
    }

    public function afterMount($extras = [])
    {
        //
    }

    public function setupViews($calendarView = null,
        $dayView = null,
        $eventView = null,
        $dayOfWeekView = null,
        $beforeCalendarView = null,
        $afterCalendarView = null): void
    {
        $this->calendarView = $calendarView ?? 'livewire-calendar::calendar';
        $this->dayView = $dayView ?? 'livewire-calendar::day';
        $this->eventView = $eventView ?? 'livewire-calendar::event';
        $this->dayOfWeekView = $dayOfWeekView ?? 'livewire-calendar::day-of-week';

        $this->beforeCalendarView = $beforeCalendarView ?? null;

        if ($this->defaultHeading && $this->beforeCalendarView === null) {
            $this->beforeCalendarView = 'livewire-calendar::heading';
        }

        $this->afterCalendarView = $afterCalendarView ?? null;
    }

    public function setupPoll($pollMillis, $pollAction): void
    {
        $this->pollMillis = $pollMillis;
        $this->pollAction = $pollAction;
    }

    public function goToPreviousMonth(): void
    {
        $this->startsAt->subMonthNoOverflow()->startOfMonth()->startOfDay();
        $this->endsAt->subMonthNoOverflow()->endOfMonth()->startOfDay();

        $this->calculateGridStartsEnds();
    }

    public function goToNextMonth(): void
    {
        $this->startsAt->addMonthNoOverflow()->startOfMonth()->startOfDay();
        $this->endsAt->addMonthNoOverflow()->endOfMonth()->startOfDay();

        $this->calculateGridStartsEnds();
    }

    public function goToCurrentMonth(): void
    {
        $this->startsAt = Carbon::today()->startOfMonth()->startOfDay();
        $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();

        $this->calculateGridStartsEnds();
    }

    public function toggleWeekends(): void
    {
        $this->hideWeekends = ! $this->hideWeekends;
    }

    public function calculateGridStartsEnds(): void
    {
        $this->gridStartsAt = $this->startsAt->clone()->startOfWeek($this->weekStartsAt);
        $this->gridEndsAt = $this->endsAt->clone()->endOfWeek($this->weekEndsAt);
    }

    /**
     * @throws Exception
     */
    public function monthGrid(): Collection
    {
        $firstDayOfGrid = $this->gridStartsAt;
        $lastDayOfGrid = $this->gridEndsAt;

        // this breaks the calendar when the month has 6 weeks
        //        $numbersOfWeeks = $lastDayOfGrid->diffInWeeks($firstDayOfGrid) + 1;
        //        $days = $lastDayOfGrid->diffInDays($firstDayOfGrid) + 1;
        //        if ($days % 7 != 0) {
        //            throw new Exception('Livewire Calendar not correctly configured. Check initial inputs.');
        //        }

        $monthGrid = collect();
        $currentDay = $firstDayOfGrid->clone();

        while (! $currentDay->greaterThan($lastDayOfGrid)) {
            $monthGrid->push($currentDay->clone());
            $currentDay->addDay();
        }

        $monthGrid = $monthGrid->chunk(7);
        // this breaks the calendar when the month has 6 weeks
        //        if ($numbersOfWeeks != $monthGrid->count()) {
        //            throw new Exception('Livewire Calendar calculated wrong number of weeks. Sorry :(');
        //        }

        return $monthGrid;
    }

    public function events(): Collection
    {
        return collect();
    }

    public function getEventsForDay($day, Collection $events): Collection
    {
        return $events->filter(function ($event) use ($day) {
            if (isset($event['multiDay']) && $event['multiDay'] === true) {
                return $day->between(Carbon::parse($event['date']), Carbon::parse($event['end']));
            }

            return Carbon::parse($event['date'])->isSameDay($day);
        });
    }

    public function onDayClick($year, $month, $day)
    {
        //
    }

    public function onEventClick($eventId)
    {
        //
    }

    public function onEventDropped($eventId, $year, $month, $day)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function render(): Factory|View|\Illuminate\Contracts\View\View
    {
        $events = $this->events();

        return view($this->calendarView)
            ->with([
                'componentId' => $this->getId(),
                'monthGrid' => $this->monthGrid(),
                'events' => $events,
                'getEventsForDay' => function ($day) use ($events) {
                    return $this->getEventsForDay($day, $events);
                },
            ]);
    }

    /**
     * @throws Throwable
     */
    public function scripts(): string
    {
        return view('livewire-calendar::components.scripts')->render();
    }
}
