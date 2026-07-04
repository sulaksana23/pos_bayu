#!/bin/bash
# Laravel Optimization Script for balitech-pos

echo "Starting Laravel optimization..."

# Clear all caches first
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Caches cleared."

# Cache configuration
php artisan config:cache
echo "Config cached."

# Cache routes
php artisan route:cache
echo "Routes cached."

# Cache views
php artisan view:cache
echo "Views cached."

# Optimize autoloader
composer dump-autoload -o
echo "Autoloader optimized."

# Run full optimization
php artisan optimize
echo "Full optimization complete."

# Set proper permissions
chmod -R 775 storage bootstrap/cache
echo "Permissions set."

echo "All optimizations complete!"
