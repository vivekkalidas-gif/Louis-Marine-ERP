<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkshopTool;

class WorkshopToolSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            [
                'code' => 'TL-0001',
                'name' => 'Digital Torque Wrench 1/2"',
                'type' => 'Calibration Tool',
                'brand' => 'Snap-on',
                'model' => 'ATECH3FR250B',
                'unit' => 'pcs',
                'description' => 'Calibrated digital torque wrench for high-precision marine fittings.',
                'is_active' => true,
            ],
            [
                'code' => 'TL-0002',
                'name' => 'Fluke True-RMS Multimeter',
                'type' => 'Electrical Tester',
                'brand' => 'Fluke',
                'model' => 'Fluke 87V',
                'unit' => 'pcs',
                'description' => 'Industrial multimeter for electrical system diagnostics.',
                'is_active' => true,
            ],
            [
                'code' => 'TL-0003',
                'name' => 'Portable 4-Gas Detector',
                'type' => 'Gas Detector',
                'brand' => 'BW Technologies',
                'model' => 'MicroClip XL',
                'unit' => 'pcs',
                'description' => '4-gas detection monitor for O2, LEL, H2S, and CO.',
                'is_active' => true,
            ],
            [
                'code' => 'TL-0004',
                'name' => 'Hydraulic Pressure Gauge 0-600 Bar',
                'type' => 'Pressure Tester',
                'brand' => 'WIKA',
                'model' => '232.50',
                'unit' => 'pcs',
                'description' => 'High pressure gauge for CO2 system hydrostatic testing.',
                'is_active' => true,
            ],
            [
                'code' => 'TL-0005',
                'name' => 'Heavy Duty Spanner Set (8-32mm)',
                'type' => 'Hand Tool',
                'brand' => 'Bahco',
                'model' => '111M/26T',
                'unit' => 'set',
                'description' => 'Metric combination spanner set.',
                'is_active' => true,
            ]
        ];

        foreach ($tools as $tool) {
            WorkshopTool::updateOrCreate(
                ['code' => $tool['code']],
                $tool
            );
        }
    }
}
