@php
    $wireKey = $field . '-' . uniqid();
    $lazy = true;
@endphp
<section class="flex flex-col" id="{{ $wireKey }}" class="flex flex-col" id="{{ $wireKey }}">
    @livewire(
        'supernova::select-field',
        [
            'index' => $field,
            'limit' => data_get($column, 'filter_options_limit'),
            'selected' => data_get($filters, $field, []) ?? [],
            'moduleId' => $moduleId,
            'type' => 'filter',
            'crudType' => 'list',
            'lazy' => $lazy,
            'reload' => true,
            'perPage' => $perPage,
            'sort' => $sort,
        ],
        key($wireKey)
    )
</section>
