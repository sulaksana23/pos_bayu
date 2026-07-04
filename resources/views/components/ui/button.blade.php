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
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $variants = [
        'primary' => 'bg-orange-500 hover:bg-orange-600 text-white focus:ring-orange-500 shadow-sm hover:shadow-md',
        'secondary' => 'bg-slate-700 hover:bg-slate-600 text-white focus:ring-slate-500 shadow-sm hover:shadow-md',
        'success' => 'bg-green-500 hover:bg-green-600 text-white focus:ring-green-500 shadow-sm hover:shadow-md',
        'danger' => 'bg-red-500 hover:bg-red-600 text-white focus:ring-red-500 shadow-sm hover:shadow-md',
        'warning' => 'bg-amber-500 hover:bg-amber-600 text-white focus:ring-amber-500 shadow-sm hover:shadow-md',
        'ghost' => 'bg-transparent hover:bg-slate-800 text-slate-300 hover:text-white focus:ring-slate-500',
        'outline' => 'border-2 border-slate-600 hover:border-slate-500 bg-transparent text-slate-300 hover:text-white focus:ring-slate-500',
        'link' => 'bg-transparent text-orange-400 hover:text-orange-300 underline-offset-4 hover:underline focus:ring-orange-500',
    ];
    
    $sizes = [
        'xs' => 'px-2.5 py-1.5 text-xs rounded-md gap-1.5',
        'sm' => 'px-3 py-2 text-sm rounded-md gap-2',
        'md' => 'px-4 py-2.5 text-sm rounded-lg gap-2',
        'lg' => 'px-5 py-3 text-base rounded-lg gap-2.5',
        'xl' => 'px-6 py-3.5 text-base rounded-xl gap-3',
    ];
    
    $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if ($disabled || $loading) disabled @endif
>
    @if ($loading)
        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @elseif ($icon && $iconPosition === 'left')
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    @endif

    {{ $slot }}

    @if ($icon && $iconPosition === 'right' && !$loading)
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    @endif
</button>
