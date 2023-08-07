#!/bin/sh
mkdir -p storage/app/export/recon_data/xlsx
mkdir -p storage/app/export/recon_dana/xlsx
php artisan key:generate
php artisan octane:start --host=0.0.0.0 --no-interaction
