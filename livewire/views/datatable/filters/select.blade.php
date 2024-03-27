@php
    $wireKey = $field . '-' . uniqid();
@endphp
<section class="flex flex-col" id="{{ $wireKey }}" class="flex flex-col" id="{{ $wireKey }}" wire:ignore>
    @livewire(
        'supernova::select-field',
        [
            'index' => $field,
            'limit' => data_get($column, 'filter_options_limit'),
            'selected' => data_get($filters, $field, []) ?? [],
            'moduleId' => $moduleId,
            'type' => 'filter',
            'crudType' => 'list',
            'perPage' => $perPage,
            'sort' => $sort,
            'refId' => $tableId,
            'option_size' => '100%',
            'options' => @$filterOptions[$field],
            'lazy' => false,
            'loading' => !is_array(@$filterOptions[$field]),
            'tableId' => @$tableId,
        ],
        key($wireKey)
    )
</section>
