<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class DefaultHeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating default hero slide...');

        HeroSlide::create([
            'title' => "Premium Marine\nServices in Qatar",
            'subtitle' => null,
            'description' => 'Providing world-class marine engineering, surveying, and maintenance solutions tailored to your needs.',
            'background_image' => null,
            'background_video_url' => null,
            'cta_text_1' => 'Get Started',
            'cta_link_1' => '/contact',
            'cta_text_2' => 'Our Services',
            'cta_link_2' => '/page/services',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->command->info('✓ Default hero slide created successfully!');
    }
}
