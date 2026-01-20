<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScopeCategory;
use App\Models\ScopeItem;

class ScopeOfWorkSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Fire Extinguishers' => [
                'CO2 Extinguisher' => 'Various capacities',
                'DCP Extinguisher' => 'Dry Chemical Powder',
                'Foam Extinguisher' => 'AFFF',
                'Water Extinguisher' => 'Water mist/jet',
                'Clean Agent' => 'FM200 / Novec',
                'Wet Chemical' => 'Kitchen hood systems'
            ],
            'Breathing Apparatus (SCBA)' => [
                'SCBA Set (30 min)' => 'Self Contained Breathing Apparatus',
                'SCBA Set (45 min)' => 'Self Contained Breathing Apparatus',
                'SCBA Set (60 min)' => 'Self Contained Breathing Apparatus',
                'Spare Cylinder' => 'Spare air cylinder',
                'Face Mask' => 'Full face mask inspection'
            ],
            'EEBD' => [
                'EEBD (10 min)' => 'Emergency Escape Breathing Device',
                'EEBD (15 min)' => 'Emergency Escape Breathing Device',
            ],
            'Life Rafts' => [
                'Life Raft (6 Persons)' => 'Inflatable',
                'Life Raft (10 Persons)' => 'Inflatable',
                'Life Raft (15 Persons)' => 'Inflatable',
                'Life Raft (20 Persons)' => 'Inflatable',
                'Life Raft (25 Persons)' => 'Inflatable',
                'HRU' => 'Hydrostatic Release Unit'
            ],
            'CO2 Fixed System' => [
                'Main Cylinder Bank' => 'Level check / weighing',
                'Pilot Cylinder' => 'Activation check',
                'Distribution Lines' => 'Blow through test',
                'Nozzles' => 'Visual inspection',
                'Control Panel' => 'Function test'
            ],
            'Fire Detection System' => [
                'Smoke Detector' => 'Function test',
                'Heat Detector' => 'Function test',
                'Flame Detector' => 'Function test',
                'Manual Call Point' => 'Activation test',
                'Control Panel' => 'Loop test'
            ],
            'Gas Detection System' => [
                'Portable Gas Detector' => 'Calibration',
                'Fixed Gas Detector' => 'Sensor check',
                'Sampling Lines' => 'Leak test'
            ],
            'Lifeboat / Rescue Boat' => [
                'Lifeboat Engine' => 'Start test',
                'Steering Gear' => 'Function test',
                'Sprinkler System' => 'Water spray test',
                'Air Support System' => 'Pressure check',
                'Release Gear' => 'On-load / Off-load test'
            ]
        ];

        $order = 0;
        foreach ($categories as $catName => $items) {
            $category = ScopeCategory::firstOrCreate(
                ['slug' => \Str::slug($catName)],
                ['name' => $catName, 'order' => ++$order]
            );

            foreach ($items as $itemName => $desc) {
                ScopeItem::firstOrCreate(
                    ['category_id' => $category->id, 'name' => $itemName],
                    ['description' => $desc, 'unit' => 'Nos', 'is_active' => true]
                );
            }
        }
    }
}
