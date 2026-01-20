<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\Feature;
use App\Models\Stat;
use App\Models\CtaSection;
use Illuminate\Database\Seeder;

class LouisSafetyLandingPageSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Populating Landing Page with Louis Safety content...');

        // Clear existing data
        HeroSlide::truncate();
        Feature::truncate();
        Stat::truncate();
        CtaSection::truncate();

        // Hero Slide
        $this->command->info('Creating hero slide...');
        HeroSlide::create([
            'title' => "Marine & Ship Safety\nInspections in UAE",
            'subtitle' => 'ISO 9001 Certified Safety Company',
            'description' => 'Louis Safety is a fast growing ISO 9001 certified safety company in the field of Marine and Oil & Gas since 2001 serving Middle East customers. Our years of combined experience and expertise are foundations for industry-leading services and products.',
            'background_image' => null,
            'background_video_url' => null,
            'cta_text_1' => 'Get Started',
            'cta_link_1' => '/contact',
            'cta_text_2' => 'Our Services',
            'cta_link_2' => '/products',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        // Features / Services
        $this->command->info('Creating features...');
        $features = [
            [
                'title' => 'Lifeboat Inspections',
                'description' => 'Comprehensive inspection and maintenance services for lifeboats ensuring compliance with international maritime safety standards.',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Liferaft Inspections',
                'description' => 'Professional liferaft servicing, inspection, and certification services to ensure your crew\'s safety at sea.',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Fire Fighting Equipment',
                'description' => 'Inspection, maintenance, and certification of fire fighting appliances and equipment for marine vessels.',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Navigation & Communications',
                'description' => 'Expert services for navigation equipment and communication systems ensuring safe maritime operations.',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Automation & Calibration',
                'description' => 'Precision automation services, calibration, and certification for marine equipment and systems.',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Load Testing Services',
                'description' => 'Professional load testing and rental of equipment for marine and offshore applications.',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }

        // Statistics
        $this->command->info('Creating statistics...');
        $stats = [
            [
                'label' => 'Years of Experience',
                'value' => '20+',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'sort_order' => 1,
            ],
            [
                'label' => 'ISO Certified',
                'value' => '9001',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>',
                'sort_order' => 2,
            ],
            [
                'label' => 'Ports Served',
                'value' => '6+',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
                'sort_order' => 3,
            ],
            [
                'label' => 'Products Available',
                'value' => '120+',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                'sort_order' => 4,
            ],
        ];

        foreach ($stats as $stat) {
            Stat::create($stat);
        }

        // CTA Sections
        $this->command->info('Creating CTA sections...');
        CtaSection::create([
            'section_key' => 'main_cta',
            'title' => 'Ready to Ensure Safety & Compliance?',
            'description' => 'Contact our expert team today for inspections, certifications, and marine servicing across UAE, Oman, Qatar, Saudi Arabia, Bahrain and Kuwait.',
            'button_text' => 'Contact Us Now',
            'button_link' => '/contact',
            'background_color' => '#0b1c3c',
            'is_active' => true,
        ]);

        CtaSection::create([
            'section_key' => 'service_cta',
            'title' => 'Authorized Service Provider',
            'description' => 'Louis Safety is now Panama Flag Service provider for Lifeboats and Authorized Service Station for Shanghai Youlong in UAE.',
            'button_text' => 'Learn More',
            'button_link' => '/products',
            'background_color' => '#dc2626',
            'is_active' => true,
        ]);

        $this->command->info('✓ Landing Page populated successfully with Louis Safety content!');
        $this->command->info('✓ Hero Slide: 1');
        $this->command->info('✓ Features: 6');
        $this->command->info('✓ Statistics: 4');
        $this->command->info('✓ CTA Sections: 2');
    }
}
