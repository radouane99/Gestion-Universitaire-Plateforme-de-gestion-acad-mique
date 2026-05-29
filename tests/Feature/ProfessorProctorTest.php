<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Professor;
use App\Models\ProfessorConvocation;
use App\Models\ProfessorAvailability;
use App\Models\Convocation;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\AcademicYear;
use App\Models\Filiere;
use App\Models\Group;
use App\Models\Module;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfessorProctorTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function createProfessorUser(): array
    {
        $user = User::factory()->professor()->create();
        $prof = Professor::create([
            'user_id'    => $user->id,
            'department' => 'Informatique',
        ]);
        return [$user, $prof];
    }

    private function createExam(?Group $group = null): array
    {
        $year    = AcademicYear::firstOrCreate(['name' => '2025-2026'], ['is_current' => true]);
        $session = ExamSession::create([
            'academic_year_id' => $year->id,
            'type'             => 'normal_spring',
            'start_date'       => now()->addDays(5)->toDateString(),
            'end_date'         => now()->addDays(30)->toDateString(),
        ]);
        $filiere = Filiere::firstOrCreate(['name' => 'Informatique'], ['code' => 'INFO']);
        $group   = $group ?? Group::create(['name' => 'G1', 'filiere_id' => $filiere->id, 'level' => 3]);
        $module  = Module::create(['name' => 'Algo', 'code' => 'ALG' . rand(1,999), 'filiere_id' => $filiere->id]);
        $room    = Room::create(['name' => 'Salle A' . rand(1,99), 'capacity' => 30]);

        $exam = Exam::create([
            'exam_session_id' => $session->id,
            'module_id'       => $module->id,
            'group_id'        => $group->id,
            'room_id'         => $room->id,
            'date'            => now()->addDays(10)->toDateString(),
            'start_time'      => '09:00:00',
            'duration'        => 90,
            'type'            => 'Final',
        ]);

        return [$exam, $session, $group];
    }

    // ─── Availability Tests ───────────────────────────────────────────────────

    /** @test */
    public function professor_must_select_at_least_3_availability_days(): void
    {
        [$user, $prof] = $this->createProfessorUser();

        // Attempt with only 2 dates
        $response = $this->actingAs($user)
            ->post(route('professor.availability.store'), [
                'dates'     => [
                    now()->addDays(1)->format('Y-m-d'),
                    now()->addDays(2)->format('Y-m-d'),
                ],
                'exam_week' => 'Semaine Examens Juin 2026',
            ]);

        // Should fail validation
        $response->assertSessionHasErrors('dates');
    }

    /** @test */
    public function professor_can_submit_3_or_more_availability_days(): void
    {
        [$user, $prof] = $this->createProfessorUser();

        $response = $this->actingAs($user)
            ->post(route('professor.availability.store'), [
                'dates'     => [
                    now()->addDays(1)->format('Y-m-d'),
                    now()->addDays(2)->format('Y-m-d'),
                    now()->addDays(3)->format('Y-m-d'),
                ],
                'exam_week' => 'Semaine Examens Juin 2026',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('professor_availabilities', [
            'professor_id' => $prof->id,
            'exam_week'    => 'Semaine Examens Juin 2026',
        ]);
        $this->assertEquals(3, ProfessorAvailability::where('professor_id', $prof->id)->count());
    }

    /** @test */
    public function professor_cannot_delete_another_professors_availability(): void
    {
        [$user1, $prof1] = $this->createProfessorUser();
        [$user2, $prof2] = $this->createProfessorUser();

        $avail = ProfessorAvailability::create([
            'professor_id'   => $prof1->id,
            'available_date' => now()->addDays(5)->toDateString(),
            'exam_week'      => 'Juin 2026',
        ]);

        // Prof2 tries to delete Prof1's availability
        $response = $this->actingAs($user2)
            ->delete(route('professor.availability.destroy', $avail));

        $response->assertForbidden();
    }

    // ─── Professor Convocation Tests ──────────────────────────────────────────

    /** @test */
    public function professor_can_see_own_proctor_convocations(): void
    {
        [$user, $prof] = $this->createProfessorUser();
        [$exam] = $this->createExam();

        ProfessorConvocation::create([
            'professor_id' => $prof->id,
            'exam_id'      => $exam->id,
            'reference'    => 'SURV-2026-000001',
            'status'       => 'generated',
            'role'         => 'principal',
        ]);

        $response = $this->actingAs($user)->get(route('professor.proctor_convocations.index'));
        $response->assertOk();
        $response->assertSee('SURV-2026-000001');
    }

    /** @test */
    public function professor_cannot_download_another_professors_convocation(): void
    {
        [$user1, $prof1] = $this->createProfessorUser();
        [$user2, $prof2] = $this->createProfessorUser();
        [$exam] = $this->createExam();

        // Create convocation for prof1
        $conv = ProfessorConvocation::create([
            'professor_id' => $prof1->id,
            'exam_id'      => $exam->id,
            'reference'    => 'SURV-2026-000002',
            'status'       => 'generated',
            'role'         => 'assistant',
        ]);

        // Prof2 tries to download prof1's convocation
        $response = $this->actingAs($user2)
            ->get(route('professor.proctor_convocations.download', $conv));

        $response->assertForbidden();
    }

    /** @test */
    public function professor_can_confirm_own_convocation(): void
    {
        [$user, $prof] = $this->createProfessorUser();
        [$exam] = $this->createExam();

        $conv = ProfessorConvocation::create([
            'professor_id' => $prof->id,
            'exam_id'      => $exam->id,
            'reference'    => 'SURV-2026-000003',
            'status'       => 'sent',
            'role'         => 'principal',
        ]);

        $response = $this->actingAs($user)
            ->post(route('professor.proctor_convocations.confirm', $conv));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('professor_convocations', [
            'id'     => $conv->id,
            'status' => 'confirmed',
        ]);
        $this->assertNotNull($conv->fresh()->confirmed_at);
    }

    /** @test */
    public function professor_cannot_confirm_another_professors_convocation(): void
    {
        [$user1, $prof1] = $this->createProfessorUser();
        [$user2, $prof2] = $this->createProfessorUser();
        [$exam] = $this->createExam();

        $conv = ProfessorConvocation::create([
            'professor_id' => $prof1->id,
            'exam_id'      => $exam->id,
            'reference'    => 'SURV-2026-000004',
            'status'       => 'sent',
            'role'         => 'principal',
        ]);

        $response = $this->actingAs($user2)
            ->post(route('professor.proctor_convocations.confirm', $conv));

        $response->assertForbidden();

        // Status should remain unchanged
        $this->assertDatabaseHas('professor_convocations', [
            'id'     => $conv->id,
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function auto_assign_respects_professor_availability(): void
    {
        [$user, $prof] = $this->createProfessorUser();
        [$exam, $session] = $this->createExam();

        // Prof has NOT declared availability for the exam date
        // So the auto-assign should NOT assign them (no availabilities)

        $response = $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.convocations.auto_assign'), [
                'session_id' => $session->id,
            ]);

        $response->assertRedirect();

        // Exam should still have 0 proctors because no one declared availability
        $this->assertEquals(0, $exam->proctors()->count());
    }

    /** @test */
    public function auto_assign_assigns_professor_with_availability(): void
    {
        [$user, $prof] = $this->createProfessorUser();
        [$exam, $session] = $this->createExam();

        // Create a student in the group to trigger the need for proctors
        $studentUser = User::factory()->student()->create();
        Student::create([
            'user_id'        => $studentUser->id,
            'group_id'       => $exam->group_id,
            'student_number' => 'STU-TEST-01',
        ]);

        // Prof declares availability for the exam date
        ProfessorAvailability::create([
            'professor_id'   => $prof->id,
            'available_date' => $exam->date,
            'exam_week'      => 'Juin 2026',
        ]);

        $response = $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.convocations.auto_assign'), [
                'session_id' => $session->id,
            ]);

        $response->assertRedirect();

        // Prof should now be assigned
        $this->assertTrue($exam->fresh()->proctors()->where('professors.id', $prof->id)->exists());
    }

    /** @test */
    public function proctor_convocation_reference_is_unique(): void
    {
        [$user1, $prof1] = $this->createProfessorUser();
        [$user2, $prof2] = $this->createProfessorUser();
        [$exam] = $this->createExam();

        $ref1 = ProfessorConvocation::generateReference();

        ProfessorConvocation::create([
            'professor_id' => $prof1->id,
            'exam_id'      => $exam->id,
            'reference'    => $ref1,
            'status'       => 'generated',
            'role'         => 'principal',
        ]);

        $ref2 = ProfessorConvocation::generateReference();

        // References must differ
        $this->assertNotEquals($ref1, $ref2);
    }
}
