import js from '@eslint/js';

export default [
  js.configs.recommended,
  {
    files: ['resources/**/*.js'],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'module',
      globals: {
        Alpine: 'readonly',
        window: 'readonly',
        document: 'readonly',
        console: 'readonly',
        fetch: 'readonly',
        FormData: 'readonly',
        URLSearchParams: 'readonly',
      },
    },
    rules: {
      'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
      'no-console': 'off',
      'prefer-const': 'warn',
      'no-var': 'error',
      quotes: ['warn', 'single', { avoidEscape: true }],
      semi: ['warn', 'always'],
      indent: ['warn', 2],
      'comma-dangle': ['warn', 'always-multiline'],
    },
  },
];
