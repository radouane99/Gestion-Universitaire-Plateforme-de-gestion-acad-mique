<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Filiere;
use App\Models\Group;
use App\Models\Grade;
use App\Models\Module;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'student']);
        
        // Create an Academic Year
        AcademicYear::create([
            'name' => '2025/2026',
            'is_current' => true,
        ]);
    }

    public function test_guest_can_access_inscription_form()
    {
        $response = $this->get(route('inscription'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.inscription');
    }

    public function test_candidate_can_submit_inscription()
    {
        $filiere = Filiere::create(['name' => 'Génie Informatique', 'code' => 'GI']);

        $response = $this->post(route('inscription'), [
            // Personal & Account
            'name' => 'Salah Addine',
            'email' => 'salah@upf.ac.ma',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'cin' => 'K123456',
            'birth_date' => '2007-05-15',
            'birth_place' => 'Fès',

            // Father
            'father_name' => 'Omar',
            'father_cin' => 'K998877',
            'father_occupation' => 'Enseignant',

            // Mother
            'mother_name' => 'Fatima',
            'mother_cin' => 'K112233',
            'mother_occupation' => 'Foyer',

            // Bac
            'bac_filiere' => 'Sciences Physiques',
            'bac_grade' => '16.50',
            'bac_mention' => 'Très Bien',
            'bac_year' => 2025,

            // Target filiere
            'filiere_id' => $filiere->id,
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'salah@upf.ac.ma']);
        $this->assertDatabaseHas('students', [
            'cin' => 'K123456',
            'registration_status' => 'pending',
            'registration_type' => 'new',
            'filiere_id' => $filiere->id,
        ]);
    }

    public function test_admin_can_approve_registration()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@upf.ac.ma',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'admin')->first()->id,
        ]);

        $studentUser = User::create([
            'name' => 'New Student',
            'email' => 'student@upf.ac.ma',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'student')->first()->id,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_number' => 'EST-PROV-123',
            'cin' => 'K111111',
            'registration_status' => 'pending',
            'registration_type' => 'new',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.registrations.approve', $student));
        $response->assertRedirect();
        
        $this->assertEquals('approved', $student->fresh()->registration_status);
    }

    public function test_round_robin_auto_dispatch_balances_groups()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@upf.ac.ma',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'admin')->first()->id,
        ]);

        $filiere = Filiere::create(['name' => 'Génie Informatique', 'code' => 'GI']);

        // Create S1 level groups (ex: GI-1A, GI-1B)
        $groupA = Group::create(['name' => 'GI-1A', 'level' => 'Licence 1', 'filiere_id' => $filiere->id]);
        $groupB = Group::create(['name' => 'GI-1B', 'level' => 'Licence 1', 'filiere_id' => $filiere->id]);

        // Create 4 approved students without group assignment
        for ($i = 1; $i <= 4; $i++) {
            $user = User::create([
                'name' => "Student {$i}",
                'email' => "student{$i}@upf.ac.ma",
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'student')->first()->id,
            ]);

            Student::create([
                'user_id' => $user->id,
                'student_number' => "EST-PROV-{$i}",
                'cin' => "K00000{$i}",
                'filiere_id' => $filiere->id,
                'registration_status' => 'approved',
                'registration_type' => 'new',
                'group_id' => null,
            ]);
        }

        $response = $this->actingAs($admin)->post(route('admin.registrations.auto_dispatch'), [
            'filiere_id' => $filiere->id,
        ]);

        $response->assertRedirect();

        // 4 students should be split evenly: 2 in Group A and 2 in Group B!
        $this->assertEquals(2, Student::where('group_id', $groupA->id)->count());
        $this->assertEquals(2, Student::where('group_id', $groupB->id)->count());
    }

    public function test_student_can_re_register_and_carry_debts()
    {
        $filiere = Filiere::create(['name' => 'Génie Informatique', 'code' => 'GI']);
        
        $currentGroup = Group::create(['name' => 'GI-1', 'level' => 'Licence 1', 'filiere_id' => $filiere->id]);
        $promotedGroup = Group::create(['name' => 'GI-2', 'level' => 'Licence 2', 'filiere_id' => $filiere->id]);

        $studentUser = User::create([
            'name' => 'Current Student',
            'email' => 'current@upf.ac.ma',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'student')->first()->id,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_number' => 'EST-2025-001',
            'cin' => 'K888888',
            'group_id' => $currentGroup->id,
            'filiere_id' => $filiere->id,
            'registration_status' => 'approved',
            'registration_type' => 'new',
        ]);

        // Create two courses: one validated, one failed
        $modulePassed = Module::create(['name' => 'Module A', 'code' => 'MOD-A', 'filiere_id' => $filiere->id]);
        $moduleFailed = Module::create(['name' => 'Module B', 'code' => 'MOD-B', 'filiere_id' => $filiere->id]);

        // Grades: average should be (14 + 6) / 2 = 10 (Admitted with debts)
        Grade::create([
            'student_id' => $student->id,
            'module_id' => $modulePassed->id,
            'final_grade' => 14.00,
        ]);

        Grade::create([
            'student_id' => $student->id,
            'module_id' => $moduleFailed->id,
            'final_grade' => 6.00,
        ]);

        $this->assertTrue($student->isEligibleForReinscription());
        $this->assertEquals(10.00, $student->getYearlyGpa());
        $this->assertCount(1, $student->getFailedModules());

        // Process Réinscription
        $response = $this->actingAs($studentUser)->post(route('student.reinscription.store'), [
            'confirm_details' => '1',
        ]);

        $response->assertRedirect(route('student.dashboard'));

        $updatedStudent = $student->fresh();
        
        // Assert student promoted to Licence 2 group
        $this->assertEquals($promotedGroup->id, $updatedStudent->group_id);
        $this->assertEquals('reinscription', $updatedStudent->registration_type);
        
        // Assert failed module carried over to debt pivot table
        $this->assertDatabaseHas('student_credit_modules', [
            'student_id' => $student->id,
            'module_id' => $moduleFailed->id,
            'status' => 'pending',
        ]);
    }
}
