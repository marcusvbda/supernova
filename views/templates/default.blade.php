@php
    use App\Http\Supernova\Application;
    $novaApp = app()->make(config('supernova.application', Application::class));
@endphp

<html class="{{ $novaApp->darkMode() ? 'dark' : 'ligth' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('supernova::templates.scripts.tailwind')
    <title>{{ $novaApp->title() }} | @yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ $novaApp->icon() }}" />
    @include('supernova::templates.styles.styles')
    <style>
        {!! $novaApp->styles() !!}
    </style>
    @yield('head')
    @livewireStyles
    @include('supernova::templates.scripts.vue')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100 dark:bg-gray-700">
    @livewire('supernova::navbar')
    @yield('body')
    @livewire('supernova::alerts')
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8 py-4">
        @yield('content')
    </div>
    @yield('footer')
    @livewireScripts
</body>

</html>
