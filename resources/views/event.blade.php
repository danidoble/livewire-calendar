<div
        @if($eventClickEnabled)
            wire:click.stop="onEventClick('{{ $event['id']  }}')"
        @endif
        class="bg-white dark:bg-gray-950 rounded-lg border dark:border-gray-900 py-2 px-3 shadow-md dark:shadow-gray-800 cursor-pointer">

    <p class="text-sm font-medium">
        {{ $event['title'] }}
    </p>
    <p class="mt-0 text-xs">
        @if(isset($event['multiDay']) && $event['multiDay'] === true)
            {{$event['date']->format('G:i')}} - {{$event['end']->format('G:i')}}
        @endif
    </p>
    <p class="mt-2 text-xs">
        {{ $event['description'] ?? 'No description' }}
    </p>
</div>