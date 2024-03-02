@php
    $wireKey = $field . '-' . uniqid();
    $lazy = true;
    $reload = true;
    $cacheKey = 'list:' . $field;
    if (Cache::has($cacheKey)) {
        $lazy = false;
        $reload = false;
        $initOptions = Cache::get($cacheKey);
    } else {
        $initOptions = [];
    }
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
            'initOptions' => $initOptions,
            'lazy' => $lazy,
            'reload' => $reload,
        ],
        key($wireKey)
    )
</section>
