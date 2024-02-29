@php
    $title = $module->title('details');
    $canEdit = $module->canEdit();
    $canDelete = $module->canDelete();
    $canEditRow = $canEdit && $module->canEditRow($entity);
    $canDeleteRow = $canDelete && $module->canDeleteRow($entity);
@endphp
@extends(config('supernova.modules_template', 'supernova::templates.default'))
@section('title', $title)
@section('content')
    @livewire('supernova::breadcrumb', [
        'entityUrl' => route('supernova.modules.details', ['module' => $module->id(), 'id' => $entity->id]),
        'entityId' => $entity->id,
        'moduleId' => $module->id(),
        'parentId' => @$parent_id,
        'parentModule' => @$parent_module,
    ])
    <section class="flex flex-col">
        @livewire('supernova::details', [
            'module' => $module->id(),
            'entity' => $entity,
            'lazy' => true,
            'canEdit' => $canEditRow,
            'canDelete' => $canDeleteRow,
            'parentId' => @$parent_id,
            'parentModule' => @$parent_module,
        ])
    </section>
@endsection
