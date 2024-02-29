@php
    $wireKey = @$wireKey ? $wireKey : uniqid();
@endphp
<section n class="flex flex-col" id="{{ $wireKey }}">
    <h4 class="text-3xl text-neutral-800 font-bold dark:text-neutral-200 mt-3 mb-2 flex items-center gap-3 mt-6">
        {{ $module->title('index') }}
    </h4>
    @livewire('supernova::datatable', [
        'module' => $module->id(),
        'sort' => $module->defaultSort(),
        'queryInit' => @$queryInit,
        'checkDeclaration' => @$checkDeclaration ? true : false,
        'key' => $wireKey,
    ])
</section>
