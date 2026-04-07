<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use App\Models\TestCategory;
use App\Models\LabTest;
use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\Sample;
use App\Models\Payment;
use App\Models\TestResult;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Setup Permissions and Roles
        $this->call(PermissionsSeeder::class);

        // Create all roles before seeding users
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $doctorRole->syncPermissions(['view_dashboard', 'manage_patients', 'manage_requests', 'manage_results', 'view_reports']);

        $receptionistRole = Role::firstOrCreate(['name' => 'receptionist']);
        $receptionistRole->syncPermissions(['view_dashboard', 'manage_patients', 'manage_requests', 'view_reports']);

        $technicianRole = Role::firstOrCreate(['name' => 'technician']);
        $technicianRole->syncPermissions(['view_dashboard', 'manage_requests', 'manage_samples', 'manage_results', 'view_reports']);

        $this->call(UserSeeder::class);

        // 2. Specimen Types
        $specimenTypes = ['Blood', 'Urine', 'Stool', 'Sputum', 'Swab', 'CSF', 'Pleural Fluid'];
        foreach ($specimenTypes as $type) {
            \App\Models\SpecimenType::firstOrCreate(['name' => $type]);
        }

        // 3. Departments & Doctors for referral
        $dept1 = Department::firstOrCreate(['name' => 'Internal Medicine']);
        $dept2 = Department::firstOrCreate(['name' => 'Cardiology']);
        
        $doc1 = Doctor::firstOrCreate(['name' => 'Dr. Alice', 'department_id' => $dept1->id]);
        $doc2 = Doctor::firstOrCreate(['name' => 'Dr. Bob', 'department_id' => $dept2->id]);
        $doc3 = Doctor::firstOrCreate(['name' => 'Dr. John Doe', 'department_id' => $dept1->id]);

        $doctorIds = [$doc1->id, $doc2->id, $doc3->id];

        // 3. Test Categories & Tests
        $categories = [
            'Hematology' => ['CBC', 'Hemoglobin', 'Blood Group', 'Platelets'],
            'Chemistry' => ['Glucose Fasting', 'HbA1c', 'Creatinine', 'Urea', 'ALT', 'AST'],
            'Microbiology' => ['Urine Culture', 'Stool Analysis', 'Sputum Test'],
        ];

        foreach ($categories as $catName => $tests) {
            $category = TestCategory::firstOrCreate(['name' => $catName]);
            foreach ($tests as $testName) {
                LabTest::firstOrCreate(['name' => $testName], [
                    'category_id' => $category->id,
                    'normal_min' => rand(10, 50),
                    'normal_max' => rand(60, 150),
                    'unit' => $catName == 'Hematology' ? 'g/dL' : 'mg/dL',
                    'price' => rand(20, 100),
                ]);
            }
        }

        // 4. Create 20 Patients
        if (Patient::count() < 20) {
            $genders = ['male', 'female'];
            $types = ['in_patient', 'out_patient'];
            $doctorNames = ['Dr. Alice', 'Dr. Bob', 'Dr. John Doe'];
            for ($i = 1; $i <= 20; $i++) {
                Patient::create([
                    'patient_code' => 'PT-2026-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'name' => 'Patient Demo ' . $i,
                    'age' => rand(18, 85),
                    'gender' => $genders[array_rand($genders)],
                    'patient_type' => $types[array_rand($types)],
                    'treating_doctor' => $doctorNames[array_rand($doctorNames)],
                    'referring_doctor' => $doctorNames[array_rand($doctorNames)],
                    'phone' => '09' . rand(10000000, 99999999),
                    'address' => 'Demo Street ' . $i,
                ]);
            }
        }

        // 5. Create 50 Lab Requests
        if (LabRequest::count() < 50) {
            $patients = Patient::all();
            $tests = LabTest::all();
            $statuses = ['pending', 'sample_collected', 'in_progress', 'completed', 'delivered'];

            for ($i = 1; $i <= 50; $i++) {
                $patient = $patients->random();
                $testCount = rand(1, 4);
                $selectedTests = $tests->random($testCount);
                $totalPrice = $selectedTests->sum('price');
                
                // Bias towards completed/delivered to have rich analytics
                $rand = rand(1, 100);
                if($rand <= 10) $status = 'pending';
                elseif($rand <= 20) $status = 'sample_collected';
                elseif($rand <= 30) $status = 'in_progress';
                elseif($rand <= 80) $status = 'completed';
                else $status = 'delivered';

                $createdAt = Carbon::now()->subDays(rand(0, 45)); // data for last 1.5 months

                $request = LabRequest::create([
                    'patient_id' => $patient->id,
                    'status' => $status,
                    'total_price' => $totalPrice,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                foreach ($selectedTests as $test) {
                    $itemStatus = in_array($status, ['completed', 'delivered']) ? 'completed' : 'pending';
                    $item = LabRequestItem::create([
                        'request_id' => $request->id,
                        'test_id' => $test->id,
                        'status' => $itemStatus,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    if ($itemStatus == 'completed') {
                        // Realistic normal/high/low distribution
                        // 70% normal, 15% high, 15% low
                        $resultVal = rand($test->normal_min - 20, $test->normal_max + 20);
                        if ($resultVal > $test->normal_max) $flag = 'High';
                        elseif ($resultVal < $test->normal_min) $flag = 'Low';
                        else $flag = 'Normal';

                        TestResult::create([
                            'request_item_id' => $item->id,
                            'result_value' => $resultVal,
                            'flag' => $flag,
                            'created_at' => $createdAt->copy()->addHours(rand(1, 5)),
                            'updated_at' => $createdAt->copy()->addHours(rand(1, 5)),
                        ]);
                    }
                }

                if ($status != 'pending') {
                    Sample::create([
                        'request_id' => $request->id,
                        'sample_type' => collect(['blood', 'urine', 'swab'])->random(),
                        'collected_at' => $createdAt->copy()->addMinutes(15),
                        'status' => 'collected',
                    ]);
                }

                if (in_array($status, ['completed', 'delivered'])) {
                    Payment::create([
                        'request_id' => $request->id,
                        'amount' => $totalPrice,
                        'status' => 'paid',
                        'paid_at' => $createdAt->copy()->addMinutes(25),
                    ]);
                }
            }
        }
    }
}
