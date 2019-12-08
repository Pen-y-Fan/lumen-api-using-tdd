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

    /**
     * Confirm a Product can be added to the database.
     * @test
     */
    public function canCreateAProduct(): void
    {
        $faker = Factory::create();

        // When
            // post request create product
        $response = $this->call('POST', '/api/products', [
            'name'  => $name = $faker->company,
            'price' => $price = random_int(10, 100),
        ]);

        // Then
            // The return response code is 'Created' (201)
        $this->assertEquals(201, $response->status());

        $content = json_decode($response->content(), true);
        $slug = Str::slug($name);

        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('name', $content);
        $this->assertArrayHasKey('slug', $content);
        $this->assertArrayHasKey('price', $content);
        $this->assertArrayHasKey('created_at', $content);

        $this->assertSame($content['name'], $name);
        $this->assertSame($content['slug'], $slug);
        $this->assertSame($content['price'], $price);

        // And the database has the record
        $this->seeInDatabase('products', [
            'name'  => $name,
            'slug'  => $slug,
            'price' => $price,
        ]);
        \Log::info(1, $content);
    }
}
