<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipmentItem;
use App\Models\EquipmentCategory;
use App\Models\User;
use Carbon\Carbon;

class EngineerEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Category exists
        $category = EquipmentCategory::firstOrCreate(
            ['slug' => 'calibration-tools'],
            [
                'name' => 'Calibration Tools',
                'description' => 'Tools used for verifying and calibrating equipment'
            ]
        );

        // 2. Ensure Engineer exists (using the one we just fixed)
        $engineer = User::where('email', 'engineer1@louismarine.qa')->first();
        $engineerId = $engineer ? $engineer->id : User::first()->id;

        // 3. Create Demo Items
        $items = [
            [
                'name' => 'Fluke 87V True RMS Multimeter',
                'code' => 'FL-87V-10293',
                'brand' => 'Fluke',
                'model' => '87V',
                'description' => 'Industrial Multimeter for field use.',
                'calibration_required' => true,
                'calibration_expiry_date' => Carbon::now()->addMonths(6), // Valid (Green)
                'status' => 'valid', // Assuming observer/accessor handles this, but setting mainly via date
            ],
            [
                'name' => 'BW GasAlert MicroClip XL',
                'code' => 'BW-MCXL-5541',
                'brand' => 'Honeywell',
                'model' => 'MicroClip XL',
                'description' => '4-Gas Detector (H2S, CO, O2, LEL).',
                'calibration_required' => true,
                'calibration_expiry_date' => Carbon::now()->addDays(15), // Expiring Soon (Orange)
                'status' => 'expiring_soon',
            ],
            [
                'name' => 'DPI 610 Portable Pressure Calibrator',
                'code' => 'DPI-610-9921',
                'brand' => 'Druck',
                'model' => 'DPI 610',
                'description' => 'Portable pressure calibrator 20 bar.',
                'calibration_required' => true,
                'calibration_expiry_date' => Carbon::now()->subDays(5), // Expired (Red)
                'status' => 'expired',
            ],
             [
                'name' => 'Generic Tool Kit',
                'code' => 'GTK-001',
                'brand' => 'Stanley',
                'model' => 'Pro',
                'description' => 'Standard mechanical tool kit.',
                'calibration_required' => false,
                'calibration_expiry_date' => null,
                'status' => 'not_required',
            ]
        ];

        foreach ($items as $itemData) {
            // Remove 'status' key if it's computed accessor, but Model::create might ignore it if not fillable.
            // Explicitly handling fillable fields.
            EquipmentItem::create([
                'category_id' => $category->id,
                'assigned_engineer_id' => $engineerId,
                'name' => $itemData['name'],
                'code' => $itemData['code'],
                'brand' => $itemData['brand'],
                'model' => $itemData['model'],
                'description' => $itemData['description'],
                'calibration_required' => $itemData['calibration_required'],
                'calibration_expiry_date' => $itemData['calibration_expiry_date'],
                'is_active' => true,
            ]);
        }
    }
}
