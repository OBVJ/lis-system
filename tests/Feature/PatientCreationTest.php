<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\LabTest;
use App\Models\LabRequest;
use App\Models\Payment;
use Database\Seeders\PermissionsSeeder;

class PatientCreationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role if it doesn't exist
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->user->assignRole('admin'); // Give admin role for permissions
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_create_a_new_patient_with_tests_and_payment()
    {
        // Create some test data
        $test1 = LabTest::factory()->create(['name' => 'CBC', 'price' => 50]);
        $test2 = LabTest::factory()->create(['name' => 'Glucose', 'price' => 30]);

        $patientData = [
            'name' => 'John Doe',
            'age' => 30,
            'gender' => 'male',
            'phone' => '0912345678',
            'address' => '123 Main St',
            'patient_type' => 'out_patient',
            'test_ids' => [$test1->id, $test2->id],
            'priority' => 'normal',
            'notes' => 'Routine checkup',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'paid_amount' => 70, // Total should be 80 - 8 = 72, paid 70 = partial
        ];

        $response = $this->withoutMiddleware()->post(route('patients.store'), $patientData);

        $response->assertRedirect();
        
        // Check that patient was created
        $this->assertDatabaseHas('patients', [
            'name' => 'John Doe',
            'age' => 30,
            'gender' => 'male',
            'phone' => '0912345678',
        ]);

        $patient = Patient::where('name', 'John Doe')->first();
        $this->assertNotNull($patient);

        // Check that lab request was created
        $this->assertDatabaseHas('lab_requests', [
            'patient_id' => $patient->id,
            'priority' => 'normal',
            'notes' => 'Routine checkup',
        ]);

        $request = LabRequest::where('patient_id', $patient->id)->first();
        $this->assertNotNull($request);

        // Check that request items were created
        $this->assertDatabaseHas('lab_request_items', [
            'request_id' => $request->id,
            'test_id' => $test1->id,
        ]);
        $this->assertDatabaseHas('lab_request_items', [
            'request_id' => $request->id,
            'test_id' => $test2->id,
        ]);

        // Check payment calculation
        $this->assertDatabaseHas('payments', [
            'request_id' => $request->id,
            'amount' => 72, // Final total after discount
            'paid_amount' => 70,
            'status' => 'partial',
            'discount_type' => 'percentage',
            'discount_value' => 8, // 10% of 80 = 8
        ]);
    }

    /** @test */
    public function it_can_search_existing_patients()
    {
        // Create test patients
        Patient::factory()->create([
            'name' => 'Alice Johnson',
            'patient_code' => 'PT-001',
            'phone' => '0911111111',
        ]);
        Patient::factory()->create([
            'name' => 'Bob Smith',
            'patient_code' => 'PT-002',
            'phone' => '0922222222',
        ]);

        // Test search by name
        $response = $this->withoutMiddleware()->get(route('patients.ajaxSearch', ['q' => 'Alice']));
        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('Alice Johnson', $data[0]['name']);

        // Test search by phone
        $response = $this->withoutMiddleware()->get(route('patients.ajaxSearch', ['q' => '091111']));
        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('0911111111', $data[0]['phone']);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->withoutMiddleware()->post(route('patients.store'), []);

        $response->assertSessionHasErrors(['name', 'age', 'gender']);
    }
}
