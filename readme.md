> [!WARNING]
>
> ## Archived and no longer maintained
>
> This project has been archived and is no longer actively maintained.
>
> The application is based on outdated and unsupported framework and dependency versions. It may contain known or unknown security vulnerabilities and should not be used for new installations or exposed to the public internet.
>
> No further support, bug fixes, compatibility updates, or security updates will be provided for the public version of this project.
>
> Existing users should migrate to a maintained alternative as soon as possible.


## MyTinyWMS - Open Source Warehouse Management System

MyTinyWMS is a free warehouse management system software. It helps small and medium-sized companies with inventory management and the organisation of purchasing.

Build on [Laravel 6](http://laravel.com)

Demo: [click here](https://demo.mytinywms.com)  
The demo will be reset every 24h.

Supported languages: english, german

-----

### Installation

The easiest way to install it right now is using docker and docker-compose:

- copy docker-compose file: `cp docker-compose.prod.yml docker-compose.yml`
- copy .env example file: `cp .env.example .env`
- adjust .env file:
    - generate app key using online generator like https://generate-random.org/laravel-key-generator
    - enter the full content to the .env file (including base64:...)
    - set passwords for database users (DB_PASSWORD AND DB_ROOT_PASSWORD)
- run `docker-compose up -d`
- go to the admin section (gear symbol in the right top corner) and adjust the email settings

-----

### Development

... todo: fill this stuff ...
- run `composer install`
- run `php artisan telescope:publish`

-----

### Security

To report a security vulnerability, please email security@mytinywms.com instead of using the issue tracker. 
