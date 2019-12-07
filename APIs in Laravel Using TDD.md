# APIs in Laravel Using TDD

- [APIs in Laravel Using TDD](https://www.youtube.com/playlist?list=PL3ZhWMazGi9KGG64X_HJlZ_sQuvyFGoMo)
- [Github - Laravel API using API resources and TDD.](https://github.com/devlob/apis-in-laravel-using-tdd)

Notes: code snippets are often used to highlight the code changed, any code prior or post the code snipped is generally unchanged from previous notes, or to highlight only the output of interest. To signify a snippet of a larger code block, dots are normally used e.g.

```php
\\ ...
echo "Hello";
\\ ...
```

For setup see readme.md

## Lesson 1 2:04 APIs in Laravel Using TDD - Preview

Introduction.

## Lesson 2 9:36 APIs in Laravel Using TDD - Store Part 1

```sh
laravel new APIs-in-Laravel-Using-TDD
# Once installed
php artisan make:test Htp/Controllers/Api/ProductControllerTest
#php artisan make:test Http/Controllers/Api/ProductControllerTest
```

Open the new test tests\Feature\Htp\Controllers\Api\\**ProductControllerTest.php**

- Change the comment block add @test
- Change the method name to remove test.

- Was:

```php
class ProductControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
```

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

- can_create_a_product

  - It will test whether a user can create a product

To run PHPunit with the version of PHPunit installed by Laravel:

- On linux / mac:

```sh
./vendor/bin/PHPunit /tests/Feature/Http/Controllers/Api/ProductControllerTest.php
```

- On windows:

```sh
.\vendor\bin\PHPunit tests\Feature\Http\Controllers\Api\ProductControllerTest.php
```

```text
PHPunit 7.5.9 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 1.07 seconds, Memory: 16.00 MB

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
public function canCreateaProduct()
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
PHPUnit 7.5.9 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 1.21 seconds, Memory: 16.00 MB

There was 1 failure:

1) Tests\Feature\Http\Controllers\Api\ProductControllerTest::canCreateaProduct
Expected status code 201 but received 404.
Failed asserting that false is true.

C:\laragon\www\YouTube\APIs-in-Laravel-Using-TDD\APIs-in-Laravel-Using-TDD\vendor\laravel\framework\src\Illuminate\Foundation\Testing\TestResponse.php:133
C:\laragon\www\YouTube\APIs-in-Laravel-Using-TDD\APIs-in-Laravel-Using-TDD\tests\Feature\Http\Controllers\Api\ProductControllerTest.php:25

FAILURES!
Tests: 1, Assertions: 1, Failures: 1.
The terminal process terminated with exit code: 1
```

- When we make a post request then the repose is expected to be 201.

As we know why this test is failing, we can make the model with migration and controller for the Product:

```sh
php artisan make:model Product -mc
```

```text
Model created successfully.
Created Migration: 2019_04_23_155836_create_products_table
Controller created successfully.
```

This will create the following files:

- app\\**Product.php**
- app\\**Http\Controllers\ProductController.php**
- database\migrations\\**2019_04_23_155836_create_products_table.php**

Open the **api.php** router

- Create a POST route for product, at the end of the file:

```php
Route::namespace('Api')->group(function () {
    Route::post('/products', 'ProductController@store');
});
```

Move the ProductController.php:

- Create a sub folder called Api and move the ProductController.php into the new folder.
  - app\Http\Controllers\Api\ProductController.php

Open the **ProductController.php**

- Add \Api to the namespace
- Import the Controller

```php
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    //
}
```

Re-run the test

```text
...
Expected status code 201 but received 500.
...
```

It now returns 500 (Internal Server Error) as we don not have the store function.

Add a store method:

```php
// ...
class ProductController extends Controller
{
    public function store()
    {
        // code
    }
}
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
public function store()
{
    return response()->json([], 201);
}
// ...
```

The test now passes:

```text
PHPUnit 7.5.9 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 1.19 seconds, Memory: 16.00 MB

OK (1 test, 1 assertion)
```

This is a forced demonstration of how to pass the test.

## Lesson 3 10:01 APIs in Laravel Using TDD - Store Part 2

Open the products table migration and update the up method

- add image, name, slug and price:

```php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->integer('image')->unsigned()->nullable();
        $table->string('name', 64);
        $table->string('slug', 64)->unique();
        $table->integer('price')->unsigned();
        $table->timestamps();
    });
}
```

Run the migrations (remember to setup the database before running this):

```sh
php artisan migrate
```

```text
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table
Migrating: 2014_10_12_100000_create_password_resets_table
Migrated:  2014_10_12_100000_create_password_resets_table
Migrating: 2019_04_23_155836_create_products_table
Migrated:  2019_04_23_155836_create_products_table
```

Back in **ProductControllerTest.php**

- Use faker to create some product data.
- Remember to import Factory class.
- Use faker to generate data.
- Assert the data has been saved to the database.

```php
<?php

namespace Tests\Feature\Http\Controllers\Api;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function canCreateaProduct()
    {
        $faker = Factory::create();

        // Given
            // user is authenticated
        // When
            // post request create product
            $response = $this->json('POST', '/api/products', [
                // use faker data, note the use of $name = and $price =
                // these variables can be used later
                'name'  => $name = $faker->company,
                'slug'  => str_slug($name),
                'price' => $price = random_int(10, 100),
            ]);
        // Then
            // product exists
            $response->assertStatus(201);

            // add an assert for the data has been added to the database:
            $this->assertDatabaseHas('products', [
                // Note: the $name and $price variables from the faker factory:
                'name'  => $name,
                'slug'  => str_slug($name),
                'price' => $price,
            ])
    }
}
```

Run the test:

```text
PHPUnit 7.5.9 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 1.77 seconds, Memory: 20.00 MB

There was 1 failure:

1) Tests\Feature\Http\Controllers\Api\ProductControllerTest::canCreateaProduct
Failed asserting that a row in the table [products] matches the attributes {
    "name": "Hilpert Ltd",
    "slug": "hilpert-ltd",
    "price": 26
}.

The table is empty.

C:\laragon\www\YouTube\APIs-in-Laravel-Using-TDD\APIs-in-Laravel-Using-TDD\vendor\laravel\framework\src\Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase.php:24
C:\laragon\www\YouTube\APIs-in-Laravel-Using-TDD\APIs-in-Laravel-Using-TDD\tests\Feature\Http\Controllers\Api\ProductControllerTest.php:37

FAILURES!
Tests: 1, Assertions: 2, Failures: 1.
The terminal process terminated with exit code: 1
```

As expected the test fails, but only 1 failure, it hasn't been added to the database. The 201 response code is a pass!

Open the **ProductController.php**:

- Add Request \$request as parameters to the store method
- Create a Product using the request data.
- Import the Product class

Running test now fails with a 500 error. This is unexpected :( the reason is the model must have either a protected or guarded array. Open **Product.php**

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];
}
```

We now have 1 green test (OK line displays in green when cmder is used)

```text
PHPUnit 7.5.9 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 1.12 seconds, Memory: 20.00 MB

OK (1 test, 2 assertions)
```

If the database is checked there is one record stored. Now the tests pass it is important to refactor the code, each refactor triggers a test.

First refactor is to return the product, created when the record is created, back in the response (instead of an empty array):

- Add \$product = Product::create...
- Return the \$product in the response.

```php
class ProductController extends Controller
{
    public function store(Request $request)
    {
        $product = Product::create([
            'name'  => $request->name,
            'slug'  => str_slug($request->name),
            'price' => $request->price
        ]);

        return response()->json($product, 201);
    }
}
```

Run the test, result OK.

Back in the **ProductControllerTest.php**

- Update the response:
  - assert the Json structure returned is correct.
  - assert the Json data returned matches the input data.

```php
// ...
// Then
    // product exists
    $response->assertJsonStructure([
        'id',
        'name',
        'slug',
        'price',
        'created_at',
    ])->assertJson([
        'name' => $name,
        'slug' => str_slug($name),
        'price' => $price,
    ])->assertStatus(201);
// ...
```

Note id and created_at are not checked as the input data can not be used to confirm the exact output data. The id could be guessed by taking the last available id and incrementing it, but this isn't possible when the id is, for example, a MongoDB id.

```text
...
OK (1 test, 8 assertions)
```

## Lesson 4 7:18 APIs in Laravel Using TDD - Store Switching to API Resources

> API resources is a transformation layer that transforms your eloquent models and the JSON responses that are return to the end user.

Create a new resource class for Product:

```sh
php artisan make:resource Product
```

Info: to log the data that is returned, in the tests\Feature\Http\Controllers\Api\\**ProductControllerTest.php** add the following line:

```php
// ...
// Then
\Log::info(1, [$response->getContent()]);
    // product exists
    // ...
```

- The log files will be created in storage\logs\*\*laravel-2019-04-24.log\*\*

To only return the data which is required, (excluding the updated_at field) a resource with toArray and return only the fields required to be returned.

- Note: date fields are returned as objects, to convert them to a string use (string)
- Without (string): `["{...,\"created_at\":\"2019-04-24T10:43:09.000000Z\"}"]`
- With (string): `["{...,\"created_at\":\"2019-04-24 10:44:49\"}"]`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'price'      => $this->price,
            'created_at' => (string)$this->created_at,
        ];
    }
}
```

Next the store action can use the resource. Open the ProductController.php:

- In the return response, the ProductResource can be used:
- The namespace needs to be imported, however Product is already used, so it needs an alias of ProductResource.

```php
use App\Http\Resources\Product as ProductResource;

// Nas: return response()->json($product, 201);
return response()->json(new ProductResource($product), 201);
```

Re-run the test and the result is green.

```text
...
OK (3 tests, 10 assertions)
```

Info: the log file now only contains the data required:

```JSON
[2019-04-24 10:31:21] testing.INFO: 1 ["{\"id\":15,\"name\":\"McLaughlin, Cummerata and Boehm\",\"slug\":\"mclaughlin-cummerata-and-boehm\",\"price\":94,\"created_at\":\"2019-04-24 10:31:21\"}"]
```

The line to log the information can now be deleted from the **ProductControllerTest.php**

```php
// Remove line:
\Log::info(1, [$response->getContent()]);
```

The ProductResource can also rename fields, e.g. created_at could be named created, in the return string.

## Lesson 5 10:41 APIs in Laravel Using TDD - Testing success and 404 on Show

This time a new test for can return a product will be created.

Create a new test method called can_return_a_product

Before the test is written, open **TestCase.php**

- Create a create method.
  - string \$model
  - array \$attributes = []
  - use a product factory passing in any data
  - return the data using the ProductResource
  - Import the ProductResource

```php
use App\Http\Resources\Product as ProductResource;
//..
public function create(string $model, array $attributes = [])
{
    $product = factory("App\\$model")->create($attributes);

    return new ProductResource($product);
}
```

Create a Product factory based on the Product model:

```sh
php artisan make:factory ProductFactory -m=Product
```

Copy the logic from **ProductControllerTest.php** canCreateaProduct method to the **ProductFactory.php**

```php
// ...
$factory->define(Product::class, function (Faker $faker) {
    $name = $faker->company;

    return [
        'name'  => $name,
        'slug'  => str_slug($name),
        'price' => random_int(10, 100),
    ];
});
```

Now write the test method for can_return_a_product:

```php
public function can_return_a_product()
{
    // Given
    $product = $this->create('Product');
    // When
    $response = $this->json('GET', "api/products/$product->id");
    // Then
    $response->assertStatus(200);
}
```

Run the test, it fails with 404 as the route and show action does not exist

```text
...
Expected status code 200 but received 404.
...
```

Open **api.php**

- add the route:
  - GET request on products/id for the show method.

```php
Route::namespace('Api')->group(function () {
    Route::post('/products', 'ProductController@store');
    // Added
    Route::get('/products/{id}', 'ProductController@show');
});
```

Run the test:

```text
There was 1 failure:
...
Expected status code 200 but received 500.
```

The error is now 500, as the method doesn't exists on the controller.

Open **ProductController.php**

- create a show method.
  - There is an \$id
  - Find the product using findOrfail
  - there is no need to return a status code, as by default it is 200.

```php
public function show(int $id)
{
    $product = Product::findOrfail($id);

    return response()->json(new ProductResource($product));
}
```

Run a test and it returns green.

```text
PHPUnit 7.5.9 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 1.32 seconds, Memory: 20.00 MB

OK (1 test, 1 assertion)
```

In **ProductControllerTest.php**

- Add assertExactJson with the expected data.

```php
// ...
public function can_return_a_product()
{
    // Given
    $product = $this->create('Product');
    // When
    $response = $this->json('GET', "api/products/$product->id");
    // Then
    $response->assertStatus(200)
    ->assertExactJson([
        'id'         => $product->id,
        'name'       => $product->name,
        'slug'       => $product->slug,
        'price'      => (string)$product->price,
        'created_at' => (string)$product->created_at,
        'updated_at' => (string)$product->updated_at,
    ]);
}
```

Run the test. Fail. as the **updated_at** field is not returned by the ProductResource.

```text
..
Failed asserting that two strings are equal.
--- Expected
+++ Actual
@@ @@
-'{"created_at":"2019-04-24T13:08:08.000000Z","id":25,"name":"Metz-Stark","price":"17","slug":"metz-stark","updated_at":"2019-04-24T13:08:08.000000Z"}'
+'{"created_at":"2019-04-24 13:08:08","id":25,"name":"Metz-Stark","price":"17","slug":"metz-stark"}'
...
```

Remove the updated_at and the test passes.

- I had to add (string) to price, I'm guessing the db is escaping the integer.

Next write a test for a product not found, which will return 404.

Tutor like to write these tests above the tests which return data.

- Between can_create_a_product and can_return_a_product insert method will_fail_with_a_404_if_product_is_not_found

  - test with an id which will never exists e.g. -1
  - assert the status is 404

```php
/**
 * @test
 */
public function will_fail_with_a_404_if_product_is_not_found()
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

## Lesson 6 3:39 APIs in Laravel Using TDD - In memory database and filtering single tests

Currently each time a test is run it is persisted to the database.

- Checking the Sqlite database there was 32 products!

To clear the database, after each test, use the RefreshDatabase trait. Open **ProductControllerTest.php** and add:

```php
// ...
class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function can_create_a_product()
// ...
```

Run the tests and check the database, it is empty after each test.

To use an im memory database add these lines to **PHPunit.xml**:

- `<server name="DB_CONNECTION" value="sqlite"/>`
- `<server name="DB_DATABASE" value=":memory:"/>`

```xml
...
<php>
    <server name="APP_ENV" value="testing"/>
    <server name="DB_CONNECTION" value="sqlite"/>
    <server name="DB_DATABASE" value=":memory:"/>
    <server name="BCRYPT_ROUNDS" value="4"/>
    <server name="CACHE_DRIVER" value="array"/>
    <server name="MAIL_DRIVER" value="array"/>
    <server name="QUEUE_CONNECTION" value="sync"/>
    <server name="SESSION_DRIVER" value="array"/>
</php>
...
```

It looks like Sqlite returns string for the price, in the Resource/Product.php

- update the price files so it returns an integer
  - `'price' => (int)$this->price,`

```php
// ...
public function toArray($request)
{
    return [
        'id'         => $this->id,
        'name'       => $this->name,
        'slug'       => $this->slug,
        'price'      => (int)$this->price,
        'created_at' => (string)$this->created_at,
    ];
}
// ...
```

In **ProductControllerTest.php**

- remove (string) from the price.
  - `'price' => (string)$product->price,`

```php
// ...
public function can_return_a_product()
{
    // Given
    $product = $this->create('Product');
    // When
    $response = $this->json('GET', "api/products/$product->id");
    // Then
    $response->assertStatus(200)
    ->assertExactJson([
        'id'         => $product->id,
        'name'       => $product->name,
        'slug'       => $product->slug,
        'price'      => $product->price,
        'created_at' => (string)$product->created_at,
    ]);
}
```

Run the tests and they now pass.

If the test name (method name) is not known if it is unique, then to run one test, from the command line use the filter option:

```sh
.\vendor\bin\PHPunit tests\Feature\Http\Controllers\Api\ProductControllerTest.php --filter can_return_a_product
```

Note: With Better PHP Unit is you are inside a test and run the test (F1 >better PHPunit: run) or better still map the command to the keyboard CTRL + T (use Preferences Open Keyboard shortcuts CTRL K CTRL S, search for Better PHPunit: run and change the keybindings)

If the test name (method name) is unique, then to run one test, from the command line use the filter option:

```sh
.\vendor\bin\PHPunit --filter can_return_a_product
```

## Lesson 7 8:32 APIs in Laravel Using TDD - Update

This lesson will focus on the update route and the tests required.

In the **ProductControllerTest.php**:

- Create a method for will fail with a 404 if product we want to update is not found.
- Test is similar to the show test.
- Change to PUT.

```php
/**
 * @test
*/
public function will_fail_with_a_404_if_product_we_want_to_update_is_not_found()
{
    // Given
    // When
    $response = $this->json('PUT', 'api/products/-1');
    // Then
    $response->assertStatus(404);
}
```

Run the test and it fails!

```text
...
Expected status code 404 but received 405.
...
```

We get a 405 because the same endpoint used in the get request (show method on the controller) receives a PUT request.

We need to create a route for a put request in the **api.php** route:

```php
Route::namespace('Api')->group(function () {
    Route::post('/products', 'ProductController@store');
    Route::get('/products/{id}', 'ProductController@show');
    Route::put('/products/{id}', 'ProductController@update'); // Added
});
```

Run the test and we get a 500, as the controller doesn't have an update method.

```text
...
Expected status code 404 but received 500.
...
```

Open the **ProductController.php**:

- Add an update method.

```php
    public function update(Request $request, int $id)
    {
        $product = Product::findOrfail($id);
    }
```

Now we get a pass:

```text
...
OK (1 test, 1 assertion)
...
```

Now create a test for can update a product in **ProductControllerTest.php**

```php
/**
 * @test
*/
public function can_update_a_product()
{
    // Given
    $product = $this->create('Product');
    // When
    $response = $this->json('PUT', 'api/products/'.$product->id, [
        'name'  => $product->name.'_updated',
        'slug'  => str_slug($product->name.'_updated'),
        'price' => $product->price +10,
    ]);
    // Then
    $response->assertStatus(200);
}
```

Run the test gives OK.

```text
OK (1 test, 1 assertion)
```

200 doesn't confirm this is working! Next confirm teh returned data matches the data put to the controller.

In the test add another assert to confirm the database has been updated:

```php
// Then
$response->assertStatus(200)
->assertExactJson([
    'id'         => $product->id,
    'name'       => $product->name.'_updated',
    'slug'       => str_slug($product->name.'_updated'),
    'price'      => $product->price +10,
    'created_at' => (string)$product->created_at,
]);
```

The test now fails:

```text
Invalid JSON was returned from the route.
```

In the ProductController update method, return the product data:

```php
// ..
return response()->json(new ProductResource($product));
```

The test now fails for different reason, the data doesn't match:

```text
Failed asserting that two strings are equal.
--- Expected
+++ Actual
@@ @@
-'{"created_at":"2019-04-24 19:26:13","id":1,"name":"Morar, Paucek and Hudson_updated","price":33,"slug":"morar-paucek-and-hudson-updated"}'
+'{"created_at":"2019-04-24 19:26:13","id":1,"name":"Morar, Paucek and Hudson","price":23,"slug":"morar-paucek-and-hudson"}'
```

This is because the data hasn't been updated in the database. Back in the **ProductController.php**, persist the updated data.

```php
public function update(Request $request, int $id)
{
    $product = Product::findOrfail($id);

    $product->update([
        'name'  => $request->name,
        'slug'  => $request->slug,
        'price' => $request->price,
    ]);

    return response()->json(new ProductResource($product));
}
```

Run the test, and it now returns green.

```text
PHPUnit 7.5.9 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 1.59 seconds, Memory: 24.00 MB

OK (1 test, 2 assertions)
```

One more test, which can be run, it to test the database directly. In the test method.

```php
public function can_update_a_product()
{
    // Given
    $product = $this->create('Product');
    // When
    $response = $this->json('PUT', 'api/products/'.$product->id, [
        'name'  => $product->name.'_updated',
        'slug'  => str_slug($product->name.'_updated'),
        'price' => $product->price +10,
    ]);
    // Then
    $response->assertStatus(200)
    ->assertExactJson([
        'id'         => $product->id,
        'name'       => $product->name.'_updated',
        'slug'       => str_slug($product->name.'_updated'),
        'price'      => $product->price +10,
        'created_at' => (string)$product->created_at,
    ]);

    $this->assertDatabaseHas('products', [
        'id'         => $product->id,
        'name'       => $product->name.'_updated',
        'slug'       => str_slug($product->name.'_updated'),
        'price'      => $product->price +10,
        'created_at' => (string)$product->created_at,
        'updated_at' => (string)$product->updated_at,
    ]);
}
```

Run the tests and again it passes.

```text
PHPUnit 7.5.9 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 1.71 seconds, Memory: 24.00 MB

OK (1 test, 3 assertions)
```

Now run all the tests adn they all pass :)

```text
PHPUnit 8.0.6 by Sebastian Bergmann and contributors.

.......                                                             7 / 7 (100%)

Time: 4.92 seconds, Memory: 32.00 MB

OK (7 tests, 17 assertions)
```

## Lesson 8 5:50 APIs in Laravel Using TDD - Delete

```php
/**
 * @test
*/
public function will_fail_with_a_404_if_product_we_want_to_delete_is_not_found()
{
    // Given
    // When
    $response = $this->json('DELETE', 'api/products/-1');
    // Then
    $response->assertStatus(404);
}
```

Test fails with a 405.

```text
Expected status code 404 but received 405.
```

Add the Route to **api.php**:

```php
Route::namespace('Api')->group(function () {
    Route::post('/products', 'ProductController@store');
    Route::get('/products/{id}', 'ProductController@show');
    Route::put('/products/{id}', 'ProductController@update');
    Route::delete('/products/{id}', 'ProductController@destroy'); // Added
});
```

As with update we get a 500, as the controller isn't found.

```text
Expected status code 404 but received 500.
```

Add the destroy method to the **ProductController.php**:

```php
public function destroy(int $id)
{
    // code
}
```

Test fails with code 200.

```text
Expected status code 404 but received 200.
```

Update the delete method with a findOrFail:

```php
    public function destroy(int $id)
    {
        $product = Product::findOrfail($id);
    }
```

Test now passes green

```text
OK (1 test, 1 assertion)
```

Write a new test to check if an actual product can be deleted:

```php
/**
 * @test
 *
 * @return void
*/
public function can_delete_a_product()
{
    // Given
    $product = $this->create('Product');
    // When
    $response = $this->json('DELETE', 'api/products/'.$product->id);
    // Then
    $response->assertStatus(204)
        ->assertSee(null);
}
```

Test passes with green

```text
OK (1 test, 1 assertion)
```

Update the destroy method.

```php
return response()->json(null, 204);
```

Test passes with green

```text
OK (1 test, 1 assertion)
```

Update the test to confirm the product has been deleted.

```php
$this->assertDatabaseMissing('products', [
    'id' => $product->id,
]);
```

Test fails, as the data is still in teh database.

Action the delete request in the destroy method.

```php
public function destroy(int $id)
{
    $product = Product::findOrfail($id);

    $product->delete();

    return response()->json(null, 204);
}
```

Test passes with green

```text
OK (1 test, 1 assertion)
```

Tests pass green:

```text
PHPUnit 7.5.9 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 1.97 seconds, Memory: 24.00 MB

OK (1 test, 2 assertions)
```

Run all tests:

```text
PHPUnit 8.0.6 by Sebastian Bergmann and contributors.

.........                                                           9 / 9 (100%)

Time: 5.72 seconds, Memory: 32.00 MB

OK (9 tests, 20 assertions)
```

## Lesson 9 9:28 APIs in Laravel Using TDD - Index

Index should be the first test in the Test class.

Open **ProductControllerTest.php**

- Start with a test can return a collection fo paginated products
- As this will be multiple products, three products will be created.
- The data is returned as a collection with an array, the JSON structure is therefore different.

  - data =>

    - \* => signifies an array

      - then the data structure.

```php
/**
 * @test
 *
 * @return void
    */
public function can_return_a_collection_of_paginated_products()
{
    $product1 = $this->create('Product');
    $product2 = $this->create('Product');
    $product3 = $this->create('Product');

    $response = $this->json('GET', '/api/products');

    $response->assertStatus(200)
    ->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
                'slug',
                'price',
                'created_at',
            ],
        ],
    ]);
}
```

Run the test and it fails with code 405.

Refactor the **api.php** route, so use a route resource for products, all verbs are used except edit.

```php
Route::namespace('Api')->group(function () {
    Route::resource('/products', 'ProductController', ['except' => 'edit']);
});
```

Run all the tests.

- they all pass except the first one (as expected).
- The first test now fails with status 500 as there is no index method on the ProductController.

Open **ProductController.php**

- create an index method.
- ProductResource can not be used, as it is for a single product, instead a resource collection.
- From the command line run create a resource called ProductCollection and make it a collection (-c)

```sh
php artisan make:resource ProductCollection -c
```

The app\Http\Resources\\**ProductCollection.php** can be left as default.

Back in **ProductController.php**

- return the ProductCollection with pagination

```php
//..
use App\Http\Resources\ProductCollection;
//..
public function index()
{
    return new ProductCollection(Product::paginate());
}
```

Run the test and it now passes.

- Add a log info the the end of the tes, run it and open the log file to see what is returned:

```php
\Log::info($response->getContent());
```

```text
[2019-04-25 09:02:12] testing.INFO:
{
  "data": [
    {
      "id": 1,
      "name": "Kling, Schmitt and Braun",
      "slug": "kling-schmitt-and-braun",
      "price": 39,
      "created_at": "2019-04-25 09:02:12"
    },
    {
      "id": 2,
      "name": "Wehner-Welch",
      "slug": "wehner-welch",
      "price": 69,
      "created_at": "2019-04-25 09:02:12"
    },
    {
      "id": 3,
      "name": "Little-Watsica",
      "slug": "little-watsica",
      "price": 63,
      "created_at": "2019-04-25 09:02:12"
    }
  ],
  "links": {
    "first": "http://localhost/api/products?page=1",
    "last": "http://localhost/api/products?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://localhost/api/products",
    "per_page": 15,
    "to": 3,
    "total": 3
  }
}
```

Update the test with each link:

```php
public function can_return_a_collection_of_paginated_products()
{
    // Given a collection of products
    $product1 = $this->create('Product');
    $product2 = $this->create('Product');
    $product3 = $this->create('Product');
    // when the endpoint is reached
    $response = $this->json('GET', '/api/products');
    // Then the response should be OK and the data should be returned
    $response->assertStatus(200)
    ->assertJsonStructure([
        'data'  => [
            '*' => [
                'id',
                'name',
                'slug',
                'price',
                'created_at',
            ],
        ],
        'links' => [
            'first',
            'last',
            'prev',
            'next',
        ],
        'meta'  => [
            'current_page',
            'from',
            'last_page',
            'path',
            'per_page',
            'to',
            'total',
        ],
    ]);
    // \Log::info($response->getContent());
}
```

Re-run the test and it passes, but note there are now 31 assertions!

```text
...
OK (1 test, 31 assertions)
...
```

Re-run all the tests and they pass.

```text
PHPUnit 8.0.6 by Sebastian Bergmann and contributors.

..........                                                        10 / 10 (100%)

Time: 7.39 seconds, Memory: 32.00 MB

OK (10 tests, 52 assertions)
```

## Lesson 10 9:55 APIs in Laravel Using TDD - Protecting the API

The API should be protected from unauthorised users.

Open **Api.php**:

- Copy `middleware('auth:api')->` from the built in route and insert it before namespace in the Api route:
- Also add `create` the the exceptions list.

```php
Route::middleware('auth:api')->namespace('Api')->group(function () {
    Route::resource('/products', 'ProductController', ['except' => ['edit', 'create']]);
});
```

Tests now fail with a status code 401, not authorised.

Fix one test at a time, starting with can_return_a_collection_of_paginated_products

- add an actingAs with create user
  - `$response = $this->actingAs($this->create('User'))->json('GET', '/api/products');`

Open TestCase and amend the create method, for resourceModel and resourceClass, then return the new resourceClass with the ResourceModel data.

```php
public function create(string $model, array $attributes = [])
{
    $resourceModel = factory("App\\$model")->create($attributes);
    $resourceClass = "App\\Http\\Resources\\$model";

    return new $resourceClass($resourceModel);
}
```

The test was run, but there is no User resource, the user factory returns a user object that implements the authenticable interface

- add an extra parameter for the tests to pass. `$resource = true`
- add an if statement to return the resourceModel (the factory) when there is no resource.

```php
public function create(string $model, array $attributes = [], bool $resource = true)
{
    $resourceModel = factory("App\\$model")->create($attributes);
    $resourceClass = "App\\Http\\Resources\\$model";

    if (!$resource) {
        return $resourceModel;
    }
    return new $resourceClass($resourceModel);
}
```

Back in the test:

- amend the parameters to add an empty array and false:
- add a guard for api.

```php
// ...
// when the endpoint is reached, as an authenticated user
$response = $this->actingAs($this->create('User', [], false), 'api')
                ->json('GET', '/api/products');
// Then the response should be OK and the data should be returned
// ...
```

This test now passes green.

```text
OK (1 test, 31 assertions)
```

Run all tests, the next one which fails is can create a product. Copy the actingAs snippet to all the other tests.

- `$this->actingAs($this->create('User', [], false), 'api')`

All tests now fail on will_fail_with_a_404_if_product_is_not_found, this time copy the snipped to all the required tests:

- Was: `$this->json..`
- Now: `$this->actingAs($this->create('User', [], false), 'api')->json`

All tests now pass.

```php
<?php

namespace Tests\Feature\Http\Controllers\Api;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    public function can_return_a_collection_of_paginated_products()
    {
        // Given a collection of products
        $product1 = $this->create('Product');
        $product2 = $this->create('Product');
        $product3 = $this->create('Product');
        // when the endpoint is reached, as an authenticated user
        $response = $this->actingAs($this->create('User', [], false), 'api')
                        ->json('GET', '/api/products');
        // Then the response should be OK and the data should be returned
        $response->assertStatus(200)
        ->assertJsonStructure([
            'data'  => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'price',
                    'created_at',
                ],
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta'  => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function can_create_a_product()
    {
        $faker = Factory::create();

        // Given
            // user is authenticated
        // When
            // post request create product
            $response = $this->actingAs($this->create('User', [], false), 'api')
                        ->json('POST', '/api/products', [
                            'name'  => $name = $faker->company,
                            'slug'  => str_slug($name),
                            'price' => $price = random_int(10, 100),
                        ]);
        // Then
            // product exists
            $response
            ->assertJsonStructure([
                'id',
                'name',
                'slug',
                'price',
                'created_at',
            ])
            ->assertJson([
                'name'  => $name,
                'slug'  => str_slug($name),
                'price' => $price,
            ])
            ->assertStatus(201);

            $this->assertDatabaseHas('products', [
                'name'  => $name,
                'slug'  => str_slug($name),
                'price' => $price,
            ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function will_fail_with_a_404_if_product_is_not_found()
    {
        // Given
        // When
        $response = $this->actingAs($this->create('User', [], false), 'api')
                    ->json('GET', 'api/products/-1');
        // Then
        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_return_a_product()
    {
        // Given
        $product = $this->create('Product');
        // When
        $response = $this->actingAs($this->create('User', [], false), 'api')
        ->json('GET', "api/products/$product->id");
        // Then
        $response->assertStatus(200)
        ->assertExactJson([
            'id'         => $product->id,
            'name'       => $product->name,
            'slug'       => $product->slug,
            'price'      => $product->price,
            'created_at' => (string)$product->created_at,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function will_fail_with_a_404_if_product_we_want_to_update_is_not_found()
    {
        // Given
        // When
        $response = $this->actingAs($this->create('User', [], false), 'api')
                                 ->json('PUT', 'api/products/-1');
        // Then
        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * @return void
     */
    public function can_update_a_product()
    {
        // Given
        $product = $this->create('Product');
        // When
        $response = $this->actingAs($this->create('User', [], false), 'api')
                                 ->json('PUT', 'api/products/'.$product->id, [
            'name'  => $product->name.'_updated',
            'slug'  => str_slug($product->name.'_updated'),
            'price' => $product->price +10,
        ]);
        // Then
        $response->assertStatus(200)
        ->assertExactJson([
            'id'         => $product->id,
            'name'       => $product->name.'_updated',
            'slug'       => str_slug($product->name.'_updated'),
            'price'      => $product->price +10,
            'created_at' => (string)$product->created_at,
        ]);

        $this->assertDatabaseHas('products', [
            'id'         => $product->id,
            'name'       => $product->name.'_updated',
            'slug'       => str_slug($product->name.'_updated'),
            'price'      => $product->price +10,
            'created_at' => (string)$product->created_at,
            'updated_at' => (string)$product->updated_at,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function will_fail_with_a_404_if_product_we_want_to_delete_is_not_found()
    {
        // Given
        // When
        $response = $this->actingAs($this->create('User', [], false), 'api')
                    ->json('DELETE', 'api/products/-1');
        // Then
        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * @return void
     */
    public function can_delete_a_product()
    {
        // Given
        $product = $this->create('Product');
        // When
        $response = $this->actingAs($this->create('User', [], false), 'api')                         ->json('DELETE', 'api/products/'.$product->id);
        // Then
        $response->assertStatus(204)
            ->assertSee(null);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
```

Add a new test to check for non-authenticated users cannot access the following endpoints for the product api

- create a method with the above description as the name.
- write a test for each endpoint, index, store, show, update and delete.

```php
public function non_authenticated_users_cannot_access_the_following_endpoints_for_the_product_api()
{
    $index = $this->json('GET', '/api/products');
    $index->assertStatus(401);

    $store = $this->json('POST', '/api/products');
    $store->assertStatus(401);

    $show = $this->json('GET', '/api/products/-1');
    $show->assertStatus(401);

    $update = $this->json('PUT', '/api/products/-1');
    $update->assertStatus(401);

    $destroy = $this->json('DELETE', '/api/products/-1');
    $destroy->assertStatus(401);
}
```

This test passes and all tests pass.

Info: in the future if index and show, for example, are allowed, then this test will fail. These assertions within the test can be removed.

```text
PHPUnit 8.0.6 by Sebastian Bergmann and contributors.

...........                                                       11 / 11 (100%)

Time: 7.23 seconds, Memory: 34.00 MB

OK (11 tests, 57 assertions)
```

## Lesson 11 13:53 APIs in Laravel Using TDD - Setting Up Passport

Laravel has an optional plugin for passport, from the command line it can be installed using composer:

```sh
composer require laravel/passport
```

```text
Using version ^7.2 for laravel/passport
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 15 installs, 0 updates, 0 removals
  - Installing psr/http-message (1.0.1): Loading from cache
  - Installing psr/http-factory (1.0.0): Downloading (100%)
  - Installing zendframework/zend-diactoros (2.1.1): Downloading (100%)
  - Installing symfony/psr-http-message-bridge (v1.2.0): Downloading (100%)
  - Installing phpseclib/phpseclib (2.0.15): Downloading (100%)
  - Installing defuse/php-encryption (v2.2.1): Loading from cache
  - Installing lcobucci/jwt (3.2.5): Loading from cache
  - Installing league/event (2.2.0): Loading from cache
  - Installing league/oauth2-server (7.3.3): Downloading (100%)
  - Installing ralouphie/getallheaders (2.0.5): Loading from cache
  - Installing guzzlehttp/psr7 (1.5.2): Loading from cache
  - Installing guzzlehttp/promises (v1.3.1): Loading from cache
  - Installing guzzlehttp/guzzle (6.3.3): Loading from cache
  - Installing firebase/php-jwt (v5.0.0): Loading from cache
  - Installing laravel/passport (v7.2.2): Downloading (100%)
symfony/psr-http-message-bridge suggests installing nyholm/psr7 (For a super lightweight PSR-7/17 implementation)
phpseclib/phpseclib suggests installing ext-libsodium (SSH2/SFTP can make use of some algorithms provided by the libsodium-php extension.)
phpseclib/phpseclib suggests installing ext-mcrypt (Install the Mcrypt extension in order to speed up a few other cryptographic operations.)
phpseclib/phpseclib suggests installing ext-gmp (Install the GMP (GNU Multiple Precision) extension in order to speed up arbitrary precision integer arithmetic operations.)
lcobucci/jwt suggests installing mdanter/ecc (Required to use Elliptic Curves based algorithms.)
Writing lock file
Generating optimized autoload files
```

The **User.php** model:

- Add the `HasApiTokens` trait
- It also needs to be imported.

```php
// ..
use Laravel\Passport\HasApiTokens;
// ..
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
// ..
```

This will add some helper methods to the user model.

Open the **authServiceProvider.php**, in the boot method

- Register the passport route.
- Import the class

```php
// ...
use Laravel\Passport\Passport;
// ...
public function boot()
{
    $this->registerPolicies();
    Passport::routes();
}
// ..
```

By importing the route, there is a bunch, but sometimes oyu only need two or three.

To add the new tables run migrate:

```sh
php artisan migrate
```

```text
Migrating: 2016_06_01_000001_create_oauth_auth_codes_table
Migrated:  2016_06_01_000001_create_oauth_auth_codes_table
Migrating: 2016_06_01_000002_create_oauth_access_tokens_table
Migrated:  2016_06_01_000002_create_oauth_access_tokens_table
Migrating: 2016_06_01_000003_create_oauth_refresh_tokens_table
Migrated:  2016_06_01_000003_create_oauth_refresh_tokens_table
Migrating: 2016_06_01_000004_create_oauth_clients_table
Migrated:  2016_06_01_000004_create_oauth_clients_table
Migrating: 2016_06_01_000005_create_oauth_personal_access_clients_table
Migrated:  2016_06_01_000005_create_oauth_personal_access_clients_table
```

Then install passport install

```sh
php artisan passport:install
```

```text
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client secret: l8BcXW00OCc3RrTIgMlLnWYLGvKDBF7vvf6LdXnU
Password grant client created successfully.
Client ID: 2
Client secret: nABuoRrKDTGj6JOJueMYkI6n9wia9o2w9Ig8vSax
```

To check the route list

```sh
php artisan route:list
```

15 routes have been created. Only two are required at this stage (auth/token and auth/token/refresh). In a future video the tutor will go into more details on how to declare the required passport routes. Possibly in a separate course on passport.

Open **auth.php**

- In guards for api driver, change token to passport.

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport', // token > passport
        'provider' => 'users',
        'hash' => false,
    ],
],
```

For this course we will use personal access tokens. You can also use password grant tokens, with password grant tokens there is no need to test authentication because the code is already tested by Passport, you you have to do is make a post request with the required parameters to get a token back. For the purpose of this TDD course we will write the code to generate personal access tokens.

Create a new test class.

- Create a sub folder from Feature/Http/Controllers called **Auth**
- In the Auth folder, create a file called **AuthControllerTest.php**

```php
<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    public function can_authenticate()
    {
        $this->assertTrue(false);
    }
}
```

The test fails.

Open **web.php**

- Add a post route to /auth/token
- controller is auth\AuthController@store

```php
// ..
Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/token', 'Auth\AuthController@store');
```

Create the AuthController.php in the app/Http/Auth directory

```sh
php artisan make:controller Auth/AuthController
```

Add the store method

```php
<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        //
    }
}
```

In the can_authenticate method of **AuthControllerTest.php**

- Add the user, using the create method for User from the previous lessons.

```php
/**
 * @test
 *
 * @return void
    */
public function can_authenticate()
{
    $response = $this->json('POST', '/auth/token', [
        'email'    => $this->create('User', [], false)->email,
        'password' => 'secret',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token']);
}
```

Test fails

```text
...
Invalid JSON was returned from the route.
...
```

The problem is the user passport is being reset every time the tests are run, as Sqlite is running from memory. The fix it to run a setup routine:

- Add a setUp method in **AuthControllerTest.php** class

```php
public function setUp()
{
    parent::setUp();

    $this->artisan('passport:install');
}
```

## Lesson 12 17:31 APIs in Laravel Using TDD - Social Authentication

This video will test social authentication, using Google.

In these case the API may be supplied by the supplier, so testing a dependency is out of our control.

Laravel has an official package called

```sh
composer require laravel/socialite
```

console.developers.google.com, create a new app (called laravel api)

Goto credentials, create auth client ID, name Laravel API client.

<http://localhost/social/auth/google/callback>

Click create.

Copy the client ID and secret.

Open services PHP

Add goggle

client_id => past in
client_secret =>
redirect => /social/auth/google/callback

Open **web.php**

- Route::get /social.auth/{provider}, Auth\AuthController@redirect
- Route::get social/auth/{provider}/callback, Auth\AuthController@callback

```php
\\ ...
Route::post('/auth/token', 'Auth\AuthController@store');

Route::get('/social/auth/{provider}', 'Auth\AuthController@redirect');
Route::get('/social/auth/{provider}/callback', 'Auth\AuthController@callback');
```

It is recommended to use a socialAuthController to store keys inside the .env file, for this example the existing AuthController will be used. Open **AuthController.php**

Create two methods:

- redirect(\$provider)
- callback(\$provider) methods.

In the redirect method:

- return Socialite::driver(\$provider)->redirect;

In the callback method:

- $user = Socialite::driver($provider)->user();
- \Log::info('user', [$user]);
- \Log::

In **Welcome.blade.php**

- Add a link with url('/social/auth/google')

```php
<body>
    <div class="flex-center position-ref full-height">
        <a href="{{ url('/social/auth/google') }}">
            Login with Google
        </a>
// ...
```

Serve the website

```sh
php artisan serve
```

Open the home page

- Click the link

We are redirected to login with out google account, after logging in the user and token are returned. Open the log created in

The data provided by google can be used to create a new user.

When the user returns and clicks login with Google the user us automatically logged in.

In **AuthController.php**

- `return redirect->away("http://localhost:8000?token=$user->token);`

```php
public function redirect($provider)
{
    return Socialite::driver($provider)->redirect();
}

public function callback($provider)
{
    $user = Socialite::driver($provider)->user();

    \Log::info('user', [$user]);
    \Log::info($user->token);

    return redirect()->away("http://localhost:8000?token=$user->token");
}
```

The test can now be written.

Create a new test in AuthControllerTest.php

Called can_authenticate_using_google()

- \$this->get('/social/auth/google/callback')
- ->assertStatus(302);

This class needs to be Mocked, as it is an external class.

- Configure the abstract user with Mock data.

```php
/**
 * @test
 */
public function can_authenticate_using_google()
{
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')
        ->andReturn(rand())
        ->shouldReceive('getEmail')
        ->andReturn('johnDoe@acme.com')
        ->shouldReceive('getName')
        ->andReturn('John Doe')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->andReturn($provider);

    $this->get('/social/auth/google/callback')
        ->assertStatus(302);
}
```
