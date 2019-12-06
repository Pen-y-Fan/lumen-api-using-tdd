# APIs in Lumen Using TDD

Based on:

- [APIs in Laravel Using TDD](https://www.youtube.com/playlist?list=PL3ZhWMazGi9KGG64X_HJlZ_sQuvyFGoMo)
- [Github - Laravel API using API resources and TDD.](https://github.com/devlob/apis-in-laravel-using-tdd)

Notes: code snippets are often used to highlight the code changed, any code prior or post the code snipped is generally unchanged from previous notes, or to highlight only the output of interest. To signify a snippet of a larger code block, dots are normally used e.g.

```php
\\ ...
echo "Hello";
\\ ...
```

For setup see readme.md

## Setup

```sh
lumen new lumen-api
```

Open **web.php**

- Add the route:

```php
$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});
```

Copy the example env file then Serve the application:

```sh
copy .env.example .env
php -S localhost:8000 -t public
```

Open <http://localhost:8000/key>

Open the **.env** file and copy the generated key to **APP_KEY=**

Return the **web.php** and delete the **/key** route

Open **composer.json**

- In the classmap for tests add psr-4 autoloading for Tests:

```json
"autoload-dev": {
    "classmap": [
        "tests/"
    ]
    // Add:
    ,
    "psr-4": {
        "Tests\\": "tests/"
    }
}
//.. Add to the scripts:
,
"tests": [
    "vendor\\bin\\phpunit.bat"
]
// ..
```

Open the **ExampleTest.php**

- Add the namespace Tests

```php
namespace Tests;
```

Open the **TestCase.php**

- Add the namespace Tests
- Add the use statement for TestCase as LumenTestCase
- Amend the .. extends LumenTestCase

```php
namespace Tests;

use Laravel\Lumen\Testing\TestCase as LumenTestCase;

abstract class TestCase extends LumenTestCase
// ...
```

Regenerate the autoloader & run the test

```sh
composer dumpautoload
composer tests
```

The ExampleTest should pass.

## Create the ProductControllerTest

Duplicate **ExampleTest.php** and call it tests\Feature\Http\Controllers\Api\\**ProductControllerTest.php**

- Add a namespace
- Change the comment block add @test
- Change the method name to remove test.

Now:

```php
class ProductControllerTest extends TestCase
{
    /**
     * @test
     */
    public function example()
    {
        $this->assertTrue(true);
    }
}

```

This make it easier to read. The method should be called the description of the test

- canCreateAProduct

  - It will test if a user can create a product

To run PHPUnit with the version of PHPUnit installed by Lumen:

- On linux / mac:

```sh
./vendor/bin/PHPunit /tests/Feature/Http/Controllers/Api/ProductControllerTest.php
```

- On windows:

```sh
.\vendor\bin\PHPunit tests\Feature\Http\Controllers\Api\ProductControllerTest.php
```

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 1.05 seconds, Memory: 8.00 MB

OK (1 test, 1 assertion)
```

Note: This is calling the PHPunit 7.5.9 installed by Laravel, not the PHPunit 8.0.6 global version.

This has confirmed the test can be run. Note: interestingly the **recommendation** I have read is to always start with a failing test e.g. `$this->assertTrue(false);`.

The tutor recommends the following 3-steps be followed most of the time:

- **Given** describes the preconditions for a test to work
- **When** is the action we want to take
- **Then** describes the outcome of that actions here according to the preconditions.

```php
// Given
  // user is authenticated
// When
  // post request create product
// Then
  // product exists
```

The plan is to create the authentication later, so we can start with **When**

- \$this->json('POST', '/api/products');
- will return a response
- data will need to be passed to the post request, as an array
  - []

```php
<?php

namespace Tests\Feature\Htp\Controllers\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    /**
     * @test
     */
    public function can_create_a_product()
    {
        // Given
            // user is authenticated
        // When
            // post request create product
            $response = $this->json('POST', '/api/products', []);
        // Then
            // product exists
    }
}
```

```sh
PHPunit tests\Feature\Http\Controllers\Api\ProductControllerTest
```

```text
PHPUnit 8.0.6 by Sebastian Bergmann and contributors.

R                                                                   1 / 1 (100%)

Time: 1.27 seconds, Memory: 22.00 MB

There was 1 risky test:

1) Tests\Feature\Http\Controllers\Api\ProductControllerTest::can_create_a_product
This test did not perform any assertions

C:\laragon\www\YouTube\APIs-in-Laravel-Using-TDD\APIs-in-Laravel-Using-TDD\tests\Feature\Http\Controllers\Api\ProductControllerTest.php:14

OK, but incomplete, skipped, or risky tests!
Tests: 1, Assertions: 0, Risky: 1.
```

The result is a yellow, risky test, the test can be marked as either incomplete or skipped like so:

```php
// Mark incomplete:
$this->markTestIncomplete('Your message here')
// Mark skipped:
$this->markTestSkipped('Your message here')

```

Next we can confirm the test for a return code, a successful return code is 201.

```php
public function canCreateAProduct()
{
    // Given
        // user is authenticated
    // When
        // post request create product
        $response = $this->json('POST', '/api/products', []);
    // Then
        // product exists
        $response->assertStatus(201);
}
```

Run the test, and it fails: Expected status code **201** but received **404**.

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 359 ms, Memory: 14.00 MB

There was 1 failure:

1) Tests\Feature\Http\Controllers\Api\ProductControllerTest::canCreateAProduct
Failed asserting that 404 matches expected 201.

C:\laragon\www\lumen-api\tests\Feature\Http\Controllers\Api\ProductControllerTest.php:25

FAILURES!
Tests: 1, Assertions: 1, Failures: 1.
```

As we know why this test is failing, we can make the model with migration and controller for the Product.

- The Product and ProductController will need to be copied from existing examples, the migration can be created using the artisan command:

```sh
php artisan make:migration CreateProductsTable
```

```text
Created Migration: 2019_12_06_195017_create_products_table
```

This will create the following files:

- database\migrations\\**2019_12_06_195017_create_products_table**

Copy:

- from app\\**User.php** to app\\**Product.php**
  - Rename and class **Product**
  - Remove the authentacable and tratests etc.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];
}
```

Next copy the ExampleController

- from app\Http\Controllers\\**ExampleController.php** to app\\**Http\Controllers\ProductController.php**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Store a post request to the Products table
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //
    }
}
```

Open the **web.php** router

- Create a router group for api
- Nest a router POST route for products

```php
$router->group(['prefix' => 'api'], function ($router) {
    $router->post('products', [
    'as' => 'products', 'uses' => 'ProductController@store'
    ]);
});
```

Re-run the test

```text
...
Failed asserting that 500 matches expected 201.
...
```

Open the **ProductController.php**

It now returns 500 (Internal Server Error) as we do not have the store function.

Add a store method:

```php
// ...
    /**
     * Store a post request to the Products table
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //
    }
// ...
```

Re-run the test:

```text
...
Expected status code 201 but received 200.
...
```

Basically 200 is an empty page.

To prove the test can pass, in the **ProductController.php**:

```php
// ...
public function store(Request $request): JsonResponse
{
    return response()->json([], 201);
}
// ...
```

The test now passes:

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 282 ms, Memory: 14.00 MB

OK (1 test, 1 assertion)
```

This is a forced demonstration of how to pass the test.
