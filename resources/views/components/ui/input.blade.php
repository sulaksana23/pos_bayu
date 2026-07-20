@props ([
    'type'         => 'text',
    'label'        => null,
    'error'        => null,
    'hint'         => null,
    'icon'         => null,
    'iconPosition' => 'left',
    'disabled'     => false,
    'required'     => false,
    'prefix'       => null,
    'suffix'       => null,
])

@php
    $inputId  = $attributes->get('id', 'input-' . Str::random(6));
    $hasError = (bool) $error;

    $base = 'block w-full rounded-lg border bg-white text-sm text-gray-900 placeholder-gray-400 transition-all duration-150 focus:outline-none focus:ring-2 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed';
    $borderCls = $hasError ? 'border-red-400 focus:border-red-400 focus:ring-red-400/20' : 'border-gray-200 hover:border-gray-300 focus:border-orange-400 focus:ring-orange-400/20';
    $paddingCls = 'py-2 ';
    $paddingCls .= $icon && $iconPosition === 'left'  ? 'pl-9 pr-3'  : '';
    $paddingCls .= $icon && $iconPosition === 'right' ? 'pl-3 pr-9'  : '';
    $paddingCls .= (!$icon && !$prefix && !$suffix)   ? 'px-3'       : '';
    $paddingCls .= $prefix                             ? 'pl-10 pr-3' : '';
    $paddingCls .= $suffix                             ? 'pl-3 pr-10' : '';

    $classes = $base . ' ' . $borderCls . ' ' . $paddingCls;
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $inputId }}" class="mb-1.5 block text-xs font-semibold text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        {{-- Left icon --}}
        @if ($icon && $iconPosition === 'left')
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="{{ $icon }} text-xs text-gray-400"></i>
            </div>
        @endif

        {{-- Prefix --}}
        @if ($prefix)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <span class="text-sm text-gray-400">{{ $prefix }}</span>
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            {{ $attributes->except('class')->merge(['class' => $classes]) }}
            @if ($disabled) disabled @endif
            @if ($required) required @endif
        />

        {{-- Right icon --}}
        @if ($icon && $iconPosition === 'right')
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <i class="{{ $icon }} text-xs text-gray-400"></i>
            </div>
        @endif

        {{-- Suffix --}}
        @if ($suffix)
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <span class="text-sm text-gray-400">{{ $suffix }}</span>
            </div>
        @endif
    </div>

    @if ($hasError)
        <p class="mt-1 flex items-center gap-1 text-xs text-red-500">
            <i class="fas fa-exclamation-circle text-[10px]"></i>
            {{ $error }}
        </p>
    @elseif ($hint)
        <p class="mt-1 text-xs text-gray-400">{{ $hint }}</p>
    @endif
</div>
