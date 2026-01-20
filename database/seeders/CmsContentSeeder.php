<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;
use App\Models\PageSection;
use App\Models\CmsService;
use App\Models\Setting;

class CmsContentSeeder extends Seeder
{
    public function run()
    {
        // 1. Settings (Contact Info)
        $settings = [
            ['key' => 'company_name_full', 'value' => 'Louis Marine Equipment Sale', 'group' => 'company', 'type' => 'text'],
            ['key' => 'company_phone', 'value' => '+974 4460 3388', 'group' => 'company', 'type' => 'text'],
            ['key' => 'company_email', 'value' => 'info@louismarineqatar.com', 'group' => 'company', 'type' => 'text'],
            ['key' => 'company_address', 'value' => 'P.O. Box: 9864, Street #24, Industrial Area, Doha, Qatar', 'group' => 'company', 'type' => 'textarea'],
            ['key' => 'google_maps_embed_url', 'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d230888.75010660317!2d51.36531393685952!3d25.285447293587635!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e45c534ffdce87f%3A0x44d9319f78cfd4b1!2sDoha%2C%20Qatar!5e0!3m2!1sen!2s!4v1645437891234!5m2!1sen!2s', 'group' => 'company', 'type' => 'textarea'],
            ['key' => 'social_facebook', 'value' => '#', 'group' => 'social', 'type' => 'text'],
            ['key' => 'social_linkedin', 'value' => '#', 'group' => 'social', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 2. CMS Page (Home)
        $homePage = CmsPage::firstOrCreate(
            ['slug' => 'home'],
            ['title' => 'Home Page', 'is_published' => true]
        );

        // 3. Sections (Hero & About)
        // Hero
        PageSection::updateOrCreate(
            ['cms_page_id' => $homePage->id, 'section_key' => 'hero'],
            [
                'title' => 'Experts in Marine Safety Equipments',
                'subtitle' => 'Approved by major classification societies. Protecting life and property at sea for over 25 years.',
                'image_path' => 'https://louismarineqatar.com/wp-content/uploads/2024/11/banner1-1.jpg', // Using external URL for now, can be replaced with local path later
                'content' => 'EST. 1999', // Using content field for the "Tagline" or extra text
                'order' => 1,
                'is_visible' => true
            ]
        );

        // About
        PageSection::updateOrCreate(
            ['cms_page_id' => $homePage->id, 'section_key' => 'about'],
            [
                'title' => 'Leading Stockist for Marine Safety & Fire Fighting',
                'subtitle' => 'Who We Are',
                'content' => "Louis Marine Equipment Sale, registered in Doha, Qatar since 1999, is a premier provider of marine safety, survival, and fire fighting equipment.\n\nOur service station constitutes state-of-the-art facilities approved by all major classification societies, local flag administrations, and civil defense. We are the first marine safety services company in Qatar to achieve compliance with OHSAS 18001:1999.",
                'image_path' => 'https://louismarineqatar.com/wp-content/uploads/2024/11/hm-abut.jpg',
                'order' => 2,
                'is_visible' => true
            ]
        );

        // 3a. CMS Page (About Us)
        $aboutPage = CmsPage::firstOrCreate(
            ['slug' => 'about-us'],
            ['title' => 'About Us', 'meta_description' => 'Learn more about Louis Marine Qatar, our history, and our commitment to marine safety.', 'is_published' => true]
        );

        // About Us Sections
        PageSection::updateOrCreate(
            ['cms_page_id' => $aboutPage->id, 'section_key' => 'story'],
            [
                'title' => 'Our Story',
                'subtitle' => 'Since 1999',
                'content' => "Louis Marine Equipment Sale, registered in Doha, Qatar since 1999, is a premier provider of marine safety, survival, and fire fighting equipment.\n\nWe have grown from a small trading company to a leading stockist and service provider, trusted by major maritime operators in the region. Our journey is defined by a relentless pursuit of excellence and a deep commitment to protecting life at sea.",
                'image_path' => 'https://louismarineqatar.com/wp-content/uploads/2024/11/hm-abut.jpg',
                'order' => 1,
                'is_visible' => true
            ]
        );

        PageSection::updateOrCreate(
            ['cms_page_id' => $aboutPage->id, 'section_key' => 'vision'],
            [
                'title' => 'Our Vision & Mission',
                'subtitle' => 'Safety First',
                'content' => "To be the most reliable and preferred partner for marine safety solutions in the Middle East.\n\nWe aim to deliver world-class products and services that ensure the safety of vessels, crew, and offshore assets, adhering to the strictest international standards.",
                'image_path' => '',
                'order' => 2,
                'is_visible' => true
            ]
        );

        // 3b. CMS Page (Products)
        $productsPage = CmsPage::firstOrCreate(
            ['slug' => 'products'],
            ['title' => 'Our Products', 'meta_description' => 'Explore our wide range of marine safety and fire fighting equipment.', 'is_published' => true]
        );

        // Product Categories (as Sections)
        PageSection::updateOrCreate(
            ['cms_page_id' => $productsPage->id, 'section_key' => 'lifesaving'],
            [
                'title' => 'Life Saving Appliances',
                'subtitle' => 'Core Products',
                'content' => "We stock a vast array of LSA equipment including:\n- Life Rafts (Solas & Non-Solas)\n- Life Jackets & Immersion Suits\n- Life Buoys & Lights\n- Emergency Rations & Water\n- Pyrotechnics (Flares, Smoke Signals)",
                'image_path' => 'https://louismarineqatar.com/wp-content/uploads/2024/11/img-1.jpg',
                'order' => 1,
                'is_visible' => true
            ]
        );

        PageSection::updateOrCreate(
            ['cms_page_id' => $productsPage->id, 'section_key' => 'firefighting'],
            [
                'title' => 'Fire Fighting Equipment',
                'subtitle' => 'Protection',
                'content' => "Complete fire safety solutions:\n- Portable & Wheeled Fire Extinguishers\n- Fire Hoses, Nozzles & Couplings\n- Fireman Outfits & Breathing Apparatus\n- Fire Blankets & Axes",
                'image_path' => 'https://louismarineqatar.com/wp-content/uploads/2024/11/img-2.jpg', 
                'order' => 2,
                'is_visible' => true
            ]
        );

        // 4. Services
        $services = [
            [
                'title' => 'Supply',
                'slug' => 'supply',
                'description' => 'Comprehensive range of products including fire extinguishers, life rafts, life jackets, and emergency flares tailored to your vessel\'s needs.',
                'content' => "WE SUPPLY ALL TYPES OF MARINE SAFETY AND FIRE FIGHTING EQUIPMENT WHICH INCLUDES:\n\n- Fire Extinguishers (Portable & Wheeled)\n- Fire Hoses, Nozzles, Couplings\n- Fireman Outfits, Breathing Apparatus (SCBA & EEBD)\n- Life Rafts, Life Jackets, Immersion Suits\n- Pyrotechnics, Life Buoys, Lights\n- Gas Detectors, Fall Protection\n- IMO Signage & Flags",
                'icon_class' => '<svg class="h-8 w-8 text-[#0b1c3c] group-hover:text-red-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
                'order' => 1
            ],
            [
                'title' => 'Installation',
                'slug' => 'installation',
                'description' => 'Expert installation by certified technicians ensuring optimal performance and full compliance with international safety standards.',
                'content' => "OUR EXPERT TEAM PROVIDES PROFESSIONAL INSTALLATION SERVICES FOR:\n\n- Fixed Fire Suppression Systems (CO2, Foam, FM200, Novec)\n- Fire Detection & Alarm Systems\n- Gas Detection Systems\n- Liferaft Davits & Release Mechanisms\n- Navigational Equipment\n\nWe ensure all installations meet manufacturer specifications and Class Society requirements.",
                'icon_class' => '<svg class="h-8 w-8 text-[#0b1c3c] group-hover:text-red-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
                'order' => 2
            ],
            [
                'title' => 'Maintenance',
                'slug' => 'maintenance',
                'description' => 'Approved service station for periodic inspections, servicing, and repairs to maintain the highest safety and reliability standards.',
                'content' => "WE OFFER COMPREHENSIVE SERVICE & MAINTENANCE PACKAGES:\n\n- Annual & 5-Yearly Inspections of LSA & FFE\n- Life Raft Servicing & Certification\n- Breathing Apparatus & EEBD Testing\n- Fire Extinguisher Refilling & Testing\n- Spares & Repairs\n\nOur service station is fully equipped and approved by major Class Societies.",
                'icon_class' => '<svg class="h-8 w-8 text-[#0b1c3c] group-hover:text-red-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>',
                'order' => 3
            ]
        ];

        foreach ($services as $service) {
            CmsService::updateOrCreate(['title' => $service['title']], $service);
        }
    }
}
