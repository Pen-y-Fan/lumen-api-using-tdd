<?php

namespace Tests\Http\Controllers\Api\ProductControllerTest;

use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProductControllerTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function example()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(),
            $this->response->getContent()
        );
    }
}
