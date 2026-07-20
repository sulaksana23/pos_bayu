@props ([
    'id',
    'title' => null,
    'size' => 'md',
    'closeButton' => true,
])

@php
    $sizes = [
        'sm'   => 'max-w-md',
        'md'   => 'max-w-lg',
        'lg'   => 'max-w-2xl',
        'xl'   => 'max-w-4xl',
        'full' => 'max-w-full mx-4',
    ];
@endphp

<div
    x-data="{ open: false }"
    x-show="open"
    @open-modal-{{ $id }}.window="open = true"
    @close-modal-{{ $id }}.window="open = false"
    @keydown.escape.window="open = false"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title-{{ $id }}"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] transition-opacity"
        @click="open = false"
    ></div>

    {{-- Modal panel --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            {{ $attributes->merge(['class' => 'relative w-full ' . ($sizes[$size] ?? $sizes['md']) . ' bg-white rounded-xl shadow-xl border border-gray-200']) }}
        >
            {{-- Header --}}
            @if ($title || $closeButton)
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    @if ($title)
                        <h3 class="text-sm font-bold text-gray-900" id="modal-title-{{ $id }}">
                            {{ $title }}
                        </h3>
                    @endif
                    @if ($closeButton)
                        <button
                            type="button"
                            @click="open = false"
                            class="ml-auto flex h-7 w-7 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                            aria-label="Tutup"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @endif

            {{-- Body --}}
            <div class="px-5 py-4">{{ $slot }}</div>
        </div>
    </div>
</div>
