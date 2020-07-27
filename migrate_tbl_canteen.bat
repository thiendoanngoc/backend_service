rem composer dump-autoload
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan migrate:refresh --path=/database/migrations/table/2020_07_08_093944_create_canteen.php --database=bittosolution --force
php artisan db:seed --class=AddDefaultCanteenRegistrations --database=bittosolution --force