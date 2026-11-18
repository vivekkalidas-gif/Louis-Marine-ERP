<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CertificateType;

class CertificateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Fire Extinguisher Certificate',
                'slug' => 'fire-extinguisher',
                'validity_months' => 12,
                'description' => 'For Portable and Non-Portable Fire Extinguishers.',
            ],
            [
                'name' => 'EEBD Certificate',
                'slug' => 'eebd',
                'validity_months' => 12,
                'description' => 'For Emergency Escape Breathing Devices.',
            ],
            [
                'name' => 'Life Raft Certificate',
                'slug' => 'life-raft',
                'validity_months' => 12,
                'description' => 'For Life Rafts inspection and service.',
            ],
            [
                'name' => 'SCBA Certificate',
                'slug' => 'scba',
                'validity_months' => 12,
                'description' => 'For Self-Contained Breathing Apparatus inspections.',
            ],
            [
                'name' => 'Life Jacket Certificate',
                'slug' => 'life-jacket',
                'validity_months' => 12,
                'description' => 'For Life Jackets and PFD inspection.',
            ],
            [
                'name' => 'Immersion Suit Certificate',
                'slug' => 'immersion-suit',
                'validity_months' => 12,
                'description' => 'For Immersion Suits and Anti-Exposure Suits.',
            ],
            [
                'name' => 'CO₂ Fixed System Certificate',
                'slug' => 'co2-fixed-system',
                'validity_months' => 12,
                'description' => 'For CO2 Total Flooding Systems.',
            ],
            [
                'name' => 'Lifeboat / Rescue Boat Certificate',
                'slug' => 'lifeboat-rescue-boat',
                'validity_months' => 12,
                'description' => 'For Annual or 5-Yearly inspection of Lifeboats/Rescue Boats.',
            ],
            [
                'name' => 'Gas Detection System Certificate',
                'slug' => 'gas-detection-system',
                'validity_months' => 6,
                'description' => 'For fixed or portable gas detection calibration.',
            ],
            [
                'name' => 'Fixed Foam System Certificate',
                'slug' => 'fixed-foam-system',
                'validity_months' => 12,
                'description' => 'For Fixed Foam & Deluge Fire Extinguishing Systems.',
            ],
            [
                'name' => 'Fire Hose & Hydrant Certificate',
                'slug' => 'fire-hose-hydrant',
                'validity_months' => 12,
                'description' => 'For Fire Hoses, Nozzles and Hydrants.',
            ],
            [
                'name' => 'Breathing Air Compressor Certificate',
                'slug' => 'breathing-air-compressor',
                'validity_months' => 6,
                'description' => 'For High Pressure Breathing Air Compressors & Air Quality Test.',
            ],
            [
                'name' => 'Hydrostatic Test Certificate',
                'slug' => 'hydrostatic-test',
                'validity_months' => 60,
                'description' => 'For Pressure Vessel and Cylinder Hydrostatic Pressure Testing.',
            ],
            [
                'name' => 'Safety Harness & Fall Arrest Certificate',
                'slug' => 'safety-harness-fall-arrest',
                'validity_months' => 12,
                'description' => 'For Safety Harnesses, Lanyards and Fall Arrest Systems.',
            ],
            [
                'name' => 'Medical Oxygen Resuscitator Certificate',
                'slug' => 'medical-oxygen-resuscitator',
                'validity_months' => 12,
                'description' => 'For Medical Oxygen Resuscitators and Regulators.',
            ],
            [
                'name' => 'Pyrotechnics & Distress Signals Certificate',
                'slug' => 'pyrotechnics-distress-signals',
                'validity_months' => 12,
                'description' => 'For Marine Pyrotechnics and Distress Signals Inspection.',
            ],
        ];

        foreach ($types as $type) {
            CertificateType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
