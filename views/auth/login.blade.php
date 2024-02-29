@extends(config('supernova.modules_template', 'supernova::templates.default'))
@section('title', 'Login')
@section('content')
    @livewire('supernova::login', [
        'redirect' => $redirect,
    ])
@endsection
