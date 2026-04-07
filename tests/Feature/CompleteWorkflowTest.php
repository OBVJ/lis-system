<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\LabTest;
use App\Models\LabRequest;
use App\Models\Sample;
use App\Models\TestResult;

class CompleteWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions and roles
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        
        // Create specimen types
        $specimenTypes = ['Blood', 'Urine', 'Stool', 'Sputum', 'Swab', 'CSF', 'Pleural Fluid'];
        foreach ($specimenTypes as $type) {
            \App\Models\SpecimenType::create(['name' => $type]);
        }
        
        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        // Create regular user (technician) with proper permissions
        $this->user = User::factory()->create();
        $this->user->assignRole('admin'); // For testing purposes - gives all permissions
    }

    /** @test */
    public function complete_workflow_from_registration_to_results()
    {
        // Step 1: Create test data
        $test1 = LabTest::factory()->create(['name' => 'CBC', 'price' => 50]);
        $test2 = LabTest::factory()->create(['name' => 'Glucose', 'price' => 30]);

        // Step 2: Register patient with tests and payment (Receptionist workflow)
        $patientData = [
            'name' => 'Ahmed Omar',
            'age' => 30,
            'gender' => 'male',
            'phone' => '0532451344',
            'patient_type' => 'in_patient',
            'address' => 'Test Address',
            'treating_doctor' => 'Dr. Mohammed',
            'referring_doctor' => 'Clinic name or doctor',
            'test_ids' => [$test1->id, $test2->id],
            'priority' => 'normal',
            'notes' => 'Routine checkup',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'paid_amount' => 70, // Partial payment
        ];

        $response = $this->actingAs($this->admin)->withoutMiddleware()
                        ->post(route('patients.store'), $patientData);

        $response->assertRedirect();
        
        // Verify patient was created
        $this->assertDatabaseHas('patients', [
            'name' => 'Ahmed Omar',
            'age' => 30,
            'gender' => 'male',
            'phone' => '0532451344',
        ]);

        $patient = Patient::where('name', 'Ahmed Omar')->first();
        $this->assertNotNull($patient);

        // Verify lab request was created
        $request = LabRequest::where('patient_id', $patient->id)->first();
        $this->assertNotNull($request);
        $this->assertEquals('pending', $request->status);
        $this->assertEquals('normal', $request->priority);

        // Verify request items
        $this->assertDatabaseHas('lab_request_items', [
            'request_id' => $request->id,
            'test_id' => $test1->id,
        ]);
        $this->assertDatabaseHas('lab_request_items', [
            'request_id' => $request->id,
            'test_id' => $test2->id,
        ]);

        // Step 3: Collect sample (Lab technician workflow)
        $sampleData = [
            'request_id' => $request->id,
            'sample_type' => 'Blood', // Assuming this exists in specimen_types
        ];

        $response = $this->actingAs($this->user)->withoutMiddleware()
                        ->post(route('samples.store'), $sampleData);

        $response->assertRedirect();

        // Verify sample was created and request status updated
        $this->assertDatabaseHas('samples', [
            'request_id' => $request->id,
            'sample_type' => 'Blood',
        ]);

        $request->refresh();
        $this->assertEquals('sample_collected', $request->status);

        // Step 4: Enter test results (Lab technician workflow)
        $sample = Sample::where('request_id', $request->id)->first();
        
        // Get request items
        $requestItems = $request->items;
        
        foreach ($requestItems as $item) {
            $resultData = [
                'request_item_id' => $item->id,
                'result_value' => rand(10, 100), // Random result within normal range
                'flag' => 'Normal',
            ];

            $response = $this->actingAs($this->user)->withoutMiddleware()
                            ->post(route('results.store'), $resultData);

            $response->assertRedirect();
        }

        // Verify results were entered
        foreach ($requestItems as $item) {
            $this->assertDatabaseHas('test_results', [
                'request_item_id' => $item->id,
            ]);
        }

        // Check if request status is updated to review
        $request->refresh();
        $this->assertEquals('review', $request->status);

        // Step 4.5: Approve results (move from review to completed)
        $response = $this->actingAs($this->user)->withoutMiddleware()
                        ->patch(route('requests.update-status', $request->id), [
                            'status' => 'completed'
                        ]);

        $response->assertRedirect();

        $request->refresh();
        $this->assertEquals('completed', $request->status);

        // Step 5: Print report (should be available now)
        $response = $this->actingAs($this->user)->withoutMiddleware()
                        ->get(route('reports.pdf', $request->id));

        $response->assertStatus(200);

        // Step 6: Mark as printed/delivered
        $response = $this->actingAs($this->user)->withoutMiddleware()
                        ->patch(route('requests.update-status', $request->id), [
                            'status' => 'printed_delivered'
                        ]);

        $response->assertRedirect();

        $request->refresh();
        $this->assertEquals('printed_delivered', $request->status);

        // Final verification: Complete workflow successful
        $this->assertEquals('Ahmed Omar', $patient->name);
        $this->assertEquals('printed_delivered', $request->status);
        $this->assertCount(2, $request->items); // Two tests
        $this->assertCount(2, TestResult::whereIn('request_item_id', $request->items->pluck('id'))->get()); // Two results
    }

    /** @test */
    public function patient_search_functionality_works()
    {
        // Create test patients
        $patient1 = Patient::factory()->create([
            'name' => 'Fatima Hassan',
            'patient_code' => 'PT-2026-0001',
            'phone' => '0912345678',
        ]);
        
        $patient2 = Patient::factory()->create([
            'name' => 'Omar Khalid',
            'patient_code' => 'PT-2026-0002',
            'phone' => '0998765432',
        ]);

        // Test search by name
        $response = $this->actingAs($this->admin)->withoutMiddleware()
                        ->get(route('patients.ajaxSearch', ['q' => 'Fatima']));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('Fatima Hassan', $data[0]['name']);

        // Test search by phone
        $response = $this->actingAs($this->admin)->withoutMiddleware()
                        ->get(route('patients.ajaxSearch', ['q' => '091234']));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('0912345678', $data[0]['phone']);
    }
}
