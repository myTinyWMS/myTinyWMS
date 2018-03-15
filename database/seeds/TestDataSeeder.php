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
            $article->suppliers()->attach($suppliers->random(), ['order_number' => $faker->randomNumber(5).$faker->randomNumber(5), 'price' => $faker->randomFloat(0, 5, 100)]);
            $article->category()->associate($categories->random());
            $article->unit()->associate($units->random())->save();
        });

        factory(\Mss\Models\Order::class, 20)->create()->each(function ($order) use ($faker) {
            factory(\Mss\Models\OrderItem::class, $faker->randomFloat(0, 1, 10))->create()->each(function ($orderItem) use ($order, $faker) {
                $orderItem->article_id = \Mss\Models\Article::whereHas('suppliers', function ($query) use ($order) {
                    $query->where('supplier_id', $order->supplier_id);
                })->inRandomOrder()->first()->id;
                $orderItem->order()->associate($order);
                $orderItem->save();
            });
        });

        /* @var $article \Mss\Models\Article */
        $article = \Mss\Models\Article::first();
        $quantity = 200;
        $date = \Carbon\Carbon::now();

        for($i = 0; $i < 100; $i++) {
            $type = rand(1,2);
            $change = rand(1, 10);
            $change = ($type == \Mss\Models\ArticleQuantityChangelog::TYPE_OUTGOING) ? ($change * -1) : $change;

            $date = $date->subDays(rand(1, 3));

            $article->quantityChangelogs()->create([
                'user_id' => 1,
                'type' => $type,
                'change' => $change,
                'new_quantity' => $quantity,
                'created_at' => $date,
                'updated_at' => $date
            ]);

            $quantity = $quantity - $change;
        }

        $article->quantity = $quantity;
        $article->save();
    }
}
