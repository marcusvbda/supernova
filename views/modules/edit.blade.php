@php
    $title = $module->title('edit');
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
    <section class="flex flex-col">
        @livewire('supernova::crud', [
            'module' => $module->id(),
            'entity' => $entity,
            'panelFallback' => 'Edição de',
            'crudType' => 'edit',
            'parentId' => @$parent_id,
            'parentModule' => @$parent_module,
        ])
    </section>
@endsection
