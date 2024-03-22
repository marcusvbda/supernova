<nav class="bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-800 dark:border-gray-700" id="navbar">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="relative flex h-16 items-center justify-between">
            <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                <a href="{{ $homeRoute }}" class="flex flex-shrink-0 items-center mr-8">
                    <img class="h-[40px] w-auto" src="{{ $logo }}">
                </a>
                <div class="hidden sm:ml-6 sm:flex align-center">
                    <div class="flex space-x-4">
                        @foreach ($items as $key => $value)
                            @if (is_string($value))
                                <div class="relative flex">
                                    <a href="{{ $value }}"
                                        class="{{ $currentUrl == $value ? 'bg-gray-300 dark:bg-gray-900' : 'dark:bg-gray-700' }} dark:text-white rounded-md px-3 py-2 text-sm font-medium flex align-center">
                                        {{ $key }}
                                    </a>
                                </div>
                            @else
                                <div class="relative flex" @mouseover="setOpenedMenu('{{ $key }}')"
                                    @mouseleave="closeOpenedMenu()">
                                    <a href="#"
                                        class="{{ in_array($currentUrl, collect($value)->values()->toArray()) ? 'bg-gray-300 dark:bg-gray-900' : 'dark:bg-gray-700' }} dark:text-white rounded-md px-3 py-2 text-sm font-medium"
                                        @click.prevent="()=>{}">
                                        {{ $key }}
                                    </a>
                                    <div v-if="openedMenu == '{{ $key }}'"
                                        class="absolute left-0 top-0 z-10 mt-10 w-48 origin-top-right rounded-md bg-white dark:bg-gray-900 py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                        role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                                        @foreach ($value as $menuItem => $menuValue)
                                            <a href="{{ $menuValue }}"
                                                class="block px-4 py-2 text-sm text-gray-700 dark:bg-gray-900 dark:text-gray-50"
                                                role="menuitem">{{ $menuItem }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                <!-- Profile dropdown -->
                <div class="relative ml-3" @mouseover="setOpenedMenu('user-menu')" @mouseleave="closeOpenedMenu()">
                    <div>
                        <button type="button"
                            class="relative flex dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                            id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span class="absolute -inset-1.5"></span>
                            <span class="sr-only">Open user menu</span>
                            {!! data_get($menuUserNavbar, 'element', '') !!}
                        </button>
                    </div>
                    @if (count(data_get($menuUserNavbar, 'items', [])))
                        <div v-if="openedMenu == 'user-menu'"
                            class="absolute right-0 z-10 mt-1 w-48 origin-top-right rounded-md bg-white dark:bg-gray-900 py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button"
                            tabindex="-1">
                            @foreach (data_get($menuUserNavbar, 'items', []) as $key => $value)
                                <a href="{{ $value }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-50" role="menuitem"
                                    tabindex="-1" id="user-menu-item-2">{{ $key }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="sm:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pb-3 pt-2">
            @foreach ($items as $key => $value)
                @if (is_string($value))
                    <a href="{{ $value }}"
                        class="{{ $currentUrl == $value ? 'dark:bg-gray-900 bg-gray-300' : 'dark:bg-gray-700' }} dark:text-white block rounded-md px-3 py-2 text-base font-medium"
                        aria-current="page"> {{ $key }}</a>
                @else
                    <a href="#" @click.prevent="toogleOpenedMenu('{{ $key }}')"
                        class="{{ in_array($currentUrl, collect($value)->values()->toArray()) ? 'dark:bg-gray-900 bg-gray-300 ' : 'dark:bg-gray-700' }} dark:text-white block rounded-md px-3 py-2 text-base font-medium"
                        aria-current="page"> {{ $key }} </a>
                    @foreach ($value as $menuItem => $menuValue)
                        <a v-if="openedMenu == '{{ $key }}'" href="{{ $menuValue }}"
                            class="dark:bg-gray-700 dark:text-gray-50 block rounded-md px-8 py-2 text-base font-medium mx-4"
                            aria-current="page"> {{ $menuItem }}</a>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>
</nav>

<script>
    new Vue({
        el: '#navbar',
        data: {
            openedMenu: null,
            timeoutClose: null
        },
        methods: {
            setOpenedMenu(menu) {
                clearTimeout(this.timeoutClose)
                this.openedMenu = menu
            },
            closeOpenedMenu() {
                this.timeoutClose = setTimeout(() => {
                    this.openedMenu = null
                }, 300);
            },
            toogleOpenedMenu(menu) {
                if (this.openedMenu == menu) {
                    this.openedMenu = null
                } else {
                    this.openedMenu = menu
                }
            }
        }
    })
</script>
