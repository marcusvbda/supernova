<div class="bg-gray-50 dark:bg-gray-600  rounded-lg text-gray-700 border border-gray-200 dark:border-gray-700 dark:text-gray-50"
    x-data="datatable">
    <div class="overflow-x-auto relative" wire:loading.class="opacity-50 overflow-x-hidden">
        <div wire:loading>
            <div class="flex items-center justify-center w-full cursor-wait"
                style="position: absolute;inset: 0;background-color: #77777729;z-index=9;display:flex;align-items-center;justify-content:center;z-index: 9;">
                <div class="flex flex-col items-center gap-10 my-20 justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 opacity-30" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
        <table class="w-full border-b border-gray-200 dark:border-gray-700">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-600">
                    @php
                        $_sort = $sort;
                        $sort = explode('|', $sort);
                    @endphp
                    @foreach ($columns as $key => $value)
                        @php
                            $sortable = data_get($value, 'sortable', false);
                            $label = data_get($value, 'label', false);
                            $field = data_get($value, 'name');
                            $minWidth = data_get($value, 'minWidth');
                            $width = data_get($value, 'width');
                            $lastColumn = $key === count($columns) - 1;
                            $showBorder = !$lastColumn;
                            $align = data_get($value, 'align', 'justify-end');
                            $isFirst = $key === 0;
                        @endphp
                        <th class="@if ($isFirst) table-column-fixed @endif @if ($field === 'id')  @endif font-normal @if ($minWidth) min-w-[{{ $minWidth }}] @endif @if ($width) w-[{{ $width }}] @endif p-5 font-medium text-gray-700 @if ($showBorder) border-right border-r border-gray-200 dark:border-gray-700 @endif dark:text-gray-200 @if ($sortable) cursor-pointer @endif"
                            @if ($sortable) wire:click="reloadSort('{{ $field }}','{{ $sort[0] }}','{{ $sort[1] }}')" @endif>
                            <div class="flex items-center gap-5 w-full {{ $align }}">
                                {!! $label !!}
                                @if ($sortable && $sort[0] === $field)
                                    <div class="flex gap-3 ml-auto">
                                        @if ($sort[1] === 'desc')
                                            <div class="relative w-[24px] h-[20px]">
                                                <svg class="h-5 w-5 stroke-current h-6 w-6 text-blue-600 dark:text-blue-200 stroke-current"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="relative w-[24px] h-[20px] ml-auto">
                                                <svg class="h-5 w-5 stroke-current h-6 w-6 text-blue-600 dark:text-blue-200 stroke-current"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
                @if ($filterable)
                    <tr class="bg-blue-100 dark:bg-blue-300">
                        @foreach ($columns as $key => $value)
                            @php
                                $lastColumn = $key === count($columns) - 1;
                                $showBorder = !$lastColumn;
                                $filterType = data_get($value, 'filter_type');
                                $filterBlade = "supernova-livewire-views::datatable.filters.$filterType";
                                $filter_options_limit = data_get($value, 'filter_options_limit');
                                $field = data_get($value, 'name');
                                $isFirst = $key === 0;
                            @endphp
                            <th
                                class="@if ($isFirst) table-column-fixed filter @endif @if ($field === 'id')  @endif @if ($showBorder) border-r border-blue-200 dark:border-blue-500 @endif align-top p-1">
                                @if (View::exists($filterBlade))
                                    @include($filterBlade, [
                                        'column' => $value,
                                        'field' => $field,
                                        'filter_options_limit' => $filter_options_limit,
                                    ])
                                @endif
                            </th>
                        @endforeach
                    </tr>
                @endif
            </thead>
            <tbody>
                @if (!$hasItems)
                    <tr class="bg-white dark:bg-gray-500">
                        @php
                            $colspan = count($columns);
                        @endphp
                        <td class="p-4 px-5 text-right font-light text-gray-600 dark:text-gray-300"
                            colspan="{{ $colspan }}">
                            <div class="w-full flex">
                                @include('supernova-livewire-views::datatable.no-result')
                            </div>
                        </td>
                    </tr>
                @else
                    @foreach ($itemsPage as $i => $item)
                        <tr class="{{ $i % 2 === 1 ? 'bg-gray-100 dark:bg-gray-600' : 'bg-white dark:bg-gray-500' }}">
                            @foreach ($columns as $key => $value)
                                @php
                                    $sortable = data_get($value, 'sortable', false);
                                    $label = data_get($value, 'label', false);
                                    $field = data_get($value, 'name');
                                    $minWidth = data_get($value, 'minWidth', '100px');
                                    $width = data_get($value, 'width');
                                    $lastColumn = $key === count($columns) - 1;
                                    $showBorder = !$lastColumn;
                                    $align = data_get($value, 'align', 'justify-end');
                                    $isFirst = $key === 0;
                                @endphp
                                <td
                                    class="@if ($isFirst) table-column-fixed @endif @if ($field === 'id')  @endif p-4 px-5 text-right font-light text-sm text-gray-600 @if ($showBorder) border-r border-gray-200 dark:border-gray-700 @endif dark:text-gray-300">
                                    <div class="w-full flex {{ $align }}">
                                        @if ($isFirst)
                                            <a href="{{ $moduleUrl . '/' . $item['_id'] }}"
                                                class="font-medium text-blue-600 dark:text-blue-300 hover:underline ">
                                        @endif
                                        {!! $item[$field] !!}
                                        @if ($isFirst)
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    @include('supernova-livewire-views::datatable.pagination')
</div>
@script
    <script>
        Alpine.data('datatable', () => ({
            editClick(id) {
                window.location.href = `{{ $moduleUrl }}/${id}/edit`;
            }
        }));
    </script>
@endscript
