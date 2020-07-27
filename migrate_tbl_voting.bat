rem composer dump-autoload
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan migrate:refresh --path=/database/migrations/table/2020_07_09_215908_create_voting.php --database=bittosolution --force
php artisan db:seed --class=AddDefaultVotingOptions --database=bittosolution --force
php artisan db:seed --class=AddDefaultVotingTopics --database=bittosolution --force
php artisan db:seed --class=AddDefaultVotingBindings --database=bittosolution --force
php artisan db:seed --class=AddDefaultVoters --database=bittosolution --force