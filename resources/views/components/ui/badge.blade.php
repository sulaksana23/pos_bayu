@props ([
    'variant' => 'default',
    'size' => 'md',
    'removable' => false,
])

@php
    $baseClasses = 'inline-flex items-center font-medium rounded-full transition-colors';
    
    $variants = [
        'default' => 'bg-slate-700 text-slate-200',
        'primary' => 'bg-orange-500/20 text-orange-300 border border-orange-500/30',
        'success' => 'bg-green-500/20 text-green-300 border border-green-500/30',
        'danger' => 'bg-red-500/20 text-red-300 border border-red-500/30',
        'warning' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
        'info' => 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
        'purple' => 'bg-purple-500/20 text-purple-300 border border-purple-500/30',
    ];
    
    $sizes = [
        'sm' => 'px-2 py-0.5 text-xs gap-1',
        'md' => 'px-2.5 py-1 text-xs gap-1.5',
        'lg' => 'px-3 py-1.5 text-sm gap-2',
    ];
    
    $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}

    @if ($removable)
        <button type="button" class="-mr-1 rounded-full p-0.5 transition-colors hover:bg-white/10">
            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    @endif
</span>
