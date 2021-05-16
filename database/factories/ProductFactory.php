<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $product = Product::class;

    /**
     * @throws \Exception
     */
    public function definition()
    {
        $name = $this->faker->text(60);

        return [
            'name'  => $name,
            'slug'  => Str::slug($name),
            'price' => random_int(10, 100),

        ];
    }
}
