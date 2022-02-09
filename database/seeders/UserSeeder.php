<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Mohammad Zahwi',
            'email' => 'mohammadzahwy2017@gmail.com',
            'password' => Hash::make('admin@123')
        ]);
    }
}
