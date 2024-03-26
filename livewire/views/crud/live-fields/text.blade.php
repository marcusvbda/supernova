@php
    $wireKey = @$wireKey ? $wireKey : uniqid();
@endphp
@livewire(
    'supernova::crud-text-field',
    [
        'index' => data_get($field, 'field'),
        'mask' => data_get($field, 'mask', ''),
        'disabled' => data_get($field, 'disabled', false),
        'type' => @$type ? $type : 'text',
        'crudId' => $crudId,
        'rows' => data_get($field, 'rows', 3),
        'moduleId' => $module->id(),
        'entity' => @$entity,
    ],
    $wireKey
)
