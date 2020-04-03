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
        \Mss\Models\User::create([
            'email' => 'admin@example.com',
            'name' => 'admin',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'remember_token' => str_random(10),
            'settings' => []
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class
        ]);

        \Mss\Models\User::where('email', 'admin@example.com')->first()->assignRole('admin');

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
