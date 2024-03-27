<tbody>
    @if (!@$loaded)
        <tr class="bg-white dark:bg-gray-500" wire:init="loadData({{ $perPage }})">
            <td colspan="{{ $colspan }}" class="p-0">
                <div class="w-full flex">
                    @include('supernova-livewire-views::skeleton', ['size' => '300px'])
                </div>
            </td>
        </tr>
    @else
        @if (!$hasItems)
            <tr class="bg-white dark:bg-gray-500">
                <td class="p-4 px-5 text-right font-light text-gray-600 dark:text-gray-300" colspan="{{ $colspan }}">
                    <div class="w-full flex">
                        <div class="w-full text-center flex flex-col items-center justify-center my-20">
                            <h4
                                class="text-2xl text-neutral-800 font-bold dark:text-neutral-200 mt-4 mb-1 flex items-center">
                                Nenhum registro encontrado !
                            </h4>
                            @if ($canCreate)
                                <small class="text-neutral-600 dark:text-gray-500">
                                    Clique em <strong>'Cadastrar'</strong> para adicionar um novo registro
                                </small>
                            @endif
                        </div>

                    </div>
                </td>
            </tr>
        @else
            @foreach (@$itemsPage as $i => $item)
                <tr class="{{ $i % 2 === 1 ? 'bg-gray-100 dark:bg-gray-600' : 'bg-white dark:bg-gray-500' }}"
                    wire:loading.class="opacity-50 overflow-x-hidden pointer-events-none">
                    @foreach ($columns as $key => $value)
                        @php
                            $sortable = data_get($value, 'sortable', false);
                            $label = data_get($value, 'label', false);
                            $field = data_get($value, 'name');
                            $minWidth = data_get($value, 'minWidth', '100px');
                            $width = data_get($value, 'width');
                            $lastColumn = $key === count($columns) - 1;
                            $showBorder = !$lastColumn;
                            $align = data_get($value, 'align', 'justify-end');
                            $isFirst = $key === 0;
                        @endphp
                        <td
                            class="@if ($isFirst) table-column-fixed @endif @if ($field === 'id')  @endif text-wrap p-4 px-5 text-left font-light text-sm text-gray-600 @if ($showBorder) border-r border-gray-200 dark:border-gray-700 @endif dark:text-gray-300">
                            <div class="w-full flex {{ $align }}">
                                @if ($isFirst && $canViewDetails)
                                    <a href="{{ $moduleUrl . '/' . $item['_id'] }}"
                                        class="font-medium text-blue-600 dark:text-blue-300 hover:underline ">
                                @endif
                                {!! $item[$field] !!}
                                @if ($isFirst)
                                    </a>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @endif
    @endif
</tbody>
