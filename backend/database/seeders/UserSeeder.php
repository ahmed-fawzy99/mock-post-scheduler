<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Root user - Admin
        $root = User::factory()->create([
            'name' => 'Super Root',
            'email' => 'super@root.com',
        ]);
        $root->assignRole('admin');

        // Normal users
        $users = User::factory(15)->create();
        $users->each(function ($user) {
            $user->assignRole('user');
        });

        // choose random user to be the author
        $users->random()->disallowedPlatforms()->attach([1,5,7]);

    }
}
