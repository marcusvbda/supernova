<div class="flex items-center justify-between p-3 flex flex-row flex-col md:flex-row gap-3">
    <div class="w-full md:w-3/12">
        <select wire:model.change="perPage"
            class="block rounded-md w-full md:w-auto border py-1.5 pr-20 text-gray-900 shadow-sm placeholder:text-gray-400 sm:text-sm sm:leading-6 px-3 dark:bg-gray-800 dark:border-gray-800 dark:text-gray-50">
            @foreach ($perPageOptions as $option)
                <option value="{{ $option }}">
                    {{ $option }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="w-full md:w-9/12 flex align-center justify-center md:justify-end pagination-section ">
        <div class="flex flex-col gap-3 items-center md:flex-row">
            <span class="text-light text-neutral-700 text-sm dark:text-gray-300">
                {{ $currentPage }} / {{ $totalPages }} Página{{ $totalPages > 1 ? 's' : '' }} - (Total de
                {{ $totalResults }} registro{{ $totalResults > 1 ? 's' : '' }})
            </span>
            <div class="flex flex-row gap-3">
                <button wire:click.prevent="setCursor('{{ $prevCursor }}','prev')"
                    @if (!$hasPrevCursor) disabled @endif
                    class="flex items-center justify-center px-3 h-8 me-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white mr-0 disabled:opacity-30 disabled:cursor-not-allowed">
                    <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 5H1m0 0 4 4M1 5l4-4" />
                    </svg>
                    Anterior
                </button>
                <button wire:click.prevent="setCursor('{{ $nextCursor }}', 'next')"
                    @if (!$hasNextCursor) disabled @endif
                    class="flex items-center justify-center px-3 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white disabled:opacity-30 disabled:cursor-not-allowed">
                    Próxima
                    <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
