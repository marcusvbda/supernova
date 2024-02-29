@php
    use App\Http\Supernova\Application;
    $novaApp = app()->make(config('supernova.application', Application::class));
@endphp

@extends(config('supernova.modules_template', 'supernova::templates.default'))
@section('title', 'Dashboard')
@section('content')
    @livewire('supernova::breadcrumb')
    <h4 class="text-3xl text-neutral-800 font-bold dark:text-neutral-200 my-5">
        {{ $novaApp->DashboardGreetingMessage() }}
    </h4>
    @livewire('supernova::dashboard')
@endsection
