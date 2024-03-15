@php
    $wireKey = @$wireKey ? $wireKey : uniqid();
    $moduleUrl = route('supernova.modules.index', $module->id());
    if (@$queryInit) {
        $urlSplitted = explode('.', $queryInit);
        $moduleUrl = route('supernova.modules.field-create', [
            'module' => $urlSplitted[0],
            'id' => $urlSplitted[1],
            'field' => $urlSplitted[2],
        ]);
        $moduleUrl = str_replace('/create', '', $moduleUrl);
    } else {
        $moduleUrl = route('supernova.modules.index', $module->id());
    }
    $canCreate = $module->canCreate();
    $columns = array_map(function ($row) {
        $row = (array) $row;
        $row['action'] = null;
        $row['action'] = null;
        $row['filter_options'] = null;
        $row['filter_options_callback'] = null;
        $row['filter_callback'] = null;
        return $row;
    }, $module->getDataTableVisibleColumns());
    $filterable = collect($columns)->filter(fn($row) => $row['filterable'])->count() > 0;
    $tableId = uniqid();
@endphp
<section class="flex flex-col" id="{{ $wireKey }}" wire:ignore>
    <h4 class="text-3xl text-neutral-800 font-bold dark:text-neutral-200 mt-3 mb-2 flex items-center gap-3 mt-6">
        {{ $module->title('index') }}
    </h4>
    <div class="flex flex-col items-center justify-center">
        <div class="flex flex-col gap-3 w-full">
            <div class="flex flex-row flex-wrap gap-3 w-full items-center">
                @livewire('supernova::datatable-global-filter', [
                    'moduleId' => $module->id(),
                    'sort' => $module->defaultSort(),
                    'tableId' => $tableId,
                ])
                @if ($canCreate || @$parentId)
                    <a class="ml-auto cursor-pointer w-full md:w-auto" href="{{ $moduleUrl }}/create">
                        <button type="button"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition  w-full md:w-auto">
                            Cadastrar
                        </button>
                    </a>
                @endif
            </div>
            <div
                class="bg-gray-50 dark:bg-gray-600  rounded-lg text-gray-700 border border-gray-200 dark:border-gray-700 dark:text-gray-50">
                <div class="overflow-x-auto relative datatable-fixed-header">
                    <table class="w-full border-b border-gray-200 dark:border-gray-700">
                        <thead>
                            @livewire('supernova::datatable-header', [
                                'moduleId' => $module->id(),
                                'sort' => $module->defaultSort(),
                                'tableId' => $tableId,
                            ])
                            @if ($filterable)
                                @livewire('supernova::datatable-header-filter', [
                                    'moduleId' => $module->id(),
                                    'sort' => $module->defaultSort(),
                                    'tableId' => $tableId,
                                ])
                            @endif
                        </thead>
                        @livewire('supernova::datatable-body', [
                            'moduleId' => $module->id(),
                            'sort' => $module->defaultSort(),
                            'queryInit' => @$queryInit,
                            'moduleUrl' => $moduleUrl,
                            'tableId' => $tableId,
                        ])
                    </table>
                </div>
                @livewire('supernova::datatable-pagination', [
                    'moduleId' => $module->id(),
                    'sort' => $module->defaultSort(),
                    'tableId' => $tableId,
                ])
            </div>
        </div>
    </div>
</section>
