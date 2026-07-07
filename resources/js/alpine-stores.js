/**
 * Alpine.js Global Stores — DevStack Admin Panel
 *
 * Load this file BEFORE Alpine.js initializes (i.e. before the Alpine CDN <script>).
 * It hooks into the `alpine:init` event so stores are registered at the right time.
 *
 * Stores defined here:
 *   - Alpine.store('sidebar')  — controls open/collapsed state + localStorage persistence
 *
 * Usage from any Alpine component:
 *   $store.sidebar.collapsed   → true/false
 *   $store.sidebar.open        → true/false (mobile drawer)
 *   $store.sidebar.toggle()    → flip mobile drawer
 *   $store.sidebar.toggleCollapse() → flip desktop collapse
 */

document.addEventListener('alpine:init', () => {

    // ─── Sidebar Store ────────────────────────────────────────────────────────
    Alpine.store('sidebar', {

        // Mobile drawer: off by default
        open: false,

        // Desktop collapsed (icon-only) state.
        // Read from localStorage so preference survives page reload.
        // Default: collapsed on md, expanded on lg — but we let JS decide based on
        // window width only on first visit (no saved preference).
        collapsed: (() => {
            const saved = localStorage.getItem('sidebar_collapsed');
            if (saved !== null) return saved === 'true';
            // First visit: collapse by default on tablets (< 1280px)
            return window.innerWidth < 1280;
        })(),

        /** Toggle mobile drawer (called by hamburger in navbar) */
        toggle() {
            this.open = !this.open;
        },

        /** Close mobile drawer (called by backdrop click) */
        close() {
            this.open = false;
        },

        /** Toggle desktop collapsed/expanded and persist to localStorage */
        toggleCollapse() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebar_collapsed', this.collapsed);
        },
    });

});
