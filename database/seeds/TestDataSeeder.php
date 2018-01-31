<?php

use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        $categories = factory(\Mss\Models\Category::class, 30)->create();
        $suppliers = factory(\Mss\Models\Supplier::class, 20)->create();
        $units = \Mss\Models\Unit::all();

        factory(\Mss\Models\Article::class, 500)->create()->each(function ($article) use ($categories, $suppliers, $units, $faker) {
            $article->suppliers()->attach($suppliers->random(), ['order_number' => $faker->randomNumber(5).$faker->randomNumber(5), 'price' => $faker->randomFloat(2, 1, 1000)]);
            $article->category()->associate($categories->random());
            $article->unit()->associate($units->random())->save();
        });
    }
}
