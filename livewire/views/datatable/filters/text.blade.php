<div class="relative flex-grow">
    <input wire:model.live.debounce.1000ms="filters.{{ $field }}"
        class="block pl-4 pr-10 w-full rounded-md border font-normal py-1.5 text-gray-900 shadow-sm placeholder:text-gray-400 sm:text-sm sm:leading-6 px-3 dark:bg-gray-800 dark:border-gray-800 dark:text-gray-50"
        type="{{ $type ?? 'text' }}">
    <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
        <button class="text-gray-300 hover:text-blue-500 focus:outline-none"
            wire:click="clearFilter('{{ $field }}')">
            <svg class="h-5 w-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
        </button>
    </div>
</div>
