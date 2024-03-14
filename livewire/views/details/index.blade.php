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
            @if (($key === 0 && ($canEdit || $canDelete)) || @$parentId)
                <div class="text-sm order-1 flex justify-end">
                    <div>
                        @if ($canEdit || @$parentId)
                            <button type="button" wire:click="redirectToEdit" wire:loading.attr="disabled"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition dark:bg-gray-800 hover:dark:bg-gray-900">
                                Editar
                            </button>
                        @endif
                        @if ($canDelete || @$parentId)
                            <button type="button" wire:click="deleteEntity" wire:loading.attr="disabled"
                                wire:confirm="Tem certeza que deseja excluir?"
                                class="bg-red-700 hover:bg-red-800 border text-white font-bold py-2 px-6 rounded transition dark:bg-gray-800 hover:dark:bg-gray-900 dark:hover:text-red-700">
                                <svg aria-hidden="true" role="status"
                                    class="inline w-4 h-4 me-3 text-gray-500 animate-spin" wire:loading
                                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="#E5E7EB" />
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentColor" />
                                </svg>
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
                            <svg aria-hidden="true" role="status"
                                class="inline w-4 h-4 me-3 text-gray-500 animate-spin" wire:loading
                                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                    fill="#E5E7EB" />
                                <path
                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                    fill="currentColor" />
                            </svg>
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
                    'parentId' => $entity->id,
                ])
            @endforeach
        </div>
    @endforeach
</div>
