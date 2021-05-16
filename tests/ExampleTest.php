<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testExample(): void
    {
        $this->get('/');

        self::assertEquals(
            $this->app->version(),
            $this->response->getContent()
        );
    }
}
