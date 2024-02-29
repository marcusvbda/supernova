@include('supernova::select-field', [
    'index' => $field,
    'options' => data_get($filter_options, $field, []) ?? [],
    'selected' => data_get($filters, $field, []) ?? [],
    'onChange' => 'setSelectOption',
    'onRemove' => 'removeFilterOption',
    'onInit' => 'loadFilterOptions',
    'limit' => data_get($column, 'filter_options_limit'),
])
