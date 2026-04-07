<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\Sample;
use App\Models\Payment;
use App\Models\TestResult;
use App\Models\TestCategory;
use App\Models\LabTest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FreshStartSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🗑️  Clearing demo/transactional data (keeping users)...');

        // Disable FK checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        TestResult::truncate();
        Sample::truncate();
        Payment::truncate();
        LabRequestItem::truncate();
        LabRequest::truncate();
        Patient::truncate();

        // Clear demo tests & categories to re-seed properly
        DB::table('test_materials')->truncate();
        LabTest::truncate();
        TestCategory::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('✅ Cleared. Seeding real test categories and lab tests...');

        // ─── Test Categories ───────────────────────────────────────────────
        $categories = [
            'Hematology'        => [
                ['name' => 'Complete Blood Count (CBC)',  'min' => 4.0,  'max' => 11.0,  'unit' => '×10³/μL', 'price' => 50],
                ['name' => 'Hemoglobin (HGB)',           'min' => 12.0, 'max' => 17.5,  'unit' => 'g/dL',    'price' => 30],
                ['name' => 'Hematocrit (HCT)',           'min' => 36.0, 'max' => 52.0,  'unit' => '%',       'price' => 25],
                ['name' => 'Platelets (PLT)',            'min' => 150,  'max' => 400,   'unit' => '×10³/μL', 'price' => 30],
                ['name' => 'Blood Group & Rh',          'min' => 0,    'max' => 0,     'unit' => '-',       'price' => 20],
                ['name' => 'ESR',                        'min' => 0,    'max' => 20,    'unit' => 'mm/hr',   'price' => 20],
            ],
            'Clinical Chemistry' => [
                ['name' => 'Fasting Blood Sugar (FBS)',  'min' => 70,   'max' => 100,   'unit' => 'mg/dL',   'price' => 30],
                ['name' => 'Random Blood Sugar (RBS)',   'min' => 70,   'max' => 140,   'unit' => 'mg/dL',   'price' => 25],
                ['name' => 'HbA1c',                      'min' => 4.0,  'max' => 5.7,   'unit' => '%',       'price' => 80],
                ['name' => 'Creatinine',                 'min' => 0.6,  'max' => 1.2,   'unit' => 'mg/dL',   'price' => 30],
                ['name' => 'Blood Urea Nitrogen (BUN)', 'min' => 7,    'max' => 20,    'unit' => 'mg/dL',   'price' => 30],
                ['name' => 'Uric Acid',                  'min' => 3.5,  'max' => 7.2,   'unit' => 'mg/dL',   'price' => 35],
                ['name' => 'ALT (SGPT)',                 'min' => 7,    'max' => 56,    'unit' => 'U/L',     'price' => 40],
                ['name' => 'AST (SGOT)',                 'min' => 10,   'max' => 40,    'unit' => 'U/L',     'price' => 40],
                ['name' => 'Alkaline Phosphatase (ALP)', 'min' => 44,  'max' => 147,   'unit' => 'U/L',     'price' => 40],
                ['name' => 'Total Bilirubin',            'min' => 0.2,  'max' => 1.2,   'unit' => 'mg/dL',   'price' => 35],
                ['name' => 'Total Protein',              'min' => 6.0,  'max' => 8.3,   'unit' => 'g/dL',    'price' => 30],
                ['name' => 'Albumin',                    'min' => 3.5,  'max' => 5.0,   'unit' => 'g/dL',    'price' => 30],
                ['name' => 'Cholesterol Total',          'min' => 0,    'max' => 200,   'unit' => 'mg/dL',   'price' => 40],
                ['name' => 'Triglycerides',              'min' => 0,    'max' => 150,   'unit' => 'mg/dL',   'price' => 40],
                ['name' => 'HDL Cholesterol',            'min' => 40,   'max' => 60,    'unit' => 'mg/dL',   'price' => 40],
                ['name' => 'LDL Cholesterol',            'min' => 0,    'max' => 100,   'unit' => 'mg/dL',   'price' => 40],
            ],
            'Thyroid Function'  => [
                ['name' => 'TSH',                        'min' => 0.4,  'max' => 4.0,   'unit' => 'mIU/L',   'price' => 70],
                ['name' => 'Free T3 (FT3)',              'min' => 2.3,  'max' => 4.2,   'unit' => 'pg/mL',   'price' => 70],
                ['name' => 'Free T4 (FT4)',              'min' => 0.8,  'max' => 1.8,   'unit' => 'ng/dL',   'price' => 70],
            ],
            'Urine Analysis'    => [
                ['name' => 'Urine Complete Analysis',    'min' => 0,    'max' => 0,     'unit' => '-',       'price' => 25],
                ['name' => 'Urine Microalbumin',         'min' => 0,    'max' => 30,    'unit' => 'mg/g',    'price' => 60],
                ['name' => 'Urine Culture',              'min' => 0,    'max' => 0,     'unit' => 'CFU/mL',  'price' => 60],
            ],
            'Microbiology'      => [
                ['name' => 'Stool Analysis',             'min' => 0,    'max' => 0,     'unit' => '-',       'price' => 30],
                ['name' => 'Widal Test',                 'min' => 0,    'max' => 0,     'unit' => 'titer',   'price' => 40],
                ['name' => 'Brucella Agglutination',     'min' => 0,    'max' => 0,     'unit' => 'titer',   'price' => 50],
                ['name' => 'Malaria Parasite',           'min' => 0,    'max' => 0,     'unit' => '-',       'price' => 30],
            ],
            'Serology'          => [
                ['name' => 'HBsAg (Hepatitis B)',        'min' => 0,    'max' => 0,     'unit' => '-',       'price' => 50],
                ['name' => 'Anti-HCV (Hepatitis C)',     'min' => 0,    'max' => 0,     'unit' => '-',       'price' => 50],
                ['name' => 'HIV 1&2 Ab/Ag',              'min' => 0,    'max' => 0,     'unit' => '-',       'price' => 60],
                ['name' => 'VDRL (Syphilis)',             'min' => 0,    'max' => 0,     'unit' => '-',       'price' => 40],
                ['name' => 'CRP (C-Reactive Protein)',   'min' => 0,    'max' => 10,    'unit' => 'mg/L',    'price' => 45],
                ['name' => 'Rheumatoid Factor (RF)',     'min' => 0,    'max' => 14,    'unit' => 'IU/mL',   'price' => 45],
            ],
            'Hormones'          => [
                ['name' => 'PSA (Prostate)',              'min' => 0,    'max' => 4.0,   'unit' => 'ng/mL',   'price' => 100],
                ['name' => 'Beta-HCG (Pregnancy)',       'min' => 0,    'max' => 5,     'unit' => 'mIU/mL',  'price' => 60],
                ['name' => 'Testosterone',               'min' => 270,  'max' => 1070,  'unit' => 'ng/dL',   'price' => 100],
                ['name' => 'Prolactin',                  'min' => 2,    'max' => 18,    'unit' => 'ng/mL',   'price' => 90],
                ['name' => 'FSH',                        'min' => 1.5,  'max' => 12.4,  'unit' => 'mIU/mL',  'price' => 90],
                ['name' => 'LH',                         'min' => 1.7,  'max' => 8.6,   'unit' => 'mIU/mL',  'price' => 90],
                ['name' => 'Estradiol (E2)',              'min' => 15,   'max' => 350,   'unit' => 'pg/mL',   'price' => 100],
            ],
            'Vitamins & Minerals' => [
                ['name' => 'Vitamin D (25-OH)',          'min' => 30,   'max' => 100,   'unit' => 'ng/mL',   'price' => 120],
                ['name' => 'Vitamin B12',                'min' => 200,  'max' => 900,   'unit' => 'pg/mL',   'price' => 100],
                ['name' => 'Ferritin',                   'min' => 12,   'max' => 300,   'unit' => 'ng/mL',   'price' => 80],
                ['name' => 'Serum Iron',                 'min' => 60,   'max' => 170,   'unit' => 'μg/dL',   'price' => 40],
                ['name' => 'Calcium',                    'min' => 8.5,  'max' => 10.5,  'unit' => 'mg/dL',   'price' => 30],
                ['name' => 'Magnesium',                  'min' => 1.7,  'max' => 2.3,   'unit' => 'mg/dL',   'price' => 35],
            ],
        ];

        $count = 0;
        foreach ($categories as $catName => $tests) {
            $category = TestCategory::create(['name' => $catName]);
            foreach ($tests as $testData) {
                LabTest::create([
                    'name'        => $testData['name'],
                    'category_id' => $category->id,
                    'normal_min'  => $testData['min'],
                    'normal_max'  => $testData['max'],
                    'unit'        => $testData['unit'],
                    'price'       => $testData['price'],
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Seeded {$count} real lab tests across " . count($categories) . " categories.");

        // ─── Specimen Types ───────────────────────────────────────────────
        $this->command->info('🩸 Seeding common specimen types...');
        $specimenTypes = [
            'Blood',
            'Serum',
            'Plasma',
            'Urine',
            'Stool',
            'Sputum',
            'Swab',
            'CSF',
            'Synovial Fluid',
            'Pleural Fluid',
            'Peritoneal Fluid',
            'Amniotic Fluid',
        ];

        foreach ($specimenTypes as $type) {
            \App\Models\SpecimenType::create(['name' => $type]);
        }

        $this->command->info("✅ Seeded " . count($specimenTypes) . " specimen types.");

        $this->command->info('🎉 System is clean and ready. Users are untouched.');
        $this->command->info('   Login: admin@lis.com / 123456');
    }
}
