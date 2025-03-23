<?php

namespace Database\Seeders;

use App\Models\Platform;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $platforms = Platform::all();
        $posts = Post::factory()
            ->count(50)
            ->create([
                'user_id' => function () use ($users) {
                    return $users->random()->id;
                },
            ]);

        foreach ($posts as $post) {
            $post->platforms()->attach([
                $platforms->random()->id,
            ]);
        }
    }
}
