<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProductControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Confirm a Product can be added to the database.
     * @test
     */
    public function canCreateAProduct(): void
    {
        // When
            // post request create product
        $response = $this->call('POST', '/api/products');

        // Then
            // Created response code (201)
        $this->assertEquals(201, $response->status());
    }
}
