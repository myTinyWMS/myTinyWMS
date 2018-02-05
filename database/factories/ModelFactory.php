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

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Mss\Models\Article::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'quantity' => $faker->numberBetween(1, 10)
    ];
});

$factory->define(Mss\Models\Category::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word
    ];
});

$factory->define(Mss\Models\Supplier::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word
    ];
});

$factory->define(Mss\Models\Unit::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word
    ];
});

$factory->define(Mss\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('password'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Mss\Models\OrderItem::class, function (Faker\Generator $faker) {

    return [
        'article_id' => \Mss\Models\Article::inRandomOrder()->first()->id,
        'price' => $faker->randomFloat(0, 5, 100),
        'quantity' => $faker->randomNumber(),
    ];
});

$factory->define(Mss\Models\Order::class, function (Faker\Generator $faker) {
    $orderDate = \Carbon\Carbon::now()->subDays($faker->randomNumber(1));
    return [
        'supplier_id' => \Mss\Models\Supplier::inRandomOrder()->first()->id,
        'internal_order_number' => $orderDate->format('ymd').$faker->randomNumber(2, true),
        'external_order_number' => $faker->randomNumber(5),
        'total_cost' => $faker->randomFloat(0, 50, 1000),
        'shipping_cost' => $faker->randomFloat(0, 5, 50),
        'order_date' => $orderDate,
        'expected_delivery' => $orderDate->copy()->addDays($faker->randomFloat(0,1,50))
    ];
});