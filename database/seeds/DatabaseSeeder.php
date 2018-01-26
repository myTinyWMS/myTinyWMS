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

        \Mss\Models\Unit::create(['id' => 1, 'name' => 'Stück']);
        \Mss\Models\Unit::create(['id' => 2, 'name' => 'Palette']);
        \Mss\Models\Unit::create(['id' => 3, 'name' => 'Bogen']);
        \Mss\Models\Unit::create(['id' => 4, 'name' => 'Rolle']);
        \Mss\Models\Unit::create(['id' => 5, 'name' => 'Karton']);
        \Mss\Models\Unit::create(['id' => 6, 'name' => 'VE']);
        \Mss\Models\Unit::create(['id' => 7, 'name' => 'Ries']);
        \Mss\Models\Unit::create(['id' => 8, 'name' => 'Paket']);
        \Mss\Models\Unit::create(['id' => 9, 'name' => 'Päckchen']);

        $faker = \Faker\Factory::create();

        $categories = factory(\Mss\Models\Category::class, 2)->create();
        $suppliers = factory(\Mss\Models\Supplier::class, 3)->create();
        $units = \Mss\Models\Unit::all();

        factory(\Mss\Models\Article::class, 50)->create()->each(function ($article) use ($categories, $suppliers, $units, $faker) {
            $article->suppliers()->attach($suppliers->random(), ['order_number' => $faker->randomNumber(5).$faker->randomNumber(5), 'price' => $faker->randomFloat(2, 1, 1000)]);
            $article->categories()->attach($categories->random());
            $article->unit()->associate($units->random())->save();
        });
    }
}
