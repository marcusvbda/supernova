@php
    use App\Http\Supernova\Application;
    $application = app()->make(config('supernova.application', Application::class));
    $appModule = $application->getModule($module, false);
    $panels = $appModule->getVisibleFieldPanels($panelFallback, @$entity, @$crudType);
    $fieldPanels = collect($panels)->where('type', 'fields')->toArray();
    $resourcePanels = collect($panels)->where('type', 'resources')->toArray();
@endphp
<div class="flex flex-col pb-10" x-data="crud_view">
    @foreach ($fieldPanels as $key => $panel)
        <div class="flex flex-col md:flex-row md:flex items-center justify-between">
            <h4
                class="text-2xl md:text-3xl text-neutral-800 font-bold dark:text-neutral-200 flex items-center gap-3 gap-2 md:gap-3 mt-6 mb-2 order-2 md:order-1">
                <span class="order-2 md:order-1">{{ data_get($panel, 'label') }}</span>
            </h4>
            @if ($key === 0)
                <div class="mt-4 flex justify-end order-1">
                    <button type="button" @click="save"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition  w-full md:w-auto"">
                        Salvar
                    </button>
                </div>
            @endif
        </div>
        @php
            $fields = data_get($panel, 'fields', []);
        @endphp
        <div
            class="flex flex-col justify-between text-gray-700 border border-gray-200 rounded-lg sm:flex bg-gray-50 dark:bg-gray-800 dark:border-gray-700 relative">
            <template x-if="loading">
                <div class="flex items-center justify-center w-full cursor-wait"
                    style="position: absolute;inset: 0;background-color: #77777729;z-index=9;display:flex;align-items-center;justify-content:center;z-index: 9;">
                    <div class="flex flex-col items-center gap-10 my-20 justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-8 w-8 opacity-30" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>
            </template>
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
                                $type = data_get($field, 'type');
                                $component = data_get($field, 'component');
                                $fieldBlade = "supernova-livewire-views::crud.fields.$type";
                                $wireKey = $field->field . '_' . uniqid();
                            @endphp
                            @if (!$component)
                                @if (View::exists($fieldBlade))
                                    @include($fieldBlade, ['field' => $field, 'wireKey' => $wireKey])
                                @else
                                    {!! $appModule->processFieldDetail($entity, $field) !!}
                                @endif
                            @else
                                {!! $component($entity, [...$values, ...$uploadValues], $entity ? 'edit' : 'create') !!}
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach

    @if ($crudType !== 'create')
        @foreach ($resourcePanels as $key => $panel)
            <div class="flex flex-col" wire:ignore>
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
    @endif
</div>
@script
    <script>
        Alpine.data('crud_view', () => {
            return {
                loading: false,
                save() {
                    this.loading = true;
                    @this.save()
                        .then(() => {
                            this.loading = false;
                        })
                        .catch(() => {
                            this.loading = false;
                        });
                }
            }
        })
    </script>
@endscript
