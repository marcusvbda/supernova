@php
    $title = $module->title('edit');
    $crudId = uniqid();
    $allPanels = $module->getVisibleFieldPanels('Edição de', @$entity, 'edit');
    $panels = collect($allPanels)->where('type', '!=', 'resources')->toArray();
    $title = data_get($panels, '0.label');
    $resourcePanels = collect($allPanels)->where('type', 'resources')->toArray();
@endphp
@extends(config('supernova.modules_template', 'supernova::templates.default'))
@section('title', $title)
@section('content')
    @livewire('supernova::breadcrumb', [
        'entityUrl' => route('supernova.modules.edit', ['module' => $module->id(), 'id' => $entity->id]),
        'entityId' => $entity->id,
        'moduleId' => $module->id(),
        'parentId' => @$parent_id,
        'parentModule' => @$parent_module,
        'moduleId' => $module->id(),
    ])

    @livewire('supernova::crud-header', [
        'moduleId' => $module->id(),
        'checkDeclaration' => @$parent_id ? true : false,
        'crudId' => $crudId,
        'title' => $title,
        'entity' => $entity,
        'parentId' => @$parent_id,
        'parentModule' => @$parent_module,
    ])
    <div class="flex flex-col pb-10 pt-1">
        @foreach ($panels as $key => $panel)
            @if ($key !== 0)
                <div class="flex flex-col md:flex-row md:flex items-center justify-between">
                    <h4
                        class="text-2xl md:text-3xl text-neutral-800 font-bold dark:text-neutral-200 flex items-center gap-3 gap-2 md:gap-3 mt-6 mb-2 order-2 md:order-1">
                        <span class="order-2 md:order-1">{{ data_get($panel, 'label') }}</span>
                    </h4>
                </div>
            @endif
            <div
                class="flex flex-col justify-between text-gray-700 border border-gray-200 rounded-lg sm:flex bg-gray-50 dark:bg-gray-800 dark:border-gray-700 relative">
                @php
                    $fields = data_get($panel, 'fields', []);
                @endphp
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
                                    $fieldBlade = "supernova-livewire-views::crud.live-fields.$type";
                                    $wireKey = $field->field . '_' . uniqid();
                                @endphp
                                @if (!$component)
                                    @if (View::exists($fieldBlade))
                                        @include($fieldBlade, [
                                            'field' => $field,
                                            'wireKey' => $wireKey,
                                        ])
                                    @else
                                        {!! $module->processFieldDetail(@$entity, $field) !!}
                                    @endif
                                @else
                                    @livewire('supernova::crud-custom-component', [
                                        'index' => $field->field,
                                        'crudId' => $crudId,
                                        'moduleId' => $module->id(),
                                        'entity' => $entity,
                                    ])
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach

        @foreach ($resourcePanels as $key => $panel)
            <div class="flex flex-col">
                @foreach (@$panel->fields as $fKey => $field)
                    @php
                        $fieldResource = app()->make($field->module);
                    @endphp
                    @include('supernova::modules.resource-list', [
                        'module' => $fieldResource,
                        'queryInit' => $module->id() . '.' . $entity->id . '.' . $field->field,
                        'checkDeclaration' => false,
                        'wireKey' => $key . '-' . $fKey,
                        'parentId' => $entity->id,
                    ])
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
