<?php

namespace Database\Seeders;

use App\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory(10)->create();
        $this->command->info('Ten Products created');
        gc_collect_cycles();
    }
}
