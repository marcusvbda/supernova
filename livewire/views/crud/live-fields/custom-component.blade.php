@php
    use App\Http\Supernova\Application;
    $application = app()->make(config('supernova.application', Application::class));
    $module = $application->getModule($moduleId, false);
    $field = $module->getField($index);
    $component = $field->component;
    $component = $field->component;
@endphp

<div>
    {!! $component(@$entity, $values, @$entity ? 'edit' : 'create') !!}
</div>
