<?php

namespace Tests;

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
