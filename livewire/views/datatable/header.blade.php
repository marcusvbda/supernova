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
            @if ($sortable) wire:click="clickedSort('{{ $field }}','{{ $sort[0] }}','{{ $sort[1] }}')" @endif>
            <div class="flex items-center gap-5 w-full {{ $align }}">
                {!! $label !!}
                @if ($sortable && $sort[0] === $field)
                    <div class="flex gap-3 ml-auto">
                        @if ($sort[1] === 'desc')
                            <div class="relative w-[24px] h-[20px]">
                                <svg class="h-5 w-5 stroke-current h-6 w-6 text-blue-600 dark:text-blue-200 stroke-current"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        @else
                            <div class="relative w-[24px] h-[20px] ml-auto">
                                <svg class="h-5 w-5 stroke-current h-6 w-6 text-blue-600 dark:text-blue-200 stroke-current"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </th>
    @endforeach
</tr>
