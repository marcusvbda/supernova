@php
    $fieldIndex = data_get($field, 'field');
    $formIndex = 'values.' . $fieldIndex;
    $wireKey = @$wireKey ? $wireKey : uniqid();
@endphp
<section class="flex flex-col" id="{{ $wireKey }}" class="flex flex-col">
    @livewire(
        'supernova::select-field',
        [
            'index' => $fieldIndex,
            'limit' => data_get($field, 'limit'),
            'selected' => data_get($values, $fieldIndex, []) ?? [],
            'option_size' => '200px',
            'moduleId' => $module,
            'type' => 'field',
            'crudType' => $crudType,
            'entity' => @$entity,
        ],
        key($wireKey)
    )
    @error($formIndex)
        <div class="mt-1 text-sm text-red-500 dark:text-red-400">
            {{ $message }}
        </div>
    @enderror
</section>
