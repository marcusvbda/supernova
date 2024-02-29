@php
    $all_is_selected = count($selected) > 0 && count($selected) == count($options);
@endphp
<div class="relative" wire:init="{{ $onInit }}('{{ $index }}')">
    @if (!@$loaded_options[$index])
        @include('supernova-livewire-views::skeleton', ['size' => '38px', 'class' => 'rounded-md'])
    @else
        <div class="relative">
            @if (!$all_is_selected && ($limit ? count($selected) < $limit : true))
                <select
                    wire:change="{{ $onChange }}($event.target.value,'{{ $index }}');$event.target.value = '' "
                    class="lock pl-4 pr-10 w-full bg-white rounded-md border font-normal py-1.5 text-gray-900 shadow-sm placeholder:text-gray-400 sm:text-sm sm:leading-6 px-3 dark:bg-gray-800 dark:border-gray-800 dark:text-gray-50 {{ @$extraClass ? $extraClass : '' }}">
                    <option></option>
                    @foreach ($options as $option)
                        @if (!in_array($option['value'], $selected))
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endif
                    @endforeach
                </select>
            @endif
        </div>
        @if (count($selected) > 0)
            <div
                class="grid grid-cols-[repeat(auto-fill,minmax({{ @$option_size ? $option_size : '100px' }},1fr))] gap-1 my-2">
                @foreach ($selected as $s)
                    @php
                        $selectedLabel = @collect($options)->where('value', $s)->first()['label'];
                    @endphp
                    <span
                        class="relative flex items-center inline-flex items-center rounded-md bg-blue-300 border border-blue-400 px-2 py-1 text-xs font-medium text-white ring-1 ring-inset ring-gray-500/10 dark:bg-blue-600">
                        {{ $selectedLabel }}
                        <button type="button"
                            wire:click="{{ $onRemove }}('{{ $index }}', '{{ $s }}')"
                            class="text-white hover:text-blue-500 dark:hover:text-blue-200 focus:outline-none ml-auto cursor-pointer">
                            <svg class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </button>
                    </span>
                @endforeach
            </div>
        @endif
    @endif
</div>
