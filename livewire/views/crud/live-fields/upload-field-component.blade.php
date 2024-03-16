@php
    use App\Http\Supernova\Application;
    $app = app()->make(config('supernova.application', Application::class));
    $mod = $app->getModule($moduleId);
    $field = $mod->getField($index);
    $previewCallback = $field->previewCallback;
    $limit = $field->limit;
@endphp

<section class="flex flex-col">
    <div x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false;progress=0;"
        x-on:livewire-upload-cancel="uploading = false;progress=0;"
        x-on:livewire-upload-error="uploading = false;progress=0"
        x-on:livewire-upload-progress="progress = $event.detail.progress">
        @if (count($values) < $limit)
            <input type="file" wire:model.blur="value" @if ($disabled) disabled @endif
                class="block w-full rounded-md border py-1.5 text-gray-900 shadow-sm placeholder:text-gray-400 sm:text-sm sm:leading-6 px-3
dark:bg-gray-800 dark:border-gray-800 dark:text-gray-50
@error('value') {{ 'dark:border-red-500' }} @enderror">
        @endif
        <div x-show="uploading">
            <progress max="100" x-bind:value="progress" class="w-full mt-2"></progress>
        </div>
        @error('value')
            <div class="mt-1 text-sm text-red-500 dark:text-red-400">
                {!! $message !!}
            </div>
        @enderror
    </div>
    @if (count($values) > 0)
        <div class="flex flex-col gap-2 my-2">
            @foreach ($values as $key => $value)
                <div class="w-full flex items-center justify-content-between">
                    {!! $previewCallback($value) !!}
                    <button type="button" wire:click="removeFile({{ $key }})"
                        class="hover:text-blue-500 dark:hover:text-blue-200 focus:outline-none ml-auto cursor-pointer">
                        <svg class="h-7 w-7 stroke-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</section>
