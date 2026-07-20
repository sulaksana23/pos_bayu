@props ([
    'variant' => 'default',
    'padding' => 'md',
    'hover'   => false,
    'header'  => null,
])

@php
    $base = 'bg-white rounded-xl border border-gray-200 transition-all duration-150';

    $variants = [
        'default'  => 'shadow-sm',
        'flat'     => 'shadow-none',
        'elevated' => 'shadow-card-lg',
        'orange'   => 'border-orange-200 bg-orange-50',
        'green'    => 'border-emerald-200 bg-emerald-50',
        'red'      => 'border-red-200 bg-red-50',
        'blue'     => 'border-blue-200 bg-blue-50',
    ];

    $paddings = [
        'none' => '',
        'xs'   => 'p-2',
        'sm'   => 'p-3',
        'md'   => 'p-4',
        'lg'   => 'p-5',
        'xl'   => 'p-6',
    ];

    $hoverClass = $hover ? 'hover:shadow-card-lg hover:border-gray-300 cursor-pointer' : '';

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($paddings[$padding] ?? $paddings['md']) . ' ' . $hoverClass;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if ($header)
        <div class="fb-card-header">
            {{ $header }}
        </div>
        <div class="{{ $padding !== 'none' ? ($paddings[$padding] ?? 'p-4') : '' }}">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>
