@php
    $fieldIndex = data_get($field, 'field');
    $formIndex = 'values.' . $fieldIndex;
@endphp

@include('supernova::select-field', [
    'index' => $fieldIndex,
    'options' => data_get($options, $fieldIndex, []) ?? [],
    'selected' => data_get($values, $fieldIndex, []) ?? [],
    'onChange' => 'setSelectOption',
    'onRemove' => 'removeOption',
    'extraClass' => @$errors->has($formIndex) ? 'dark:border-red-500' : '',
    'onInit' => 'loadInputOptions',
    'limit' => data_get($field, 'limit'),
    'option_size' => '200px',
])
@error($formIndex)
    <div class="mt-1 text-sm text-red-500 dark:text-red-400">
        {{ $message }}
    </div>
@enderror
