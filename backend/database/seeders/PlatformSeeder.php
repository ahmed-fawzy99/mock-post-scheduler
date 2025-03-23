<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socials = [
            'Twitter',
            'Instagram',
            'LinkedIn',
            'Pinterest',
            'Snapchat',
            'TikTok',
            'YouTube',
        ];

        foreach($socials as $social) {
            Platform::create([
                'name' => $social,
                'type' => strtolower($social),
                'character_limit' => match($social) {
                    'Twitter' => 280,
                    'Instagram' => 2200,
                    'LinkedIn' => 1300,
                    'Pinterest' => 500,
                    'Snapchat' => 250,
                    'TikTok' => 150,
                    'YouTube' => 5000,
                    default => 280,
                },
            ]);
        }
    }
}
