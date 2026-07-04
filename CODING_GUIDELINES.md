# Clean UI Coding Guidelines - Balitech POS

## Struktur Project

```
resources/
├── js/
│   ├── app.js              # Alpine.js initialization
│   └── components/         # Alpine components
├── css/
│   └── app.css            # Tailwind & custom styles
└── views/
    ├── layouts/           # Layout templates
    ├── components/        # Reusable Blade components
    └── pos/              # Feature views
```

## Alpine.js Best Practices

### 1. Component Structure
```javascript
// ✅ Good: Clean, organized component
Alpine.data('productSearch', () => ({
  query: '',
  results: [],
  loading: false,
  
  async search() {
    if (this.query.length < 3) return;
    
    this.loading = true;
    try {
      const response = await fetch(`/api/products?q=${this.query}`);
      this.results = await response.json();
    } finally {
      this.loading = false;
    }
  },
}));

// ❌ Bad: Inline logic in HTML
<div x-data="{ products: [] }" x-init="fetch('/api/products').then(r => r.json()).then(d => products = d)">
```

### 2. Naming Conventions
- Data properties: `camelCase`
- Methods: `camelCase`
- Component names: `kebab-case`

### 3. State Management
```javascript
// ✅ Good: Centralized store for shared state
Alpine.store('cart', {
  items: [],
  total: 0,
  
  addItem(product) {
    this.items.push(product);
    this.calculateTotal();
  },
  
  calculateTotal() {
    this.total = this.items.reduce((sum, item) => sum + item.price, 0);
  },
});
```

## Tailwind CSS Best Practices

### 1. Component Classes
```blade
{{-- ✅ Good: Reusable component classes --}}
<button class="btn btn-primary">
  Save
</button>

{{-- Define in app.css --}}
@layer components {
  .btn {
    @apply px-4 py-2 rounded-lg font-medium transition-colors;
  }
  
  .btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700;
  }
}
```

### 2. Responsive Design
```blade
{{-- ✅ Good: Mobile-first approach --}}
<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
  <!-- content -->
</div>
```

### 3. Spacing & Layout
- Use consistent spacing scale: `4, 8, 12, 16, 20, 24, 32, 40, 48, 64`
- Group related elements with `space-y-*` or `space-x-*`
- Use flexbox/grid for layouts, avoid absolute positioning

## Blade Templates Best Practices

### 1. Component Structure
```blade
{{-- ✅ Good: Reusable component --}}
@props([
    'title',
    'subtitle' => null,
    'actions' => null,
])

<div class="card">
  <div class="card-header">
    <h2 class="card-title">{{ $title }}</h2>
    @if($subtitle)
      <p class="card-subtitle">{{ $subtitle }}</p>
    @endif
  </div>
  
  <div class="card-body">
    {{ $slot }}
  </div>
  
  @if($actions)
    <div class="card-actions">
      {{ $actions }}
    </div>
  @endif
</div>
```

### 2. Naming Conventions
- Blade components: `kebab-case` (e.g., `product-card.blade.php`)
- Component props: `camelCase`
- Slots: descriptive names (`header`, `footer`, `actions`)

### 3. File Organization
```
views/
├── components/
│   ├── ui/
│   │   ├── button.blade.php
│   │   ├── card.blade.php
│   │   └── modal.blade.php
│   └── pos/
│       ├── product-card.blade.php
│       └── cart-item.blade.php
```

## Code Quality

### Format & Lint Commands
```bash
# Format Blade & JS files
npm run format

# Check formatting
npm run format:check

# Fix JavaScript issues
npm run lint:fix

# Format PHP files
./vendor/bin/pint

# Check PHP formatting
./vendor/bin/pint --test
```

### Pre-commit Checklist
- [ ] Run `npm run format` untuk format JS/Blade
- [ ] Run `./vendor/bin/pint` untuk format PHP
- [ ] Run `npm run lint` untuk check JS errors
- [ ] Test UI di mobile & desktop views
- [ ] Check console untuk errors

## Performance Tips

### 1. Alpine.js
- Use `x-cloak` untuk prevent flash of unstyled content
- Debounce expensive operations (search, API calls)
- Use `x-show` untuk toggle visibility (keeps in DOM)
- Use `x-if` untuk conditional rendering (removes from DOM)

### 2. Tailwind CSS
- Avoid `@apply` untuk one-off styles
- Use JIT mode (enabled by default in v4)
- Purge unused styles in production

### 3. Images & Assets
- Use modern formats (WebP, AVIF)
- Lazy load images below the fold
- Optimize image sizes

## Accessibility

### ARIA Labels
```blade
<button 
  aria-label="Add to cart"
  class="btn btn-icon"
>
  <svg><!-- icon --></svg>
</button>
```

### Keyboard Navigation
- Ensure all interactive elements are keyboard accessible
- Use proper tab order
- Add focus styles

### Color Contrast
- Minimum contrast ratio 4.5:1 for text
- Use Tailwind's color palette wisely

## VSCode Extensions Recommended

1. **Prettier - Code formatter**
2. **ESLint**
3. **Laravel Pint**
4. **Tailwind CSS IntelliSense**
5. **Alpine.js IntelliSense**
6. **Laravel Blade Snippets**

## Resources

- [Alpine.js Documentation](https://alpinejs.dev)
- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Laravel Blade Documentation](https://laravel.com/docs/blade)
