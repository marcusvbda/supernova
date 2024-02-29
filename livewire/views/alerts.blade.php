<div>
    @foreach ($alerts as $alert)
        @php
            $class = match (data_get($alert, 'type')) {
                'success' => 'bg-green-100 border-green-500 text-green-700',
                'error' => 'bg-red-100 border-red-500 text-red-700',
                'warning' => 'bg-orange-100 border-orange-500 text-orange-700',
                'info' => 'bg-blue-100 border-blue-500 text-blue-700',
                default => 'bg-teal-100 border-teal-500 text-teal-700',
            };
        @endphp
        <div class="{{ $class }} px-4 py-3 shadow-md mb-2 alert-message transition-opacity duration-500 ease-in-out"
            role="alert">
            <div class="flex">
                <p class="text-sm">{{ data_get($alert, 'message') }}</p>
            </div>
        </div>
    @endforeach
    @script
        <script>
            const els = document.querySelectorAll('.alert-message');
            setTimeout(() => {
                Array.from(els).reverse().forEach((element, index) => {
                    setTimeout(() => {
                        element.classList.add(
                            'opacity-0');
                        setTimeout(() => {
                            element.remove();
                        }, 500);
                    }, index * 500);
                });
            }, 5000);
        </script>
    @endscript
</div>
