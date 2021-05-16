<?php

namespace Tests\Feature\Http\Controllers;

use App\Product;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function canCreateAProduct(): void
    {
        $product = Product::factory()->make();

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
            'name'  => $product->name,
            'slug'  => $product->slug,
            'price' => $product->price,
        ]);

        // And the database has the record
        $this->seeInDatabase('products', [
            'name'  => $name,
            'slug'  => $slug,
            'price' => $price,
        ]);
    }

    /**
     * @test
     */
    public function canReturnAProduct(): void
    {
        // Given
        $product = Product::factory()->create();

        // When
        $this->json('GET', '/api/product/' . $product->id);

        // Then
        $this->assertResponseOk();

        // Then
        $this->seeInDatabase('products', [
            'name'  => $product->name,
            'slug'  => $product->slug,
            'price' => $product->price,
        ]);

        // Then
        $this->seeJsonContains([
            'name'  => $product->name,
            'slug'  => $product->slug,
            'price' => $product->price,
        ]);
    }

    /**
     * @test
     */
    public function willFailWithA404IfProductIsNotFound(): void
    {
        // Given
        // Product 999 does not exist.
        // When
        $this->json('GET', '/api/product/999');
        // Then
        $this->assertResponseStatus(404);
    }

    /**
     * @test
     */
    public function willFailWithA404IfAProductWeWantToUpdateIsNotFound(): void
    {
        // Given no product
        // When
        $this->json('PUT', '/api/product/999', []);

        // Then
        $this->assertResponseStatus(404);
    }

    /**
     * @test
     */
    public function canUpdateAProduct(): void
    {
        // Given
        $product = Product::factory()->create();

        // When
        $newProduct = [
            'name'  => $product->name . '_updated',
            'slug'  => Str::slug($product->name . '_updated'),
            'price' => $product->price + 10,
        ];

        $this->json('PUT', '/api/product/' . $product->id, $newProduct);

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

    /**
     * @test
     */
    public function willFailWithA404IfProductWeWantToDeleteIsNotFound(): void
    {
        // Given
        // When
        $this->json('DELETE', '/api/product/999');
        // Then
        $this->assertResponseStatus(404);
    }

    /**
     * @test
     */
    public function canDeleteAProduct(): void
    {
        // Given
        $product = Product::factory()->create();
        // When
        $this->json('DELETE', '/api/product/' . $product->id);
        // Then
        $this->assertResponseStatus(204);

        $this->notSeeInDatabase('products', [
            'id' => $product->id,
        ]);
    }

    /**
     * @test
     */
    public function canReturnACollectionOfPaginatedProducts(): void
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();
        $this->json('GET', '/api/product');
        $this->withoutMiddleware();

        $this->assertResponseOk();

        // Then the database records have been created (check one)
        $this->seeInDatabase('products', [
            'name'  => $product1->name,
            'slug'  => $product1->slug,
            'price' => $product1->price,
        ]);

        // Then, the database contains 3 records
        self::assertSame(3, Product::all()->count());

        // Then
        $this->seeJsonStructure([
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

        // Then
        $this->seeJsonEquals(
            [
                'data' => [
                    [
                        'created_at' => (string) $product1->created_at,
                        'id'         => $product1->id,
                        'name'       => $product1->name,
                        'price'      => $product1->price,
                        'slug'       => $product1->slug,
                    ],
                    [
                        'created_at' => (string) $product2->created_at,
                        'id'         => $product2->id,
                        'name'       => $product2->name,
                        'price'      => $product2->price,
                        'slug'       => $product2->slug,
                    ],
                    [
                        'created_at' => (string) $product3->created_at,
                        'id'         => $product3->id,
                        'name'       => $product3->name,
                        'price'      => $product3->price,
                        'slug'       => $product3->slug,
                    ],
                ],
                'links' => [
                    'first' => 'http://localhost/api/product?page=1',
                    'last'  => 'http://localhost/api/product?page=1',
                    'next'  => null,
                    'prev'  => null,
                ],
                'meta' => [
                    'current_page' => 1,
                    'from'         => 1,
                    'last_page'    => 1,
                    'links'        => [
                        [
                            'active' => false,
                            'label'  => 'pagination.next',
                            'url'    => null,
                        ],
                        [
                            'active' => false,
                            'label'  => 'pagination.previous',
                            'url'    => null,
                        ],
                        [
                            'active' => true,
                            'label'  => '1',
                            'url'    => 'http://localhost/api/product?page=1',
                        ],
                    ],
                    'path'     => 'http://localhost/api/product',
                    'per_page' => 15,
                    'to'       => 3,
                    'total'    => 3,
                ],
            ]
        );
    }
}
