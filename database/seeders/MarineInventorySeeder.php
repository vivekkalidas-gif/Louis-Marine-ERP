<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarineInventorySeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'store@louismarine.qa')->first() ?? User::first();
        $userId = $user->id;

        $categories = [
            'Life Jackets & PFDs' => [
                'SOLAS Life Jacket Adult', 'SOLAS Life Jacket Child', 'Inflatable Life Jacket 150N', 'Inflatable Life Jacket 275N',
                'Work Vest 50N', 'Life Jacket Light (Danbuoy)', 'Life Jacket Whistle', 'Immersion Suit Type 1', 
                'Thermal Protective Aid (TPA)', 'Life Jacket CO2 Cylinder 33g'
            ],
            'Fire Fighting Equipment' => [
                'Fire Extinguisher DCP 6kg', 'Fire Extinguisher DCP 9kg', 'Fire Extinguisher CO2 5kg', 'Fire Extinguisher Foam 9L',
                'Fire Hose 1.5 inch x 15m', 'Fire Hose 2.5 inch x 20m', 'Fire Hose Nozzle Fog/Jet', 'Breathing Apparatus Set (SCBA)',
                'Spare Cylinder 6L 300bar', 'Fireman Axe with Insulated Handle'
            ],
            'Pyrotechnics & Signals' => [
                'Red Hand Flare', 'Red Parachute Rocket', 'Buoyant Smoke Signal (Orange)', 'Line Throwing Apparatus (LTA)',
                'Man Overboard (MOB) Smoke & Light', 'Bridge Distress Kit', 'Lifeboat Distress Kit', 'Signal Mirror', 
                'Radar Reflector', 'Signal Flags Set'
            ],
            'Life Rafts & Boats' => [
                'Inflatable Life Raft 6-Person', 'Inflatable Life Raft 10-Person', 'Inflatable Life Raft 25-Person', 'Hydrostatic Release Unit (HRU)',
                'Life Raft Cradle', 'Life Raft Lashing Strap', 'Survival Ration 500g', 'Drinking Water 500ml Sachet', 
                'First Aid Kit for Lifeboat', 'Fishing Kit'
            ],
            'Navigation Equipment' => [
                'Marine Binoculars 7x50', 'Magnetic Compass', 'VHF Handheld Radio', 'Chart Plotter',
                'GPS Antenna', 'Echo Sounder Transducer', 'Navigational Chart (Paper)', 'Parallel Ruler',
                'Divider 8 inch', 'Sextant'
            ],
            'First Aid & Medical' => [
                'Ship Captain Medical Guide', 'Oxygen Resuscitation Kit', 'Stretcher (Neil Robertson)', 'First Aid Kit (SOLAS A)',
                'Burn Gel Dressing', 'Bandage Crepe 7.5cm', 'Surgical Scissors', 'Antiseptic Solution 500ml',
                'Eye Wash Station', 'Medical Oxygen Cylinder'
            ],
            'Safety Signage' => [
                'IMO Symbol: Life Jacket (150x150)', 'IMO Symbol: Fire Extinguisher (150x150)', 'IMO Symbol: Exit (Green/White)', 'IMO Symbol: Muster Station',
                'Safety Poster: CPR Instructions', 'Safety Poster: Donning Life Jacket', 'Hazard Sign: Flammable', 'Hazard Sign: High Voltage',
                'Prohibition Sign: No Smoking', 'Mandatory Sign: Wear PPE'
            ],
            'Spill Containment' => [
                'Oil Spill Kit 120L', 'Oil Absorbent Boom 3m', 'Oil Absorbent Pads (Pack of 100)', 'Spill Containment Pallet 4 Drum',
                'Disposal Bags (Heavy Duty)', 'Sawdust Bag 10kg', 'Oil Dispersant 20L', 'Emergency Plug Kit',
                'Drain Seal Cover', 'Shovel Non-Sparking'
            ],
            'Personal Protective Equipment' => [
                'Safety Helmet (White)', 'Safety Shoes S3 (Size 42)', 'Safety Shoes S3 (Size 44)', 'Coverall Cotton (Orange, L)',
                'Coverall Fire Retardant (Red, XL)', 'Safety Goggles (Anti-Fog)', 'Ear Muffs (30dB)', 'Welding Gloves (Leather)',
                'Chemical Resistant Gloves', 'Safety Harness Full Body'
            ],
            'Anchoring & Mooring' => [
                'Polypropylene Rope 20mm (220m Coil)', 'Nylon Rope 16mm (220m Coil)', 'Anchor Chain 12mm (Grade U2)', 'Anchor Shackle 25mm',
                'Mooring Shackle 32mm', 'D-Shackle 10mm (SS316)', 'Bow Shackle 12mm (Galvanized)', 'Wire Rope 10mm (6x36)',
                'Fender (Cylindrical 100x30)', 'Dock Cleat 10 inch'
            ]
        ];

        foreach ($categories as $catName => $products) {
            $category = InventoryCategory::firstOrCreate(
                ['name' => $catName],
                [
                    'slug' => Str::slug($catName),
                    'description' => "Marine Safety: $catName",
                    'created_by' => $userId
                ]
            );

            foreach ($products as $prodName) {
                // Determine Unit - STRICTLY matching ['Nos', 'Kg', 'Set', 'Ltr', 'Mtr']
                $unit = 'Nos';
                $lowerName = strtolower($prodName);

                if (str_contains($lowerName, 'rope') || str_contains($lowerName, 'hose') || str_contains($lowerName, 'chain')) {
                    // Ropes/Hoses usually 'Mtr' or 'Nos' (if coil).
                    // Given previous error, 'Coil' failed.
                    // If the item name says '220m Coil', it's likely sold as 1 number (1 coil).
                    // If it says 'per meter', then 'Mtr'.
                    // Let's us 'Nos' for Coils and 'Mtr' if explicitly metered.
                    // But 'Polypropylene Rope... (220m Coil)' suggests a unit package.
                    $unit = 'Nos'; 
                } elseif (str_contains($lowerName, 'liquid') || str_contains($lowerName, 'oil') || str_contains($lowerName, 'dispersant') || str_contains($lowerName, 'solution')) {
                    $unit = 'Ltr';
                } elseif (str_contains($lowerName, 'kit') || str_contains($lowerName, 'set') || str_contains($lowerName, 'apparatus')) {
                    $unit = 'Set';
                } elseif (str_contains($lowerName, 'kg')) {
                     // e.g. "Sawdust Bag 10kg" - likely sold as 1 Bag (Nos) or by Kg?
                     // Usually 'Nos' if it's a bag.
                     $unit = 'Nos';
                }
                
                // Fallback ensure valid enum
                if (!in_array($unit, ['Nos', 'Kg', 'Set', 'Ltr', 'Mtr'])) {
                    $unit = 'Nos';
                }

                InventoryItem::firstOrCreate(
                    [
                        'item_name' => $prodName,
                        'inventory_category_id' => $category->id
                    ],
                    [
                        'brand' => 'Generic Marine',
                        'model' => 'Standard',
                        'unit' => $unit,
                        'purchase_price' => rand(50, 5000),
                        'selling_price' => rand(100, 8000),
                        'minimum_stock_level' => 5,
                        'reorder_quantity' => 10,
                        'current_stock' => rand(0, 50),
                        'status' => 'active',
                        'created_by' => $userId
                    ]
                );
            }
        }
    }
}
