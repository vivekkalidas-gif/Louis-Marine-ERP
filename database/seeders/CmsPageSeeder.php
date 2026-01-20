<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;

class CmsPageSeeder extends Seeder
{
    public function run()
    {
        // About Page Content
        $aboutPage = CmsPage::firstOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Louis Marine',
                'content' => 'Leading marine service provider in Qatar.',
                'meta_title' => 'About Us - Louis Marine Qatar',
                'meta_description' => 'Learn about our history, mission, and commitment to marine excellence in Qatar.',
                'is_published' => true,
            ]
        );

            $aboutPage->sections()->create([
                'section_key' => 'mission',
                'title' => 'Mission & Vision',
                'content' => "Mission: To deliver reliable, efficient, and compliant marine services that ensure the safety and operational continuity of our clients' assets.\n\nVision: To be the preferred marine solutions provider in the Gulf region, known for technical excellence and integrity.",
                'order' => 2,
                'is_visible' => true,
            ]);

        // Home Page Content
        $homePage = CmsPage::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'content' => 'Welcome to Louis Marine Qatar',
                'meta_title' => 'Louis Marine Qatar - Premium Marine Services',
                'meta_description' => 'Your trusted partner for marine engineering, safety, and supplies in Qatar.',
                'is_published' => true,
            ]
        );

        if ($homePage->sections()->where('section_key', 'hero')->doesntExist()) {
            $homePage->sections()->create([
                'section_key' => 'hero',
                'title' => 'Premium Marine Services in Qatar',
                'content' => 'Delivering excellence in marine engineering, safety equipment, and provisions since 2010.',
                'image_path' => 'hero.jpg', // Placeholder, user can update via CMS
                'order' => 1,
                'is_visible' => true,
            ]);
        }
    }
}
