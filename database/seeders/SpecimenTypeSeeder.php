<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SpecimenType;
use App\Models\VehicleRequirement;
use App\Models\TemperatureRequirement;

class SpecimenTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Added Specimen types
        $types = [
            'Blood',
            'Urine',
            'Saliva',
            'Stool',
            'Sputum',
            'Tissue',
            'Plasma',
            'Serum',
            'Swab',
            'Cerebrospinal Fluid (CSF)',
        ];

        foreach ($types as $type) {
            SpecimenType::firstOrCreate(
                ['name' => $type],
                [
                    'status' => true,
                ]
            );
        }

        //Added temprature types
        $types2 = [
            'Ambient',
            'Refrigerated',
            'Frozen',
            'Dry Ice'
        ];

        foreach ($types2 as $type) {
            TemperatureRequirement::firstOrCreate(
                ['name' => $type],
                [
                    'status' => true,
                ]
            );
        }

        //Vehicle Requirements
        $types3 = [
            'Refrigerated Van',
            'Temperature Controlled',
            'Hazmat Equipped',
        ];

        foreach ($types3 as $type) {
            VehicleRequirement::firstOrCreate(
                ['name' => $type],
                [
                    'status' => true,
                ]
            );
        }
    }
}
