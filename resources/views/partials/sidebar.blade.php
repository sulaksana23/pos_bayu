@php
    $isAdmin = auth()->user()?->isAdmin();
    $isManager = auth()->user()?->isManager();
    $canManage = auth()->user()?->canManageInventory();
@endphp
<div
    id="sidebarBackdrop"
    class="fixed inset-0 z-10 hidden bg-black/40 lg:hidden"
    onclick="window.toggleSidebar()"
></div>
<aside
    id="posSidebar"
    class="scroll-thin fixed top-16 bottom-0 left-0 z-20 w-64 -translate-x-full transform overflow-y-auto border-r border-gray-200 bg-white transition-transform duration-200 lg:translate-x-0"
>
    <nav class="space-y-0.5 p-4">

        {{-- Operasional --}}
        <p class="mb-2 px-3 text-[10px] font-bold tracking-wider text-gray-400 uppercase">Operasional</p>
        <a href="{{ route('pos.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('pos.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('pos.cashier.index') }}"
            class="sidebar-link {{ request()->routeIs('pos.cashier.*') ? 'active' : '' }}">
            <i class="fas fa-cash-register w-5 text-center"></i>
            <span>Kasir</span>
        </a>
        <a href="{{ route('pos.shifts.index') }}"
            class="sidebar-link {{ request()->routeIs('pos.shifts.*') ? 'active' : '' }}">
            <i class="fas fa-business-time w-5 text-center"></i>
            <span>Shift</span>
        </a>

        {{-- Katalog --}}
        <p class="mt-5 mb-2 px-3 text-[10px] font-bold tracking-wider text-gray-400 uppercase">Katalog</p>
        <a href="{{ route('pos.products.index') }}"
            class="sidebar-link {{ request()->routeIs('pos.products.*') ? 'active' : '' }}">
            <i class="fas fa-box w-5 text-center"></i>
            <span>Produk</span>
        </a>
        <a href="{{ route('pos.categories.index') }}"
            class="sidebar-link {{ request()->routeIs('pos.categories.*') ? 'active' : '' }}">
            <i class="fas fa-tag w-5 text-center"></i>
            <span>Kategori</span>
        </a>
        <a href="{{ route('pos.inventory.index') }}"
            class="sidebar-link {{ request()->routeIs('pos.inventory.*') ? 'active' : '' }}">
            <i class="fas fa-boxes-stacked w-5 text-center"></i>
            <span>Stok & Gudang</span>
        </a>
        <a href="{{ route('pos.customers.index') }}"
            class="sidebar-link {{ request()->routeIs('pos.customers.*') ? 'active' : '' }}">
            <i class="fas fa-users w-5 text-center"></i>
            <span>Pelanggan</span>
        </a>

        {{-- Laporan & Keuangan --}}
        <p class="mt-5 mb-2 px-3 text-[10px] font-bold tracking-wider text-gray-400 uppercase">Keuangan</p>
        <a href="{{ route('pos.accounting.index') }}"
            class="sidebar-link {{ request()->routeIs('pos.accounting.index') ? 'active' : '' }}">
            <i class="fas fa-calculator w-5 text-center"></i>
            <span>Akunting</span>
        </a>
        <a href="{{ route('pos.accounting.cash-drawer') }}"
            class="sidebar-link {{ request()->routeIs('pos.accounting.cash-drawer') ? 'active' : '' }}">
            <i class="fas fa-cash-register w-5 text-center"></i>
            <span>Laci Kas</span>
        </a>

        {{-- Laporan --}}
        <p class="mt-5 mb-2 px-3 text-[10px] font-bold tracking-wider text-gray-400 uppercase">Laporan</p>
        <a href="{{ route('pos.reports.sales') }}"
            class="sidebar-link {{ request()->routeIs('pos.reports.sales') ? 'active' : '' }}">
            <i class="fas fa-chart-bar w-5 text-center"></i>
            <span>Lap. Penjualan</span>
        </a>
        <a href="{{ route('pos.reports.products') }}"
            class="sidebar-link {{ request()->routeIs('pos.reports.products') ? 'active' : '' }}">
            <i class="fas fa-chart-pie w-5 text-center"></i>
            <span>Lap. Produk</span>
        </a>
        <a href="{{ route('pos.reports.customers') }}"
            class="sidebar-link {{ request()->routeIs('pos.reports.customers') ? 'active' : '' }}">
            <i class="fas fa-user-chart w-5 text-center"></i>
            <span>Lap. Pelanggan</span>
        </a>
        <a href="{{ route('pos.reports.inventory') }}"
            class="sidebar-link {{ request()->routeIs('pos.reports.inventory') ? 'active' : '' }}">
            <i class="fas fa-warehouse w-5 text-center"></i>
            <span>Lap. Inventaris</span>
        </a>

        {{-- Admin --}}
        @if ($isAdmin)
        <p class="mt-5 mb-2 px-3 text-[10px] font-bold tracking-wider text-gray-400 uppercase">Admin</p>
        <a href="{{ route('pos.users.index') }}"
            class="sidebar-link {{ request()->routeIs('pos.users.*') ? 'active' : '' }}">
            <i class="fas fa-user-cog w-5 text-center"></i>
            <span>Pengguna</span>
        </a>
        @endif

        {{-- Akun --}}
        <p class="mt-5 mb-2 px-3 text-[10px] font-bold tracking-wider text-gray-400 uppercase">Akun</p>
        <button
            type="button"
            onclick="document.getElementById('logout-form').submit()"
            class="sidebar-link w-full text-red-600 hover:!bg-red-50 hover:!text-red-700"
        >
            <i class="fas fa-sign-out-alt w-5 text-center text-red-500"></i>
            <span>Logout</span>
        </button>
    </nav>

    @isset ($currentShift)
        @if ($currentShift)
            <div class="m-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-3 text-white shadow-lg shadow-emerald-500/30">
                <p class="text-[10px] tracking-wider uppercase opacity-80">Shift Aktif</p>
                <p class="mt-0.5 text-lg font-bold">{{ $currentShift->opened_at->format('H:i') }} WITA</p>
                <p class="mt-1 text-xs opacity-80">{{ $currentShift->user->name }}</p>
                <a href="{{ route('pos.shifts.index') }}"
                    class="mt-3 inline-flex items-center gap-1 rounded-lg bg-white/15 px-2 py-1.5 text-[11px] font-semibold backdrop-blur transition hover:bg-white/25">
                    Tutup shift <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        @endif
    @endisset
</aside>

<style>
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.875rem;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #4b5563;
        border-radius: 0.625rem;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .sidebar-link:hover:not(.active):not(.text-blue-600):not(.text-red-600) {
        background: #f3f4f6;
        color: #111827;
    }
    .sidebar-link:hover:not(.active) i {
        color: #6b7280;
    }
    .sidebar-link.active {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white !important;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.35);
    }
    .sidebar-link.active i {
        color: white !important;
    }
    .sidebar-link i {
        color: #9ca3af;
        transition: color 0.15s ease;
    }
    @media (max-width: 1023.98px) {
        #posSidebar.sidebar-open {
            --tw-translate-x: 0px;
        }
    }
</style>

<script>
    window.toggleSidebar = function () {
        const sb = document.getElementById('posSidebar');
        const bd = document.getElementById('sidebarBackdrop');
        if (sb) {
            const isOpen = sb.classList.toggle('sidebar-open');
            if (bd) bd.classList.toggle('hidden', !isOpen);
        }
    };
    document.addEventListener('click', e => {
        if (window.innerWidth < 1024) {
            const sb = document.getElementById('posSidebar');
            const isClickInside = sb?.contains(e.target);
            const isToggle =
                e.target.closest('[onclick="window.toggleSidebar()"]') ||
                e.target.closest('button[aria-label="Toggle menu"]');
            if (sb?.classList.contains('sidebar-open') && !isClickInside && !isToggle) {
                sb.classList.remove('sidebar-open');
                const bd = document.getElementById('sidebarBackdrop');
                if (bd) bd.classList.add('hidden');
            }
        }
        const am = document.getElementById('accountMenu');
        if (
            am &&
            !am.contains(e.target) &&
            !e.target.closest('[onclick="window.toggleAccountMenu()"]')
        ) {
            am.classList.add('hidden');
        }
    });
</script>
