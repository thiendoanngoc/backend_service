# composer dump-autoload
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan migrate:fresh --database=masterdb --seed --force
php artisan migrate:fresh --database=bittosolution --seed --force