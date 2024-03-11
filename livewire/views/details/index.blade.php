@php
    use App\Http\Supernova\Application;
    $application = app()->make(config('supernova.application', Application::class));
    $appModule = $application->getModule($module, false);
    $panels = $appModule->getVisibleFieldPanels('Detalhes de', $entity, 'details');
    $fieldPanels = collect($panels)->where('type', 'fields')->toArray();
    $resourcePanels = collect($panels)->where('type', 'resources')->toArray();
    $values = [];
    foreach ($fieldPanels as $panel) {
        foreach ($panel->fields as $field) {
            $values[$field->field] = $appModule->processFieldDetail($entity, $field);
        }
    }
@endphp
<div class="flex flex-col pb-10">
    @foreach ($fieldPanels as $key => $panel)
        <h4
            class="text-2xl md:text-3xl text-neutral-800 font-bold dark:text-neutral-200 flex items-center gap-3 flex justify-between flex-col md:flex-row gap-2 md:gap-3 mt-6 mb-2">
            <span class="order-2 md:order-1">{{ data_get($panel, 'label') }}</span>
            @if ($key === 0 && ($canEdit || $canDelete))
                <div class="text-sm order-1 flex justify-end">
                    <div>
                        @if ($canEdit)
                            <button type="button" wire:click="redirectToEdit" wire:loading.attr="disabled"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition dark:bg-gray-800 hover:dark:bg-gray-900">
                                Editar
                            </button>
                        @endif
                        @if ($canDelete)
                            <button type="button" wire:click="deleteEntity" wire:loading.attr="disabled"
                                wire:confirm="Tem certeza que deseja excluir?"
                                class="bg-red-700 hover:bg-red-800 border text-white font-bold py-2 px-6 rounded transition dark:bg-gray-800 hover:dark:bg-gray-900 dark:hover:text-red-700">
                                Excluir
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </h4>
        @php
            $fields = data_get($panel, 'fields', []);
        @endphp
        <div
            class="flex flex-col justify-between text-gray-700 border border-gray-200 rounded-lg sm:flex bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
            @foreach ($fields as $fieldIndex => $field)
                @if (data_get($field, 'visible'))
                    <div
                        class="w-full flex flex-col md:flex-row gap-1 md:gap-0 items-center px-4 p-5 md:px-6 @if ($fieldIndex !== count($fields) - 1) border-b-2 border-gray-100 dark:border-gray-700 @endif">
                        <label class="w-full md:w-3/12">
                            <h4 class="text-md text-gray-500 dark:text-gray-400">
                                {{ $field->label }}
                            </h4>
                        </label>
                        <div class="w-full md:w-9/12 text-gray-600 dark:text-gray-300">
                            @php
                                $component = $field->component;
                            @endphp
                            @if (!$component)
                                {!! nl2br($values[$field->field]) !!}
                            @else
                                {!! $component($entity, $values, 'details') !!}
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach

    @if (count($fieldPanels) == 0 && ($canEdit || $canDelete))
        <div class="mt-6 mb-2">
            <div class="text-sm order-1 flex justify-end">
                <div>
                    @if ($canEdit)
                        <button type="button" wire:click="redirectToEdit" wire:loading.attr="disabled"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition dark:bg-gray-800 hover:dark:bg-gray-900">
                            Editar
                        </button>
                    @endif
                    @if ($canDelete)
                        <button type="button" wire:click="deleteEntity" wire:loading.attr="disabled"
                            wire:confirm="Tem certeza que deseja excluir?"
                            class="bg-red-700 hover:bg-red-800 border text-white font-bold py-2 px-6 rounded transition dark:bg-gray-800 hover:dark:bg-gray-900 dark:hover:text-red-700">
                            Excluir
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @foreach ($resourcePanels as $key => $panel)
        <div class="flex flex-col">
            @foreach (@$panel->fields as $fKey => $field)
                @php
                    $fieldResource = app()->make($field->module);
                @endphp
                @include('supernova::modules.resource-list', [
                    'module' => $fieldResource,
                    'queryInit' => $module . '.' . $entity->id . '.' . $field->field,
                    'checkDeclaration' => false,
                    'wireKey' => $key . '-' . $fKey,
                ])
            @endforeach
        </div>
    @endforeach
</div>
