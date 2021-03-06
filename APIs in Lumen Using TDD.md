# APIs in Lumen Using TDD
<!-- cSpell: ignore classmap autoloading phpunit dumpautoload PHPunit Bergmann laragon testsuites testsuite wisoky Animi veritatis dignissimos --->
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
./vendor/bin/phpunit /tests/Feature/Http/Controllers/Api/ProductControllerTest.php
```

- On windows:

```sh
.\vendor\bin\PHPUnit tests\Feature\Http\Controllers\Api\ProductControllerTest.php
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

- \$this->json('POST', '/api/product');
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
            $response = $this->json('POST', '/api/product', []);
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
        $response = $this->json('POST', '/api/product', []);
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
  - Remove the the traits

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

### Adding the database

For testing purposes configure phpunit.xml to use a sqlite database in memory.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit backupGlobals="false"
         backupStaticAttributes="false"
         bootstrap="bootstrap/app.php"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="false"
         stopOnFailure="false">
    <testsuites>
        <testsuite name="Application Test Suite">
            <directory suffix="Test.php">./tests</directory>
        </testsuite>
    </testsuites>
    <filter>
        <whitelist processUncoveredFilesFromWhitelist="true">
            <directory suffix=".php">./app</directory>
        </whitelist>
    </filter>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="DB_CONNECTION" value="sqlite"/> <!-- Add -->
        <env name="DB_DATABASE" value=":memory:"/> <!-- Add -->
    </php>
</phpunit>
```

Next updated the test to confirm the data is being created in the database

- Use faker to generate some data

```php
public function canCreateAProduct(): void
{
    $faker = Factory::create();

    // When
        // post request create product
    $response = $this->call('POST', '/api/product', [
        'name'  => $name = $faker->company,
        'price' => $price = random_int(10, 100),
    ]);

    // Then
        // The return response code is 'Created' (201)
    $this->assertEquals(201, $response->status());

        // Then confirm the database has the record
    $this->seeInDatabase('products', [
        'name'  => $name,
        'slug'  => $slug,
        'price' => $price,
        ]);
}
```

Run the test and it fails, as expected

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 361 ms, Memory: 16.00 MB

There was 1 failure:

1) Tests\Feature\Http\Controllers\ProductControllerTest::canCreateAProduct
Unable to find row in database table [products] that matched attributes [{"name":"Jacobs-Wisoky","slug":"jacobs-wisoky","price":46}].
Failed asserting that 0 is greater than 0.

C:\laragon\www\lumen-api\vendor\laravel\lumen-framework\src\Testing\TestCase.php:143
C:\laragon\www\lumen-api\tests\Feature\Http\Controllers\ProductControllerTest.php:39

FAILURES!
Tests: 1, Assertions: 2, Failures: 1.
```

To get this test to pass:

- The products table (migration) needs to have the fields
- The controller needs to receive the request and add it to the database

Open the migration **...create_products_table.php**

Add the fields image, name, slug and price to the table:

```php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->bigIncrements('id');
        // Add these fields:
        $table->integer('image')->unsigned()->nullable();
        $table->string('name', 64);
        $table->string('slug', 64)->unique();
        $table->integer('price')->unsigned();
        $table->timestamps();
    });
}
```

Open the **ProductController.php**:

- Add Request \$request as parameters to the store method
- Create a Product using the request data.
- Import the model: Product class
- Import the slug helper Str class

```php
use App\Product;
// ...
use Illuminate\Support\Str;
// ...
/**
    * Store a post request to the Products table
    *
    * @param Request $request
    * @return JsonResponse
    */
public function store(Request $request): JsonResponse
{
    $product = Product::create([
        'name'  => $request->name,
        'slug'  => Str::slug($request->name),
        'price' => $request->price
    ]);

    return response()->json(['created' => true], 201);
}
```

Run the test and it still fails:

```text
...
Failed asserting that 500 matches expected 201.
...
```

Open the latest logs file in storage\logs\lumen-2019-12-07.log

```text
....
[previous exception] [object] (PDOException(code: 23000): SQLSTATE[23000]: Integrity constraint violation: 19 NOT NULL constraint failed: products.slug at C:\\laragon\\www\\lumen-api\\vendor\\illuminate\\database\\Connection.php:459)
[stacktrace]
....
```

The error is ambiguous, it is actually a mass assignment error, open the mode **Product.php**

- Add the fillable properties.

```php
class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image', 'name', 'slug', 'price',
    ];
}
```

Re-run the test and it passes

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 293 ms, Memory: 16.00 MB

OK (1 test, 2 assertions)
```

Update the **ProductControllerTest.php** to confirm the response content

```php
// ...
$content = json_decode($response->content(), true);

$this->assertArrayHasKey('id', $content);
$this->assertArrayHasKey('name', $content);
$this->assertArrayHasKey('slug', $content);
$this->assertArrayHasKey('price', $content);
$this->assertArrayHasKey('created_at', $content);

$this->assertSame($content['name'], $name);
$this->assertSame($content['slug'], $slug);
$this->assertSame($content['price'], $price);
// ...
```

The test now fails.

```text
...
Failed asserting that an array has the key 'id'.
...
```

Update the **ProductController.php** to return the `$product` in the response

```php
// ...
return response()->json($product, 201);
// ...
```

Re-run the test and it passes

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 295 ms, Memory: 16.00 MB

OK (1 test, 10 assertions)
```

### Resources for Product

> API resources is a transformation layer that transforms your eloquent models and the JSON responses that are return to the end user.

The `artisan make:resource` command isn't available in Lumen, so copy an existing resource from another project (or copy Illuminate\Http\Resources\Json\JsonResource removing all methods except the toArray). Create the resource app\\Http\\Resources\\**ProductResource.php**

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'price'      => (int)$this->price,
            'created_at' => (string)$this->created_at,
        ];
    }
}
```

This will override the toArray method in the JsonResource class.

Next the store action can use the resource. Open the **ProductController.php**

- Update the return response to return `new ProductResource($product)`:

```php
// Was:
// return response()->json($product, 201);

// Now:
return response()->json(new ProductResource($product), 201);
```

Now only the required fields are returned, instead of all the fields (including updated_at)

For confirmation add the following to the test:

```php
\Log::info(1, [$content]);
```

Then open the log file \lumen-api\storage\logs\\**lumen-2019-12-08.log**

```text
[2019-12-08 11:33:31] testing.INFO: 1 {"id":1,"name":"Williamson-Ryan","slug":"williamson-ryan","price":95,"created_at":"2019-12-08 11:33:31"}
```

Remove the log line from the test.

## Use a Product Model Factory

Instead of generating Product data, in each test, a factory can be used to generate this data. However the `artisan make:factory` command isn't available in Lumen, instead use the **ModelFactory.php** built in, this has all the factories defined in one file. There is an example User factory already defined, which can be copied and modified, as required.

```php
<?php

// Add this use for Str
use Illuminate\Support\Str;


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

// Existing user factory:
$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

// Add a new Product factory:
$factory->define(App\Product::class, function (Faker\Generator $faker) {

    $name = $faker->sentence(3);

    return [
        'name'  => $name,
        'slug'  => Str::slug($name),
        'price' => random_int(10, 100),
    ];
});
```

Create a test to get a product

```php
/** @test */
public function canReturnAProduct()
{
// Given
    $product = factory('App\Product')->create();
// When
    $this->json('GET', "api/product/$product->id");
// Then
    $this->assertResponseOk();
// Then
    $this->seeInDatabase('products', [
        'name'  => $product->name,
        'slug'  => $product->slug,
        'price' => $product->price,
    ]);
}
```

Run the test and it fails, as expected.

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 452 ms, Memory: 16.00 MB

There was 1 failure:

1) Tests\Feature\Http\Controllers\ProductControllerTest::canReturnAProduct
Expected status code 200, got 404.
Failed asserting that false is true.
```

- There is no get route for product/id
- There is no controller to return the data

Open **web.php**

- Refactor the post/store method
- Add the get method for product/{id}

```php
$router->group(['prefix' => 'api'], function ($router) {
    $router->post('product', 'ProductController@store');
    $router->get('product/{id}', 'ProductController@show');
});
```

Open **ProductController.php**

- Add the show method

```php
public function show(int $id)
{
    // Code
}
```

The test now passes

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 322 ms, Memory: 16.00 MB

OK (1 test, 2 assertions)
```

Now expand the test to confirm the return data is actually correct.

Open the **ProductControllerTest.php**

- Add to the test

```php
/** @test */
public function canReturnAProduct()
{
    // Given one product is created and added to the database
    $product = factory('App\Product')->create();

    // When the endpoint for product is reached
    $this->json('GET', "api/product/$product->id");

    // Then the response is 200
    $this->assertResponseOk();

    // Then the database has the data
    $this->seeInDatabase('products', [
        'name'  => $product->name,
        'slug'  => $product->slug,
        'price' => $product->price,
    ]);

    // Then the response has the original data
    $this->seeJsonContains([
        "name"  => $product->name,
        "slug"  => $product->slug,
        "price" => $product->price
    ]);
}
```

The test now fails.

Open the **ProductController.php**

- Find the product, based on the supplied id
- Return it the using the same ProductResource as the store method

```php
public function show(int $id): JsonResponse
{
    $product = Product::findOrFail($id);

    return response()->json(new ProductResource($product));
}
```

Run the tests and they now pass

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 293 ms, Memory: 16.00 MB

OK (1 test, 5 assertions)
```

Refactor the **canCreateAProduct** method

- Use the Product factory to create a product, with the make method.
- Instead of using call POST, use json method, this has useful helper methods to verify the data
- Change the method to check the status code to `$this->assertResponseStatus(201);`
- Use the `seeJsonContains` method to confirm the response data

```php
/** @test  */
public function canCreateAProduct(): void
{
    $product = factory('App\Product')->make();

    // When
        // post request create product
    $this->json('POST', '/api/product', [
        'name'  => $name = $product->name,
        'price' => $price = $product->price,
    ]);
    $slug = Str::slug($name);

    // Then
        // The return response code is 'Created' (201)
    $this->assertResponseStatus(201);

    // Confirm the data returned is the same
    $this->seeJsonContains([
        "name"  => $product->name,
        "slug"  => $product->slug,
        "price" => $product->price
    ]);

    // And the database has the record
    $this->seeInDatabase('products', [
        'name'  => $name,
        'slug'  => $slug,
        'price' => $price,
    ]);
}
```

Run all the tests and they pass

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 279 ms, Memory: 18.00 MB

OK (2 tests, 10 assertions)
```

## Testing 404 on Show

```php
/** @test */
public function willFailWithA404IfProductIsNotFound()
{
    // Given
        // Product -1 does not exist.
    // When
        $response = $this->json('GET', 'api/products/-1');
    // Then
        $response->assertStatus(404);
}
```

Run a test and it passes.

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 281 ms, Memory: 14.00 MB

OK (1 test, 1 assertion)
```

Run all tests and they all pass.

## Update a product

Next focus on the update route and the tests required.

In the **ProductControllerTest.php**:

- Create a method for will fail with a 404 if product we want to update is not found.
- Test is similar to the show test.
- Change to PUT.

```php
/**
 * @test
 */
public function willFailWithA404IfAProductWeWantToUpdateIsNotFound()
{
    // Given
    // When
    $response = $this->json('PUT', 'api/products/-1');
    // Then
    $response->assertStatus(404);
}
```

This actually passes!

Now create a test for can update a product in **ProductControllerTest.php**

```php
/** @test */
public function canUpdateAProduct()
{
    // Given
    $product = factory('App\Product')->create();

    // When
    $newProduct = [
        "name"  => $product->name . '_updated',
        "slug"  => Str::slug($product->name . '_updated'),
        "price" => $product->price + 10,
    ];

    $this->json('PUT', 'api/product/' . $product->id, $newProduct);

    // Then
    $this->assertResponseOk();
}
```

the test fails with 404:

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 288 ms, Memory: 16.00 MB

There was 1 failure:

1) Tests\Feature\Http\Controllers\ProductControllerTest::canUpdateAProduct
Expected status code 200, got 404.
Failed asserting that false is true.
```

Let see if we can get the test to pass.

First open **web.php**

- Add the `put` route

```php
$router->group(['prefix' => 'api'], function ($router) {
    $router->post('product', 'ProductController@store');
    $router->get('product/{id:[0-9]+}', 'ProductController@show');
    $router->put('product/{id:[0-9]+}', 'ProductController@update'); // Added
});
```

The test still fails.

Open the **ProductController.php**:

- Add an update method.

```php
/**
 * Update a product already in the Product DB
 *
 * @param Request $request, product Id $id
 * @return JsonResponse
 */
public function update(Request $request, int $id)
{
    $product = Product::findOrFail($id);
}
```

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 300 ms, Memory: 18.00 MB

OK (1 test, 1 assertion)

Terminal will be reused by tasks, press any key to close it.
```

No the test pass, it can eb extended

Open **ProductControllerTest.php**

In the test add another assert to confirm the database has been updated:

```php
// Then
$this->assertResponseOk();

// Then
$this->seeInDatabase('products', $newProduct);

// Then
$this->seeJsonContains($newProduct);
```

The test now fails:

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 306 ms, Memory: 18.00 MB

There was 1 failure:

1) Tests\Feature\Http\Controllers\ProductControllerTest::canUpdateAProduct
Unable to find row in database table [products] that matched attributes [{"name":"Animi veritatis dignissimos._updated","slug":"animi-veritatis-dignissimos_updated","price":102}].
Failed asserting that 0 is greater than 0.
```

The data needs to be persisted to the database

```php
/**
 * Update a product already in the Product DB
 *
 * @param Request $request
 * @param integer $id the ProductId to be updated
 * @return JsonResponse
 */
public function update(Request $request, int $id): JsonResponse
{
    $product = Product::findOrFail($id);

    $product->update([
        'name'  => $request->name,
        'slug'  => Str::slug($request->name),
        'price' => $request->price,
    ]);


    return response()->json(new ProductResource($product));
}
```

The test now passes

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 296 ms, Memory: 18.00 MB

OK (1 test, 5 assertions)
```

Now run all the tests adn they all pass :)

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.....                                                               5 / 5 (100%)

Time: 401 ms, Memory: 18.00 MB

OK (5 tests, 17 assertions)
```

## Delete

Start with a product which doesn't exist, open **ProductControllerTest.php**

```php
/** @test */
public function willFailWithA404IfProductWeWantToDeleteIsNotFound()
{
    // Given
    // When
    $response = $this->json('DELETE', 'api/product/-1');
    // Then
    $response->assertStatus(404);
}
```

Again, with Lumen, this actually passes!

Now create a test for can delete a product in **ProductControllerTest.php**

```php
/** @test */
public function canDeleteAProduct()
{
    // Given
    $product = factory('App\Product')->create();
    // When
    $this->json('DELETE', 'api/products/' . $product->id);
    // Then
    $this->assertResponseStatus(204);
}
```

This test fails

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 911 ms, Memory: 18.00 MB

There was 1 failure:

1) Tests\Feature\Http\Controllers\ProductControllerTest::canDeleteAProduct
Expected status code 204, got 404.
Failed asserting that 404 matches expected 204.

C:\laragon\www\lumen-api\vendor\laravel\lumen-framework\src\Testing\Concerns\MakesHttpRequests.php:457
C:\laragon\www\lumen-api\tests\Feature\Http\Controllers\ProductControllerTest.php:149

FAILURES!
Tests: 1, Assertions: 1, Failures: 1.
```

This is known, there is no delete route or destroy method.

Open **web.php**

- Create a delete route

```php
$router->group(['prefix' => 'api'], function ($router) {
    $router->post('product', 'ProductController@store');
    $router->get('product/{id:[0-9]+}', 'ProductController@show');
    $router->put('product/{id:[0-9]+}', 'ProductController@update');
    $router->delete('product/{id}', 'ProductController@destroy'); // Added
});
```

The test still fails.

Open **ProductController.php**

- Add a destroy method

```php
public function destroy(int $id)
{
    return response()->json(null, 204);
}
```

Now the canDeleteAProduct test passes, but the willFailWithA404IfProductWeWantToDeleteIsNotFound test fails!

Refactor the destroy method to delete the requested record.

```php
public function destroy(int $id)
{
    $product = Product::findOrFail($id);

    $product->delete();

    return response()->json([], 204);
}
```

The test now passes.

Add another check to confirm the data has been deleted from the database

```php
/** @test */
public function canDeleteAProduct()
{
    // Given
    $product = factory('App\Product')->create();
    // When
    $this->json('DELETE', 'api/product/' . $product->id);
    // Then
    $this->assertResponseStatus(204);

    $this->notSeeInDatabase('products', ['id'  => $product->id,]);
}
```

The test still passes

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 470 ms, Memory: 18.00 MB

OK (1 test, 2 assertions)
```

Open the **web.php**

- Add a regex on the id to only allow numbers `{id:[0-9]+}`

```php
$router->group(['prefix' => 'api'], function ($router) {
    $router->post('product', 'ProductController@store');
    $router->get('product/{id:[0-9]+}', 'ProductController@show');
    $router->put('product/{id:[0-9]+}', 'ProductController@update');
    $router->delete('product/{id:[0-9]+}', 'ProductController@destroy');
});
```

The tests now fail, due to the check on product `-1`, amend them to check for an id of 999.

```php
// in method willFailWithA404IfProductIsNotFound()
$this->json('GET', 'api/product/999');
// method willFailWithA404IfAProductWeWantToUpdateIsNotFound()
$this->json('PUT', 'api/product/999', []);

//...
/** @test */
public function willFailWithA404IfProductWeWantToDeleteIsNotFound()
{
    // Given
    // When
    $this->json('DELETE', 'api/product/999'); // Change to 999
    // Then
    $this->assertResponseStatus(404);
}

/** @test */
public function canDeleteAProduct()
{
    // Given
    $product = factory('App\Product')->create();
    // When
    $this->json('DELETE', 'api/product/' . $product->id);
    // Then
    $this->assertResponseStatus(204);

    $this->notSeeInDatabase('products', ['id'  => $product->id,]);
}
// ...
```

## Index

Index should be the first test in the Test class.

Open **ProductControllerTest.php**

- Start with a test can return a collection fo paginated products
- As this will be multiple products, three products will be created.
- The data is returned as a collection with an array, the JSON structure is therefore different.

  - data =>

    - \* => signifies an array

      - then the data structure.

```php
/** @test */
public function canReturnACollectionOfPaginatedProducts()
{
    $product1 = factory('App\Product')->create();
    $product2 = factory('App\Product')->create();
    $product3 = factory('App\Product')->create();

    $this->json('GET', '/api/product');

    $this->assertResponseOk();
}
```

Run the test and it fails with code 404.

Open **web.php**

- Add the get route for product to the index method.

```php
$router->group(['prefix' => 'api'], function ($router) {
    $router->post('product', 'ProductController@store');
    $router->get('product/{id:[0-9]+}', 'ProductController@show');
    $router->put('product/{id:[0-9]+}', 'ProductController@update');
    $router->delete('product/{id:[0-9]+}', 'ProductController@destroy');
    $router->get('product', 'ProductController@index'); // Added
});
```

Test still fails. Open **ProductController.php**

- Add the index method

```php
/**
 * List of all products
 *
 * @return JsonResponse
 */
public function index()
{
    $product = Product::all();

    return response()->json($product);
}
```

The test now passes

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 296 ms, Memory: 18.00 MB

OK (1 test, 1 assertion)
```

Amend the test to check the database

- Has a record with the same details
- Has 3 records

```php
$this->seeInDatabase('products', [
    "name"  => $product1->name,
    "slug"  => $product1->slug,
    "price" => $product1->price,
]);

$this->assertSame(3, Product::all()->count());
```

Run the tests and they still pass.

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 373 ms, Memory: 18.00 MB

OK (1 test, 3 assertions)
```

Expand the test further and it now fails

```php
// Then
$this->seeJsonStructure([
    'data' => [
        '*' => [
            'id',
            'name',
            'slug',
            'price',
            'created_at',
            'updated_at',
        ],
    ],
]);

$this->seeJsonEquals(
    [
    "data" => [
        [
            "created_at" => (string)$product1->created_at,
            "id" => $product1->id,
            "image" => null,
            "name" => $product1->name,
            "price" => (string) $product1->price,
            "slug" => $product1->slug,
            "updated_at" => (string) $product1->updated_at
        ],
        [
            "created_at" => (string)$product2->created_at,
            "id" => $product2->id,
            "image" => null,
            "name" => $product2->name,
            "price" => (string) $product2->price,
            "slug" => $product2->slug,
            "updated_at" => (string) $product2->updated_at
        ],
        [
            "created_at" => (string)$product3->created_at,
            "id" => $product3->id,
            "image" => null,
            "name" => $product3->name,
            "price" => (string) $product3->price,
            "slug" => $product3->slug,
            "updated_at" => (string) $product3->updated_at
            ]
        ],
        "links" => [
            "first" => "http://localhost/api/product?page=1",
            "last" => "http://localhost/api/product?page=1",
            "next" => null,
            "prev" => null
        ],
        "meta" => [
            "current_page" => 1,
            "from" => 1,
            "last_page" => 1,
            "path" => "http://localhost/api/product",
            "per_page" => 15,
            "to" => 3,
            "total" => 3
        ]
    ]
);
```

To fix this test create a Collection Resource called **ProductCollection.php**

- As with ProductResource the make:resource command isn't available, so copy from another Laravel project.

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
```

Open the **ProductController.php**

- Add `use ProductCollection`
- Change the index method to return the new ProductCollection with pagination

```php
// ...
use App\Http\Resources\ProductCollection;
// ...
public function index()
{
    return new ProductCollection(Product::paginate());
}
```

Run the tests and they now pass

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 290 ms, Memory: 18.00 MB

OK (1 test, 24 assertions)
```

Run all tests and they all pass

```text
PHPUnit 8.4.3 by Sebastian Bergmann and contributors.

........                                                            8 / 8 (100%)

Time: 582 ms, Memory: 18.00 MB

OK (8 tests, 44 assertions)
```
