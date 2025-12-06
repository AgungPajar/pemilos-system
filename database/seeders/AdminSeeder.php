<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::firstOrCreate(
            ['email' => 'admin@pemilosbosdugar.org'],
            [
                'name' => 'Administrator Bosdugar',
                'password' => Hash::make('PemilosBosdugar2025!'),
            ]
        );
    }                                   
}
