@php
    $wireKey = @$wireKey ? $wireKey : uniqid();
@endphp
@livewire(
    'supernova::crud-upload-field',
    [
        'index' => data_get($field, 'field'),
        'crudId' => $crudId,
        'moduleId' => $module->id(),
        'entity' => @$entity,
    ],
    $wireKey
)
