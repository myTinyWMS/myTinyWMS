<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var Factory $factory */

use Carbon\Carbon;
use Mss\Models\User;
use Mss\Models\Order;
use Mss\Models\Article;
use Mss\Models\Supplier;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Mss\Models\Article::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'quantity' => $faker->numberBetween(1, 10),
        'status' => Article::STATUS_ACTIVE,
        'cost_center' => 1
    ];
});

$factory->define(Mss\Models\ArticleNote::class, function (Faker\Generator $faker) {
    return [
        'content' => $faker->sentence
    ];
});

$factory->define(Mss\Models\Category::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word
    ];
});

$factory->define(Mss\Models\Supplier::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'email' => $faker->safeEmail
    ];
});

$factory->define(Mss\Models\Unit::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word
    ];
});

$factory->define(Mss\Models\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('password'),
        'remember_token' => str_random(10),
        'settings' => []
    ];
});

$factory->define(Mss\Models\OrderItem::class, function (Faker\Generator $faker) {
    return [
        'article_id' => Article::inRandomOrder()->first()->id,
        'price' => $faker->randomFloat(0, 500, 100000),
        'quantity' => $faker->randomFloat(0, 1, 30)
    ];
});

$factory->define(Mss\Models\Order::class, function (Faker\Generator $faker) {
    $orderDate = Carbon::now()->subDays($faker->randomNumber(1));
    return [
        'supplier_id' => Supplier::inRandomOrder()->first()->id,
        'internal_order_number' => $orderDate->format('ymd').$faker->randomNumber(2, true),
        'external_order_number' => $faker->randomNumber(5),
        'total_cost' => $faker->randomFloat(0, 500, 10000),
        'shipping_cost' => $faker->randomFloat(0, 500, 5000),
        'order_date' => $orderDate,
        'expected_delivery' => $orderDate->copy()->addDays($faker->randomFloat(0,1,50))
    ];
});

$factory->define(Mss\Models\OrderMessage::class, function (Faker\Generator $faker) {
    return [
        'order_id' => Order::inRandomOrder()->first()->id,
        'received' => Carbon::now()->subDay(),
        'sender' => [$faker->safeEmail],
        'receiver' => ['System'],
        'subject' => $faker->sentence,
        'htmlbody' => $faker->paragraph,
        'read' => 1
    ];
});