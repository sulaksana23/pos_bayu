@php
    $active = request()->routeIs($match);
@endphp

<a
    href="{{ route($route) }}"
    title="{{ $label }}"
    :class="{
        'justify-center px-0': !$store.sidebar.open && $store.sidebar.collapsed,
        'px-2.5':               $store.sidebar.open || !$store.sidebar.collapsed,
    }"
    class="nav-item {{ $active ? 'active' : '' }}"
>
    {{-- Icon --}}
    <i class="{{ $icon }} nav-icon {{ $active ? 'text-orange-500' : '' }}"></i>

    {{-- Label --}}
    <span
        x-show="$store.sidebar.showLabels"
        x-transition:enter="transition-opacity duration-150 delay-75"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="nav-label"
    >{{ $label }}</span>

    {{-- Active dot (collapsed state) --}}
    @if ($active)
        <span
            x-show="!$store.sidebar.showLabels"
            class="absolute right-2 top-1/2 -translate-y-1/2 h-1.5 w-1.5 rounded-full bg-orange-500"
        ></span>
    @endif
</a>
