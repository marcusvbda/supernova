@php
    $fieldIndex = data_get($field, 'field');
    $formIndex = 'values.' . $fieldIndex;
@endphp

{{-- @include('supernova::select-field', [
    'index' => $fieldIndex,
    'options' => data_get($options, $fieldIndex, []) ?? [],
    'selected' => data_get($values, $fieldIndex, []) ?? [],
    'onChange' => 'setSelectOption',
    'onRemove' => 'removeOption',
    'extraClass' => @$errors->has($formIndex) ? 'dark:border-red-500' : '',
    'onInit' => 'loadInputOptions',
    'limit' => data_get($field, 'limit'),
    'option_size' => '200px',
]) --}}
<div wire:ignore>
    @livewire('supernova::select-field', [
        'index' => $fieldIndex,
        'limit' => data_get($field, 'limit'),
        'selected' => data_get($values, $fieldIndex, []) ?? [],
        'option_size' => '200px',
        'moduleId' => $module,
        'type' => 'field',
        'crudType' => $crudType,
        'entity' => @$entity,
    ])
</div>

@error($formIndex)
    <div class="mt-1 text-sm text-red-500 dark:text-red-400">
        {{ $message }}
    </div>
@enderror
