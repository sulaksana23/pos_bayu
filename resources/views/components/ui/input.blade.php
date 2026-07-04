@props ([
    'type' => 'text',
    'label' => null,
    'error' => null,
    'hint' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'disabled' => false,
    'required' => false,
])

@php
    $inputId = $attributes->get('id', 'input-' . Str::random(8));
    $baseClasses = 'block w-full rounded-lg border bg-slate-800/50 text-slate-200 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed';
    
    $errorClasses = $error ? 'border-red-500 focus:ring-red-500' : 'border-slate-600 hover:border-slate-500';
    
    $iconClasses = $icon ? ($iconPosition === 'left' ? 'pl-10' : 'pr-10') : '';
    
    $sizeClasses = 'px-4 py-2.5 text-sm';
    
    $classes = $baseClasses . ' ' . $errorClasses . ' ' . $iconClasses . ' ' . $sizeClasses;
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $inputId }}" class="mb-2 block text-sm font-medium text-slate-300">
            {{ $label }}
            @if ($required)
                <span class="text-red-400">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($icon && $iconPosition === 'left')
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-dynamic-component :component="$icon" class="h-5 w-5 text-slate-400" />
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            {{ $attributes->except(['class'])->merge(['class' => $classes]) }}
            @if ($disabled) disabled @endif
            @if ($required) required @endif
        />

        @if ($icon && $iconPosition === 'right')
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <x-dynamic-component :component="$icon" class="h-5 w-5 text-slate-400" />
            </div>
        @endif
    </div>

    @if ($error)
        <p class="mt-2 flex items-center gap-1 text-sm text-red-400">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            {{ $error }}
        </p>
    @elseif ($hint)
        <p class="mt-2 text-sm text-slate-400">{{ $hint }}</p>
    @endif
</div>
