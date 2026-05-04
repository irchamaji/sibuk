#!/bin/sh
set -e

# Tunggu PostgreSQL siap menerima koneksi
echo "Menunggu PostgreSQL..."
until php artisan db:monitor 2>/dev/null; do
    sleep 2
done
echo "PostgreSQL siap."

# Jalankan migrasi dan seeding jika belum ada data
php artisan migrate --force --seed 2>/dev/null || php artisan migrate --force

# Buat symbolic link storage jika belum ada
php artisan storage:link 2>/dev/null || true

# Set permission
chmod -R 775 storage bootstrap/cache

exec php-fpm
