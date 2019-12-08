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
}
