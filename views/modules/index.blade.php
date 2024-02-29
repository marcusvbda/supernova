@extends(config('supernova.modules_template', 'supernova::templates.default'))
@section('title', $module->title('index'))
@section('content')
    @livewire('supernova::breadcrumb', [
        'moduleId' => $module->id(),
    ])
    @php
        $metricCards = $module->metrics();
    @endphp
    @if (count($metricCards))
        <div class="grid lg:grid-cols-3 md:grid-cols-3 gap-3 mt-5 mb-3">
            {!! Blade::render(implode(' ', $metricCards)) !!}
        </div>
    @endif
    <div class="mb-20">
        @include('supernova::modules.resource-list', ['module' => $module])
    </div>
@endsection
