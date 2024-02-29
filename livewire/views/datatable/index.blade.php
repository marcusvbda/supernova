<div class="flex flex-col items-center justify-center">
    <div class="flex flex-col gap-3 w-full">
        <div class="flex flex-row flex-wrap gap-3 w-full items-center">
            @include('supernova-livewire-views::datatable.filter')
            @if ($canCreate)
                <a class="ml-auto cursor-pointer w-full md:w-auto" href="{{ $moduleUrl }}/create">
                    <button type="button"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition  w-full md:w-auto">
                        {{ $btnCreateText }}
                    </button>
                </a>
            @endif
        </div>
        @include('supernova-livewire-views::datatable.table')
    </div>
</div>
