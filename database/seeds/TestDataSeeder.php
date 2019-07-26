<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Category;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Models\Supplier;
use Mss\Models\Unit;

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

        $categories = factory(Category::class, 5)->create();
        $suppliers = factory(Supplier::class, 5)->create();
        $units = Unit::all();

        factory(Article::class, 100)->create()->each(function ($article) use ($categories, $suppliers, $units, $faker) {
            $article->suppliers()->attach($suppliers->random(), ['order_number' => $faker->randomNumber(5).$faker->randomNumber(5), 'price' => $faker->randomFloat(0, 5, 100)]);
            $article->category()->associate($categories->random());
            $article->unit()->associate($units->random());
            $article->save();
        });

        factory(Order::class, 10)->create()->each(function ($order) use ($faker) {
            factory(OrderItem::class, $faker->randomFloat(0, 1, 5))->create()->each(function ($orderItem) use ($order, $faker) {
                $orderItem->article_id = Article::whereHas('suppliers', function ($query) use ($order) {
                    $query->where('supplier_id', $order->supplier_id);
                })->inRandomOrder()->first()->id;
                $orderItem->expected_delivery = Carbon::now()->addDays(rand(5, 20));
                $orderItem->order()->associate($order);
                $orderItem->save();
            });
        });

        /* @var $article Article */
        $article = Article::first();
        $quantity = 200;
        $date = Carbon::now();

        for($i = 0; $i < 100; $i++) {
            $type = rand(1,2);
            $change = rand(1, 10);
            $change = ($type == ArticleQuantityChangelog::TYPE_OUTGOING) ? ($change * -1) : $change;

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
