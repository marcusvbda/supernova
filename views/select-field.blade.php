<div wire:ignore>
    @livewire('supernova::select-field', [
        'index' => $index,
        'limit' => $limit,
        'selected' => $selected,
        'option_size' => @$option_size,
        'moduleId' => $module,
        'type' => 'filter',
    ])
</div>
