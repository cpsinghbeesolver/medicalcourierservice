Run `php artisan serve` to start the server.

php artisan serve
php artisan migrate
php artisan l5-swagger:generate

## Installation
```bash
Laravel version 12
PHP 8.5.4
Mysql db Server version: 10.4.32-MariaDB

git clone https://github.com/BeeDeveloperIn/MedicalCourierService.git
cd project
composer install
php artisan key:generate
php artisan queue:work

composer update # install dependencies
php artisan serve  # start server

php artisan migrate:fresh --seed
php artisan migrate
php artisan db:seed
php artisan l5-swagger:generate

php artisan reverb:start
npm run dev
# medicalcourierservice
