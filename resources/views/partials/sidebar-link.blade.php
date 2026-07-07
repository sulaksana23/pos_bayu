{{--
    sidebar-link.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Reusable sidebar menu item.

    Variables (passed via @include):
      $route  — named route string, e.g. 'pos.dashboard'
      $match  — routeIs pattern,    e.g. 'pos.dashboard' or 'pos.cashier.*'
      $icon   — Font Awesome class,  e.g. 'fas fa-chart-line'
      $label  — Display text,        e.g. 'Dashboard'

    Active state:
      - Detected server-side via request()->routeIs($match)
      - Style: left border teal accent (3px) + soft teal/10 bg tint
      - No solid block — modern subtle treatment

    Collapsed state:
      - Label hidden, icon centered
      - Native `title` attribute provides browser tooltip (no JS lib needed)
--}}
@php
    $active = request()->routeIs($match);
@endphp

<a
    href="{{ route($route) }}"
    title="{{ $label }}"
    {{--
        :class binding handles:
          - justify-center when collapsed (icon only)
          - active teal accent vs default hover state
    --}}
    :class="{
        'justify-center px-0': $store.sidebar.collapsed,
        'px-3':                !$store.sidebar.collapsed,
    }"
    class="group relative flex items-center gap-3 rounded-lg py-2.5
           text-sm font-medium transition-all duration-150
           {{ $active
               ? 'border-l-[3px] border-teal-400 bg-teal-400/10 text-white pl-[calc(0.75rem-3px)]'
               : 'border-l-[3px] border-transparent text-slate-400 hover:bg-white/5 hover:text-white' }}"
>
    {{-- Icon — always visible, consistent 20px --}}
    <i class="{{ $icon }} w-5 shrink-0 text-center text-[18px]
              {{ $active ? 'text-teal-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>

    {{-- Label — hidden when sidebar is collapsed --}}
    <span
        x-show="!$store.sidebar.collapsed"
        x-transition:enter="transition-opacity duration-150 delay-75"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="truncate"
    >{{ $label }}</span>

    {{-- Active indicator dot (visible in collapsed state) --}}
    @if ($active)
        <span
            x-show="$store.sidebar.collapsed"
            class="absolute right-2 top-1/2 -translate-y-1/2 h-1.5 w-1.5 rounded-full bg-teal-400"
        ></span>
    @endif
</a>
