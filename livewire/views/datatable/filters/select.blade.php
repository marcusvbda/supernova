@php
    $wireKey = $field . '-' . uniqid();
@endphp
<section class="flex flex-col" id="{{ $wireKey }}" class="flex flex-col" id="{{ $wireKey }}">
    @livewire(
        'supernova::select-field',
        [
            'index' => $field,
            'limit' => data_get($column, 'filter_options_limit'),
            'selected' => data_get($filters, $field, []) ?? [],
            'moduleId' => $module,
            'type' => 'filter',
            'crudType' => 'list',
        ],
        key($wireKey)
    )
</section>
