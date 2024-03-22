<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-[{{$logoHeigth}}px] w-auto" src="{{ $logo }}">
        <h2
            class="mt-4 text-center text-2xl font-bold leading-9 font-light tracking-tight text-gray-800 dark:text-gray-50">
            Entre com a sua conta
        </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" wire:submit.prevent="submit">
            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-50">
                    Email
                </label>
                <div class="mt-2">
                    <input wire:model.blur="email"
                        class="block w-full rounded-md border py-1.5 text-gray-900 shadow-sm placeholder:text-gray-400 sm:text-sm sm:leading-6 px-3 dark:bg-gray-800 dark:border-gray-800 dark:text-gray-50 @error('email'){{ 'dark:border-red-500' }} @enderror">
                    @error('email')
                        <div class="text-red-500 text-xs font-light mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password"
                        class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-50">Senha</label>
                    {{-- <div class="text-sm">
                        <a href="#" class="font-semibold text-blue-600 hover:text-blue-500">
                            Esqueceu a senha ?
                        </a>
                    </div> --}}
                </div>
                <div class="mt-2">
                    <input type="password" wire:model.blur="password"
                        class="block w-full rounded-md border py-1.5 text-gray-900 shadow-sm placeholder:text-gray-400 sm:text-sm sm:leading-6 px-3 dark:bg-gray-800 dark:border-gray-800 dark:text-gray-50 @error('email'){{ 'dark:border-red-500' }} @enderror">
                    @error('password')
                        <div class="text-red-500 text-xs font-light mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit" wire:loading.attr="disabled"
                    class="flex items-center w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:bg-blue-500 disabled:cursor-not-allowed">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" wire:loading fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Entrar
                </button>
            </div>
        </form>
    </div>
</div>
