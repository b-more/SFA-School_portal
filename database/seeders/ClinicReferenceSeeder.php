<?php

namespace Database\Seeders;

use App\Models\ClinicComplaint;
use App\Models\MedicalStockItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClinicReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $complaints = [
            'Headache', 'Stomach ache', 'Stomach cramps', 'Diarrhoea', 'Fever',
            'Cough', 'Fatigue', 'Chest pain', 'Vomiting', 'Toothache',
            'Sore throat', 'Painful eyes', 'Painful hand', 'Bruises / wound',
            'Post-malaria', 'Painful heel',
        ];
        foreach ($complaints as $name) {
            ClinicComplaint::firstOrCreate(['slug' => Str::slug($name)], [
                'name' => $name, 'is_active' => true,
            ]);
        }

        $items = [
            ['Panadol',                   'tablet',      'tablets', 30],
            ['Panadol Syrup',             'syrup',       'ml',      500],
            ['Brufen',                    'tablet',      'tablets', 30],
            ['Diclofenac',                'tablet',      'tablets', 30],
            ['Polar Gel',                 'gel_ointment','tubes',   3],
            ['Deep Heat rub-on',          'gel_ointment','tubes',   3],
            ['Lozenges',                  'lozenge',     'pieces',  20],
            ['Plasters',                  'first_aid',   'pieces',  50],
            ['Antiseptic (wound clean)',  'first_aid',   'ml',      250],
            ['Cotton wool',               'first_aid',   'rolls',   3],
            ['ORS (oral rehydration)',    'other',       'sachets', 20],
        ];
        foreach ($items as [$name, $category, $unit, $reorder]) {
            MedicalStockItem::firstOrCreate(['name' => $name], [
                'category'      => $category,
                'unit'          => $unit,
                'reorder_level' => $reorder,
                'is_active'     => true,
            ]);
        }
    }
}
