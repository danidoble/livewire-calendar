
<div
        ondragenter="onLivewireCalendarEventDragEnter(event, '{{ $componentId }}', '{{ $day }}', '{{ $dragAndDropClasses }}');"
        ondragleave="onLivewireCalendarEventDragLeave(event, '{{ $componentId }}', '{{ $day }}', '{{ $dragAndDropClasses }}');"
        ondragover="onLivewireCalendarEventDragOver(event);"
        ondrop="onLivewireCalendarEventDrop(event, '{{ $componentId }}', '{{ $day }}', {{ $day->year }}, {{ $day->month }}, {{ $day->day }}, '{{ $dragAndDropClasses }}');"
        class="flex-1 h-40 lg:h-48 border border-gray-200 dark:border-gray-800 -mt-px -ml-px"
        style="min-width: 10rem;">

    {{-- Wrapper for Drag and Drop --}}
    <div
            class="w-full h-full"
            id="{{ $componentId }}-{{ $day }}">

        <div
                @if($dayClickEnabled)
                    wire:click="onDayClick({{ $day->year }}, {{ $day->month }}, {{ $day->day }})"
                @endif
                class="w-full h-full p-2 {{ $dayInMonth ? $isToday ? 'bg-yellow-100 dark:bg-teal-800/30' : ' bg-white dark:bg-gray-950' : 'bg-gray-100 dark:bg-gray-800/80' }} flex flex-col">

            {{-- Number of Day --}}
            <div class="flex items-center">
                <p class="text-sm text-gray-800 dark:text-gray-200 {{ $dayInMonth ? ' font-medium ' : '' }}">
                    {{ $day->format('j') }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400 ml-4">
                    @if($events->isNotEmpty())
                        {{ $events->count() }} {{ $events->count() > 1 ? trans('livewire-calendar::messages.events') : trans('livewire-calendar::messages.event') }}
                    @endif
                </p>
            </div>

            {{-- Events --}}
            <div class="p-2 my-2 flex-1 overflow-y-auto">
                <div class="grid grid-cols-1 grid-flow-row gap-2">
                    @foreach($events as $event)
                        <div
                                @if($dragAndDropEnabled)
                                    draggable="true"
                                @endif
                                ondragstart="onLivewireCalendarEventDragStart(event, '{{ $event['id'] }}')">
                            @include($eventView, [
                                'event' => $event,
                            ])
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
