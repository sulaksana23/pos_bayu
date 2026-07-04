@props ([
    'id',
    'title' => null,
    'size' => 'md',
    'closeButton' => true,
])

@php
    $sizes = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
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
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"
        @click="open = false"
    ></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            {{ $attributes->merge(['class' => 'relative w-full ' . $sizes[$size] . ' bg-slate-800 rounded-2xl shadow-2xl border border-slate-700']) }}
        >
            @if ($title || $closeButton)
                <div class="flex items-center justify-between border-b border-slate-700 px-6 py-4">
                    @if ($title)
                        <h3 class="text-lg font-semibold text-slate-200" id="modal-title">
                            {{ $title }}
                        </h3>
                    @endif

                    @if ($closeButton)
                        <button
                            type="button"
                            @click="open = false"
                            class="rounded-lg p-1 text-slate-400 transition-colors hover:bg-slate-700 hover:text-slate-200"
                        >
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif
                </div>
            @endif

            <div class="px-6 py-4">{{ $slot }}</div>
        </div>
    </div>
</div>
