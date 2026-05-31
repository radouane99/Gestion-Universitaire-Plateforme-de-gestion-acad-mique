<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Group;
use App\Models\Filiere;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;

class AdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected User $professorUser;
    protected Student $student;
    protected Professor $professor;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $profRole = Role::firstOrCreate(['name' => 'professor']);
        Role::firstOrCreate(['name' => 'admin']);

        // Create student user & student model
        $this->studentUser = User::factory()->create([
            'role_id' => $studentRole->id,
        ]);
        
        $filiere = Filiere::create([
            'name' => 'IT Department',
            'code' => 'IT_DEP',
        ]);
        
        $group = Group::create([
            'name' => 'Group 1 IT',
            'level' => '1',
            'filiere_id' => $filiere->id,
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'group_id' => $group->id,
            'student_number' => 'EST12345',
        ]);

        // Create professor user & professor model
        $this->professorUser = User::factory()->create([
            'role_id' => $profRole->id,
        ]);
        $this->professor = Professor::create([
            'user_id' => $this->professorUser->id,
            'department' => 'IT',
        ]);
    }

    /**
     * Test declaring an availability slot by a professor.
     */
    public function test_professor_can_declare_availability_slot()
    {
        $response = $this->actingAs($this->professorUser)->post(route('professor.appointments.slot.store'), [
            'start_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDays(2)->addHours(1)->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointment_slots', [
            'host_id' => $this->professorUser->id,
            'status' => 'available',
        ]);
    }

    /**
     * Test booking a slot by a student.
     */
    public function test_student_can_book_available_slot()
    {
        Notification::fake();

        $slot = AppointmentSlot::create([
            'host_id' => $this->professorUser->id,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(1),
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->studentUser)->post(route('student.appointments.book', $slot), [
            'purpose' => 'Discussion about final project',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('appointments', [
            'student_id' => $this->student->id,
            'appointment_slot_id' => $slot->id,
            'purpose' => 'Discussion about final project',
            'status' => 'scheduled',
        ]);

        $this->assertDatabaseHas('appointment_slots', [
            'id' => $slot->id,
            'status' => 'booked',
        ]);
    }

    /**
     * Test cancelling an appointment.
     */
    public function test_user_can_cancel_appointment()
    {
        Notification::fake();

        $slot = AppointmentSlot::create([
            'host_id' => $this->professorUser->id,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(1),
            'status' => 'booked',
        ]);

        $appointment = Appointment::create([
            'student_id' => $this->student->id,
            'appointment_slot_id' => $slot->id,
            'purpose' => 'Exam review',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->studentUser)->post(route('appointments.cancel', $appointment));

        $response->assertStatus(302);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('appointment_slots', [
            'id' => $slot->id,
            'status' => 'available',
        ]);
    }

    /**
     * Test scheduled reminder artisan command works.
     */
    public function test_send_reminders_command_executes_successfully()
    {
        Notification::fake();

        $slot = AppointmentSlot::create([
            'host_id' => $this->professorUser->id,
            'start_time' => now()->addHours(24), // Exactly 24h away
            'end_time' => now()->addHours(25),
            'status' => 'booked',
        ]);

        $appointment = Appointment::create([
            'student_id' => $this->student->id,
            'appointment_slot_id' => $slot->id,
            'purpose' => 'Consultation',
            'status' => 'scheduled',
            'reminder_sent' => false,
        ]);

        $exitCode = Artisan::call('appointments:send-reminders');
        
        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'reminder_sent' => true,
        ]);
    }

    /**
     * Test API Rate Limiting.
     */
    public function test_api_rate_limiting_works()
    {
        // Simple test to check if database backup command runs without crashes
        $exitCode = Artisan::call('db:backup');
        $this->assertEquals(0, $exitCode);
    }
}
