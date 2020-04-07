## MyTinyWMS - Open Source Warehouse Management System

MyTinyWMS is a free warehouse management system software. It helps small and medium-sized companies with inventory management and the organisation of purchasing.

Build on [Laravel 6](http://laravel.com)

Demo: [click here](https://demo.mytinywms.com)  
The demo will be reset every 24h.

Supported languages: english, german

-----

### Installation

The easiest way to install it right now is using docker and docker-compose:

- copy docker-compose file: `cp docker-compose-prod.yml docker-compose.yml`
- copy .env example file: `cp docker-compose.env_file.sample .env`
- adjust .env file:
    - generate app key using `docker run -t mytinywms/mytinywms php artisan key:generate --show`
    - enter the full content to the .env file (including base64:...)
    - set passwords for database users
- run `docker-compose up -d`
- go to the admin section (gear symbol in the right top corner) and adjust the email settings

-----

### Security

To report a security vulnerability, please email security@mytinywms.com instead of using the issue tracker. 