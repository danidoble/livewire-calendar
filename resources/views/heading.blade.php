<div class="w-full flex justify-between gap-2 items-center p-4">
    <div class="px-2">
        <button type="button" wire:click="goToPreviousMonth" class="rounded-md bg-gray-900 dark:bg-white/90 px-3 py-2 text-sm font-semibold text-white dark:text-black shadow-sm hover:bg-gray-900/80 dark:hover:bg-white/70">{{ trans('livewire-calendar::messages.prev') }}</button>
    </div>
    <p class="flex-1 text-center text-lg text-gray-900 dark:text-gray-100 font-medium">{{ \Illuminate\Support\Str::studly($startsAt->monthName) }} {{ $startsAt->year }}</p>
    <div class="px-2">
        <button type="button" wire:click="goToNextMonth" class="rounded-md bg-gray-900 dark:bg-white/90 px-3 py-2 text-sm font-semibold text-white dark:text-black shadow-sm hover:bg-gray-900/80 dark:hover:bg-white/70">{{ trans('livewire-calendar::messages.next') }}</button>
    </div>
</div>