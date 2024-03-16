@php
    $countSelected = is_array($selected) ? count($selected) : 0;
@endphp
<section class="flex flex-col">
    @if (!$loaded)
        <div wire:init="loadOptions">
            @include('supernova-livewire-views::skeleton', ['size' => '38px'])
        </div>
    @else
        @php
            $allIsSelected = count($options) === $countSelected;
        @endphp
        @if (count($options) <= 0)
            <small>Nenhum registro</small>
        @else
            @if (!$allIsSelected && $countSelected < $limit)
                <select wire:model.live="value" @if ($disabled) disabled @endif
                    class="block w-full
        rounded-md border py-1.5 text-gray-900 shadow-sm placeholder:text-gray-400 sm:text-sm sm:leading-6 px-3
        dark:bg-gray-800 dark:border-gray-800 dark:text-gray-50  @error('value'){{ 'dark:border-red-500' }} @enderror
        @if ($loading) pointer-events-none @endif">
                    <option value=""></option>
                    @foreach ($options as $option)
                        @if (!in_array($option['value'], is_array($selected) ? $selected : []))
                            <option value="{{ data_get($option, 'value') }}">{{ data_get($option, 'label') }}</option>
                        @endif
                    @endforeach
                </select>
            @endif
        @endif
    @endif
    @if ($countSelected)
        <div class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-1 my-2">
            @foreach ($selected as $s)
                @php
                    $selectedLabel = @collect($options)->where('value', $s)->first()['label'];
                @endphp
                <span
                    class="relative flex items-center  inline-flex items-center rounded-md bg-blue-300 border border-blue-400 px-2 py-1 text-xs font-medium text-white ring-1 ring-inset ring-gray-500/10 dark:bg-blue-600">
                    <div class="pr-2">
                        {{ $selectedLabel }}
                    </div>
                    <button type="button" wire:click="removeOption('{{ $s }}')"
                        class="text-white hover:text-blue-500 dark:hover:text-blue-200 focus:outline-none ml-auto cursor-pointer">
                        <svg class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </button>
                </span>
            @endforeach
        </div>
    @endif
    @error('value')
        <div class="mt-1 text-sm text-red-500 dark:text-red-400">
            {!! $message !!}
        </div>
    @enderror
</section>
