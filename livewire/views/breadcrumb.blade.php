<nav class="justify-between px-4 py-3 text-gray-700 border border-gray-200 rounded-lg sm:flex sm:px-5 bg-gray-50 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Breadcrumb">
    <ol class="inline-flex items-center mb-3 space-x-1 md:space-x-2 rtl:space-x-reverse sm:mb-0">
        @foreach ($items as $key => $value)
            @php
                $isLast = count($items) == $key + 1;
                $isFirst = $key == 0;
            @endphp
            @if ($isLast)
                <li aria-current="page">
                    <div class="flex items-center ">
                        @if (!$isFirst)
                            <svg class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400" aria-hidden="true" fill="none"
                                viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        @endif
                        <span class="mx-1 text-sm font-medium text-gray-800 md:mx-2 dark:text-gray-50">
                            {{ data_get($value, 'title') }}
                        </span>
                    </div>
                </li>
            @else
                <li>
                    <div class="flex items-center">
                        @if (!$isFirst)
                            <svg class="rtl:rotate-180 w-3 h-3 mx-1 text-gray-400" aria-hidden="true" fill="none"
                                viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        @endif
                        <a href="{{ data_get($value, 'route') }}"
                            class="ms-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
                            {{ data_get($value, 'title') }}
                        </a>
                    </div>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
