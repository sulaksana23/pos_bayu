@props ([
    'variant' => 'default',
    'padding' => 'md',
    'hover' => false,
])

@php
    $baseClasses = 'bg-slate-800/50 backdrop-blur-sm rounded-xl transition-all duration-200';
    
    $variants = [
        'default' => 'border border-slate-700/50',
        'glass' => 'border border-white/10 backdrop-blur-md bg-white/5',
        'solid' => 'bg-slate-800 border border-slate-700',
        'gradient' => 'bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50',
        'premium' => 'bg-gradient-to-br from-orange-500/10 to-purple-500/10 border border-orange-500/20 shadow-glow',
    ];
    
    $paddings = [
        'none' => '',
        'sm' => 'p-3',
        'md' => 'p-4',
        'lg' => 'p-6',
        'xl' => 'p-8',
    ];
    
    $hoverClass = $hover ? 'hover:shadow-lg hover:border-slate-600 cursor-pointer' : '';
    
    $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $paddings[$padding] . ' ' . $hoverClass;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</div>
