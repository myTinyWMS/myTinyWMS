<?php

use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
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
    }
}
