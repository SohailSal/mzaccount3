## About MZ Accounting

This is a basic accounting software for a chartered accountants firm. It is capable of generating invoices having basic CRM capability and complete double entry system. It can generate basic reports including ledgers and trial balance.

## Requirements

- PHP 8.2 or later with the extensions required by Laravel and PhpSpreadsheet
- Composer 2
- Node.js 18 or later and npm

## Local setup

```sh
composer install
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env`, then run migrations and start Laravel:

```sh
php artisan migrate
php artisan serve
```

Install the frontend dependencies and compile the existing assets with `npm ci` and `npm run dev` (or `npm run prod` for production).

## License

This software is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
