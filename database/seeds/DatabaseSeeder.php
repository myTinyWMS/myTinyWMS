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

        $units = factory(\Mss\Models\Unit::class, 2)->create();
        $categories = factory(\Mss\Models\Category::class, 2)->create();
        $suppliers = factory(\Mss\Models\Supplier::class, 3)->create();

        factory(\Mss\Models\Article::class, 50)->create()->each(function ($article) use ($categories, $suppliers, $units) {
            $article->suppliers()->attach($suppliers->random(), ['order_number' => 1, 'price' => 1]);
            $article->categories()->attach($categories->random());
            $article->unit()->associate($units->random())->save();
        });
    }
}
