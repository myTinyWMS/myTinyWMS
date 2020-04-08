<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
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

        $this->importCategoriesFromCSV();

        $suppliers = collect();
        $suppliers->push(Supplier::create(['name' => 'Aldi', 'email' => 'aldi@example.com']));
        $suppliers->push(Supplier::create(['name' => 'Lidl', 'email' => 'lidl@example.com']));
        $suppliers->push(Supplier::create(['name' => 'Netto', 'email' => 'netto@example.com']));
        $suppliers->push(Supplier::create(['name' => 'Globus', 'email' => 'globus@example.com']));
        $suppliers->push(Supplier::create(['name' => 'Kaufland', 'email' => 'kaufland@example.com']));
        $suppliers->push(Supplier::create(['name' => 'Penny', 'email' => 'penny@example.com']));
        $suppliers->push(Supplier::create(['name' => 'Konsum', 'email' => 'konsum@example.com']));

        $units = Unit::all();

        $this->importArticlesFromCSV($faker, $suppliers, $units);

        $this->command->getOutput()->writeln('categories, suppliers and articles imported');

        factory(Order::class, 10)->create()->each(function ($order, $key) use ($faker) {
            $itemCount = ($key == 0) ? 1 : $faker->randomFloat(0, 1, 5);    // we need at least one order with only one item
            factory(OrderItem::class, $itemCount)->create()->each(function ($orderItem) use ($order, $faker) {
                $orderItem->article_id = Article::whereHas('suppliers', function ($query) use ($order) {
                    $query->where('supplier_id', $order->supplier_id);
                })->inRandomOrder()->first()->id;
                $orderItem->expected_delivery = Carbon::now()->addDays(rand(5, 20));
                $orderItem->order()->associate($order);
                $orderItem->save();
            });
        });

        $this->command->getOutput()->writeln('orders created');

        $this->command->getOutput()->writeln('creating changelogs');
        if (config('app.demo')) {
            $bar = $this->command->getOutput()->createProgressBar(Article::count());
            $bar->start();
            Article::all()->each(function ($article) use ($bar) {
                $this->buildChangelogForArticle($article);
                $bar->advance();
            });
            $bar->finish();
        } else {
            Article::inRandomOrder()->take(10)->each(function ($article) {
                $this->buildChangelogForArticle($article);
            });
        }

        $this->command->getOutput()->writeln('creating articles for dashboard');

        /**
         * create 5 articles where the quantity is too low
         */
        $emptyArticles = Article::where('quantity', '>', 3)->inRandomOrder()->take(5)->get();
        $emptyArticles->each(function ($article) {
            /* @var $article Article */
            $article->quantity = $article->min_quantity - 1;
            $article->save();
        });
    }

    protected function buildChangelogForArticle(Article $article) {
        $quantity = 200;
        $date = Carbon::now();

        for($i = 0; $i < 20; $i++) {
            $type = rand(1,2);
            $change = rand(1, 10);
            $change = ($type == ArticleQuantityChangelog::TYPE_OUTGOING) ? ($change * -1) : $change;

            $date = $date->subDays(rand(1, 3))->setHour(rand(6,18))->setMinute(rand(0,59));

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

    /**
     * @param \Faker\Factory $faker
     * @param Collection $suppliers
     * @param Collection $units
     */
    protected function importArticlesFromCSV(\Faker\Generator $faker, Collection $suppliers, Collection $units) {
        if (($handle = fopen(database_path('seeds/testdata_articles.csv'), "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $article = Article::create([
                    'id' => $data[0],
                    'name' => $data[1],
                    'category_id' => $data[2],
                    'cost_center' => 1,
                    'status' => Article::STATUS_ACTIVE,
                    'quantity' => $faker->numberBetween(11, 20),
                    'min_quantity' => $faker->numberBetween(1, 10)
                ]);

                $article->suppliers()->attach($suppliers->random(), ['order_number' => $faker->randomNumber(5).$faker->randomNumber(5), 'price' => $faker->randomFloat(0, 1, 20) * 100, 'delivery_time' => $faker->randomFloat(0, 1, 10), 'order_quantity' => $faker->randomFloat(0, 1, 10)]);
                $article->unit()->associate($units->random());
                $article->save();
            }
            fclose($handle);
        }
    }

    protected function importCategoriesFromCSV() {

        if (($handle = fopen(database_path('seeds/testdata_categories.csv'), "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                Category::create(['id' => $data[0], 'name' => $data[1]]);
            }
            fclose($handle);
        }
    }
}
