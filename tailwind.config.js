/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Instrument Sans', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
      },
      colors: {
        'olsera-blue': '#0066FF',
        'olsera-blue-dark': '#0052CC',
        'olsera-blue-light': '#E6F0FF',
        'olsera-red': '#FF3366',
        'olsera-pink': '#FF6B9D',
      },
    },
  },
  plugins: [],
}
