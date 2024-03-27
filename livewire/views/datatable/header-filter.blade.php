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
                    'filterOptions' => $filterOptions,
                    'tableId' => $tableId,
                ])
            @endif
        </th>
    @endforeach
</tr>
