<section class="flex flex-col">
    <{{ $type === 'textarea' ? 'textarea' : 'input' }} type="{{ $type }}" wire:model.blur="value"
        x-mask="{{ $mask }}" rows="{{ $rows }}" @if ($disabled) disabled @endif
        class="block w-full
        rounded-md border py-1.5 text-gray-900 shadow-sm placeholder:text-gray-400 sm:text-sm sm:leading-6 px-3
        dark:bg-gray-800 dark:border-gray-800 dark:text-gray-50
        @if ($loading) pointer-events-none @endif
        @error('value'){{ 'dark:border-red-500' }} @enderror">
        </{{ $type === 'textarea' ? 'textarea' : 'input' }}>
        @error('value')
            <div class="mt-1 text-sm text-red-500 dark:text-red-400">
                {!! $message !!}
            </div>
        @enderror
</section>
