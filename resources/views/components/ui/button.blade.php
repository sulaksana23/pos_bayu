@props ([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
])

@php
    $base = 'inline-flex items-center justify-center font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary'   => 'bg-orange-500 hover:bg-orange-600 text-white focus:ring-orange-400 shadow-sm',
        'secondary' => 'bg-gray-100 hover:bg-gray-200 text-gray-700 focus:ring-gray-300 border border-gray-200',
        'success'   => 'bg-emerald-500 hover:bg-emerald-600 text-white focus:ring-emerald-400 shadow-sm',
        'danger'    => 'bg-red-500 hover:bg-red-600 text-white focus:ring-red-400 shadow-sm',
        'warning'   => 'bg-amber-500 hover:bg-amber-600 text-white focus:ring-amber-400 shadow-sm',
        'ghost'     => 'bg-transparent hover:bg-gray-100 text-gray-600 hover:text-gray-900 focus:ring-gray-300',
        'outline'   => 'border border-gray-200 hover:border-orange-300 bg-white text-gray-600 hover:text-orange-600 focus:ring-orange-300',
        'link'      => 'bg-transparent text-orange-500 hover:text-orange-600 underline-offset-4 hover:underline focus:ring-orange-300',
    ];

    $sizes = [
        'xs' => 'px-2.5 py-1.5 text-xs rounded-md gap-1.5',
        'sm' => 'px-3 py-1.5 text-xs rounded-lg gap-1.5',
        'md' => 'px-4 py-2 text-sm rounded-lg gap-2',
        'lg' => 'px-5 py-2.5 text-sm rounded-xl gap-2',
        'xl' => 'px-6 py-3 text-base rounded-xl gap-2.5',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if ($disabled || $loading) disabled @endif
>
    @if ($loading)
        <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
        </svg>
    @elseif ($icon && $iconPosition === 'left')
        <i class="{{ $icon }} text-[13px]"></i>
    @endif

    {{ $slot }}

    @if (!$loading && $icon && $iconPosition === 'right')
        <i class="{{ $icon }} text-[13px]"></i>
    @endif
</button>
