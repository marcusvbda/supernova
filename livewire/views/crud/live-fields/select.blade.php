@php
    $wireKey = @$wireKey ? $wireKey : uniqid();
@endphp
@livewire(
    'supernova::crud-select-field',
    [
        'index' => data_get($field, 'field'),
        'type' => @$type ? $type : 'text',
        'limit' => data_get($field, 'limit'),
        'crudId' => $crudId,
        'moduleId' => $module->id(),
        'entity' => @$entity,
    ],
    $wireKey
)
