<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Mss\Models\User::class, 1)->create([
            'email' => 'admin@example.com'
        ]);

        $faker = \Faker\Factory::create();

        $units = factory(\Mss\Models\Unit::class, 2)->create();
        $categories = factory(\Mss\Models\Category::class, 2)->create();
        $suppliers = factory(\Mss\Models\Supplier::class, 3)->create();

        factory(\Mss\Models\Article::class, 50)->create()->each(function ($article) use ($categories, $suppliers, $units, $faker) {
            $article->suppliers()->attach($suppliers->random(), ['order_number' => $faker->randomNumber(5).$faker->randomNumber(5), 'price' => $faker->randomFloat(2, 1, 1000)]);
            $article->categories()->attach($categories->random());
            $article->unit()->associate($units->random())->save();
        });
    }
}
