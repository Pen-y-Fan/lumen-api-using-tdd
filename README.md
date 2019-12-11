# Lumen API using TDD

This app is based on [APIs in Laravel using TDD](https://www.youtube.com/playlist?list=PL3ZhWMazGi9KGG64X_HJlZ_sQuvyFGoMo), except Lumen is used. Inspiration was also taken from [Lumen RealWorld Example App](https://github.com/elcobvg/lumen-realworld-example-app), as well as the [Official Documentation](https://lumen.laravel.com/docs).

Test Driven Development (TDD) techniques, from APIs in Laravel using TDD, were used to write the tests in Lumen, using PHPUnit to develop the app.

This is a basic API CRUD app, with no auth or validation checking. It is intended as a basic test API. It should **never** be used in production.

## Installation

Please check the official Lumen installation guide for server requirements before you start. [Official Documentation](https://lumen.laravel.com/docs)

Create a directory and switch to the repo folder

```sh
md lumen-api-using-tdd
cd lumen-api-using-tdd
```

Clone the repository

```sh
git clone https://github.com/Pen-y-Fan/lumen-api-using-tdd.git
```

Install all the dependencies using composer

```sh
composer install
```

Copy the example env file and make the required configuration changes in the .env file

```sh
copy .env.example .env
```

Generate a new application key

Since Lumen doesn't have the `php artisan key:generate` command, there's a custom route <http://127.0.0.1:8000/appKey> to help you generate an application key. See below to start the local development server first, stop the server after generating the key and amending the .env file, to reload the new settings.

Run the database migrations (**Set the database connection in .env before migrating**)

```sh
php artisan migrate
```

You can now access the server at <http://127.0.0.1:8000>

## TL;DR command list (Windows)

```sh
md lumen-api-using-tdd
cd lumen-api-using-tdd
git clone https://github.com/Pen-y-Fan/lumen-api-using-tdd.git
composer install
copy .env.example .env
```

**Make sure you set the correct database connection information and enter an application key before running the migrations** [Environment variables](#environment-variables)

```sh
php artisan migrate
php -S 127.0.0.1:8000 -t public
```

## Database seeding

Run the database seeder and you're done.

```sh
php artisan db:seed
```

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command (remove --seed for an empty database).

```sh
php artisan migrate:refresh --seed
```

## Testing

To run tests in windows:

```sh
composer tests
```

***Note*** : To allow the tests run run quickly, the tests use sqlite in memory database. The `extension=pdo_sqlite` may need to be enabled in `php.ini` file, this is not normally enabled by default.

## Folders

- `app` - Contains the Product model
- `app/Http/Controllers` - Contains the Product api controller
- `app/Http/Resources` - Contains the Product resource and collection
- `database/factories` - Contains the model factory for the model
- `database/migrations` - Contains the database migrations
- `database/seeds` - Contains the database seeder
- `routes` - Contains all the api routes defined in **web.php** file
- `tests/Feature/Http/Controllers` - Contains all the api tests

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

## API Specification

REST API for Product

| Request | Endpoint | Description
| --- | --- | ---
| GET | /api/product | List of Products, paginated
| GET | /api/product/{id} | Single product, by id number
| POST | /api/product | Create a product, name and price
| DELETE | /api/product/{id} | Delete a product by its id number
| PUT | /api/product/{id} | Update a product, by id number

## Examples

The following examples are available in the `examples.rest` file, which can be used with [REST Client](https://marketplace.visualstudio.com/items?itemName=humao.rest-client) VS Code extension.

```text
@hostname = http://127.0.0.1
@port = 8000
@host = {{hostname}}:{{port}}
@contentType = application/json
```

***Note*** : VS Code and therefore REST Client with `@hostname = http://localhost` does not work on windows.

### Index of first 15 products

```text
GET {{host}}/api/product HTTP/1.1
```

### Index of next 15 products (page 2)

```text
GET {{host}}/api/product?page=2 HTTP/1.1
```

### Show an existing product

```text
GET {{host}}/api/product/1 HTTP/1.1
```

### Update an existing product

```text
PUT {{host}}/api/product/1 HTTP/1.1
content-type: {{contentType}}

{
    "name": "sample",
    "price": 71
}
```

### Create a new product

***Note*** : Product name must be unique as slug is generated from the name, which is a unique field.

```text
POST {{host}}/api/product HTTP/1.1
content-type: {{contentType}}

{
    "name": "sample4",
    "price": 81
}
```

### Delete an existing product

```text
DELETE {{host}}/api/product/2 HTTP/1.1
content-type: {{contentType}}
```

## Contributions

Comments are always welcome. This is a basic test API personal project, as such contributions are not expected. Feel free to fork, if you find this project useful and which to expand it for your own personal project.

## License

This app and the Lumen framework are open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
