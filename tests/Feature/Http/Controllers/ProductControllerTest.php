<?php

namespace Tests\Feature\Http\Controllers;

use App\Product;
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
            // Product 999 does not exist.
        // When
        $this->json('GET', 'api/product/999');
        // Then
        $this->assertResponseStatus(404);
    }

    /** @test */
    public function willFailWithA404IfAProductWeWantToUpdateIsNotFound()
    {
        // Given no product
        // When
        $this->json('PUT', 'api/product/999', []);

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
                'created_at' => (string) $product->created_at,
                'updated_at' => (string) $product->updated_at,
            ]
        );
    }

    /** @test */
    public function willFailWithA404IfProductWeWantToDeleteIsNotFound()
    {
        // Given
        // When
        $this->json('DELETE', 'api/product/999');
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

    /** @test */
    public function canReturnACollectionOfPaginatedProducts()
    {
        $product1 = factory('App\Product')->create();
        $product2 = factory('App\Product')->create();
        $product3 = factory('App\Product')->create();

        $this->json('GET', '/api/product');

        $this->assertResponseOk();

        // Then the database records have been created (check one)
        $this->seeInDatabase('products', [
            "name"  => $product1->name,
            "slug"  => $product1->slug,
            "price" => $product1->price,
        ]);

        // Then, the database contains 3 records
        $this->assertSame(3, Product::all()->count());

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

        // Then
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
    }
}
