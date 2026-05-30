<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Professor;
use App\Models\Group;
use App\Models\Module;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\AcademicYear;
use App\Models\ConfirmedSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ProfessorHoursTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'professor']);
        
        // Create an Academic Year
        AcademicYear::create([
            'name' => '2025/2026',
            'is_current' => true,
        ]);
    }

    public function test_absence_submission_creates_confirmed_session_automatically()
    {
        // 1. Create a Professor user
        $profUser = User::factory()->create(['role_id' => Role::where('name', 'professor')->first()->id]);
        $professor = Professor::create([
            'user_id' => $profUser->id,
            'status' => 'vacataire',
            'department' => 'Génie Informatique',
            'contract_end_date' => Carbon::now()->addDays(30)
        ]);

        // 2. Create Filiere, Group, Module, Room & Schedule
        $filiere = \App\Models\Filiere::firstOrCreate(['code' => 'GI'], ['name' => 'GI']);
        $group = Group::create([
            'name' => 'GI-3-A',
            'level' => '3',
            'filiere_id' => $filiere->id,
        ]);
        $module = Module::create(['name' => 'Algorithmique Avancée', 'code' => 'GI301']);
        $room = Room::create(['name' => 'Salle 101', 'capacity' => 40, 'type' => 'course']);

        // Schedule of 2 hours: 08:30:00 to 10:30:00
        $schedule = Schedule::create([
            'group_id' => $group->id,
            'module_id' => $module->id,
            'professor_id' => $professor->id,
            'room_id' => $room->id,
            'date' => Carbon::now()->toDateString(),
            'day_of_week' => 1,
            'start_time' => '08:30:00',
            'end_time' => '10:30:00',
        ]);

        // Create a student in the group to have someone to record
        $studentUser = User::factory()->create();
        $student = \App\Models\Student::create([
            'user_id' => $studentUser->id,
            'group_id' => $group->id,
            'filiere_id' => $filiere->id,
            'student_number' => 'STU-12345',
            'cin' => 'AB12345'
        ]);

        $this->assertDatabaseMissing('confirmed_sessions', [
            'professor_id' => $professor->id,
            'schedule_id' => $schedule->id,
        ]);

        // 3. Act as Professor and submit attendance
        $response = $this->actingAs($profUser)->post(route('professor.absences.store'), [
            'schedule_id' => $schedule->id,
            'date' => Carbon::now()->toDateString(),
            'session_type' => 'Lecture',
            'absences' => [
                $student->id => '1', // Mark student present
            ],
        ]);

        $response->assertRedirect(route('professor.absences.index'));

        // 4. Assert ConfirmedSession is created with 2.0 hours duration
        $this->assertDatabaseHas('confirmed_sessions', [
            'professor_id' => $professor->id,
            'schedule_id' => $schedule->id,
            'duration' => 2.00,
        ]);
    }

    public function test_professor_can_access_worked_hours_dashboard()
    {
        $profUser = User::factory()->create(['role_id' => Role::where('name', 'professor')->first()->id]);
        $professor = Professor::create([
            'user_id' => $profUser->id,
            'status' => 'permanent',
            'department' => 'Génie Informatique',
            'contract_end_date' => Carbon::now()->addDays(30)
        ]);

        $response = $this->actingAs($profUser)->get(route('professor.hours.index'));
        $response->assertStatus(200);
        $response->assertViewIs('professor.hours.index');
        $response->assertViewHas('hoursWeek');
        $response->assertViewHas('hoursMonth');
        $response->assertViewHas('hoursTotal');
    }

    public function test_admin_can_access_worked_hours_control_dashboard()
    {
        $adminUser = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);
        
        $response = $this->actingAs($adminUser)->get(route('admin.hours.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.hours.index');
        $response->assertViewHas('professors');
    }
}
