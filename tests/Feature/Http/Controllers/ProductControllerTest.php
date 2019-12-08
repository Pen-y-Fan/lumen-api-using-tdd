<?php

namespace Tests\Feature\Http\Controllers;

use Faker\Factory;
use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

class ProductControllerTest extends TestCase
{
    use DatabaseMigrations;

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
            "name"  => $product->name,
            "slug"  => $product->slug,
            "price" => $product->price,
        ]);

    // Then
        $this->seeJsonContains([
            "name"  => $product->name,
            "slug"  => $product->slug,
            "price" => $product->price
        ]);
    }

    /** @test */
    public function willFailWithA404IfProductIsNotFound()
    {
        // Given
            // Product -1 does not exist.
        // When
            $this->json('GET', 'api/products/-1');
        // Then
            $this->assertResponseStatus(404);
    }

    /** @test */
    public function willFailWithA404IfAProductWeWantToUpdateIsNotFound()
    {
        // Given no product
        // When
        $this->json('PUT', 'api/product/-1', []);

        // Then
        $this->assertResponseStatus(404);
    }


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

        // Then
        $this->seeJsonContains($newProduct);

        // Then
        $this->seeInDatabase(
            'products',
            [
            'id'         => $product->id,
            'name'       => $newProduct['name'],
            'slug'       => $newProduct['slug'],
            'price'      => $newProduct['price'],
            'created_at' => (string)$product->created_at,
            'updated_at' => (string)$product->updated_at,
            ]
        );
    }
}
