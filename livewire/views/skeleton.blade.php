<div role="status" class="w-full animate-pulse h-[{{ @$size ?? '200px' }}] cursor-wait rounded-sm {{ $class ?? '' }}"
    @if (@$action) {{ $action }} @endif>
    <div class="h-2.5 bg-gray-200 dark:bg-gray-700 w-full h-full rounded"></div>
    <span class="sr-only">Loading...</span>
</div>
