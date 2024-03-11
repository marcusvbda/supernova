@php
    $formIndex = 'values.' . data_get($field, 'field');
    $rows = data_get($field, 'rows', 6);
    $wireKey = @$wireKey ? $wireKey : uniqid();
@endphp
<section class="flex flex-col" id="{{ $wireKey }}" wire:ignore>
    <textarea rows="{{ $rows }}" wire:model="{{ $formIndex }}" @if (data_get($field, 'disabled')) disabled @endif
        class="block w-full
        rounded-md border py-1.5 text-gray-900 shadow-sm placeholder:text-gray-400 sm:text-sm sm:leading-6 px-3
        dark:bg-gray-800 dark:border-gray-800 dark:text-gray-50
        @error($formIndex){{ 'dark:border-red-500' }} @enderror">
    </textarea>
    @error($formIndex)
        <div class="mt-1 text-sm text-red-500 dark:text-red-400">
            {{ $message }}
        </div>
    @enderror
</section>
