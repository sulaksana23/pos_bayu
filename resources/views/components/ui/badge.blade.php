@props ([
    'variant'  => 'default',
    'size'     => 'md',
    'dot'      => false,
    'removable' => false,
])

@php
    $base = 'inline-flex items-center font-semibold rounded-full transition-colors';

    $variants = [
        'default'  => 'bg-gray-100 text-gray-600',
        'primary'  => 'bg-orange-100 text-orange-700',
        'success'  => 'bg-emerald-100 text-emerald-700',
        'danger'   => 'bg-red-100 text-red-700',
        'warning'  => 'bg-amber-100 text-amber-700',
        'info'     => 'bg-blue-100 text-blue-700',
        'purple'   => 'bg-purple-100 text-purple-700',
        'indigo'   => 'bg-indigo-100 text-indigo-700',
        'pink'     => 'bg-pink-100 text-pink-700',
    ];

    $sizes = [
        'xs' => 'px-1.5 py-0.5 text-[10px] gap-1',
        'sm' => 'px-2 py-0.5 text-xs gap-1',
        'md' => 'px-2.5 py-1 text-xs gap-1.5',
        'lg' => 'px-3 py-1.5 text-sm gap-2',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    @endif

    {{ $slot }}

    @if ($removable)
        <button type="button" class="-mr-0.5 rounded-full p-0.5 transition-colors hover:bg-black/10">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
</span>
