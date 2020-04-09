# stop app
php artisan down

# refresh database
php artisan migrate:fresh --force
php artisan db:seed --class=BaseSeeder --force
php artisan db:seed --class=DemoDataSeeder --force
php artisan articlenumbers:set --force

# clear file storage
rm -rf storage/*
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
chmod -R 777 storage

# start app
php artisan up