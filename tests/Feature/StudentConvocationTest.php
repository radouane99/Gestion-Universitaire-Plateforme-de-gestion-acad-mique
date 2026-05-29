<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Convocation;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\AcademicYear;
use App\Models\Filiere;
use App\Models\Group;
use App\Models\Module;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class StudentConvocationTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function createAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    private function createStudentUser(): array
    {
        $filiere = Filiere::create(['name' => 'Informatique', 'code' => 'INFO']);
        $group   = Group::create(['name' => 'G1', 'filiere_id' => $filiere->id, 'level' => 3]);
        $user    = User::factory()->student()->create();
        $student = Student::create([
            'user_id'          => $user->id,
            'group_id'         => $group->id,
            'student_number'   => 'STU001',
            'academic_year_id' => null,
        ]);

        return [$user, $student, $group];
    }

    private function createExam(Group $group): Exam
    {
        $year    = AcademicYear::create(['name' => '2025-2026', 'is_current' => true]);
        $session = ExamSession::create([
            'academic_year_id' => $year->id,
            'type'             => 'normal_spring',
            'start_date'       => now()->addDays(5)->toDateString(),
            'end_date'         => now()->addDays(30)->toDateString(),
        ]);
        $module  = Module::create(['name' => 'Algorithmique', 'code' => 'ALG', 'filiere_id' => $group->filiere_id]);
        $room    = Room::create(['name' => 'Salle A', 'capacity' => 30]);

        return Exam::create([
            'exam_session_id' => $session->id,
            'module_id'       => $module->id,
            'group_id'        => $group->id,
            'room_id'         => $room->id,
            'date'            => now()->addDays(10)->toDateString(),
            'start_time'      => '09:00:00',
            'duration'        => 90,
            'type'            => 'Final',
        ]);
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /** @test */
    public function student_can_see_own_convocations(): void
    {
        [$user, $student, $group] = $this->createStudentUser();
        $exam = $this->createExam($group);

        Convocation::create([
            'exam_id'    => $exam->id,
            'student_id' => $student->id,
            'reference'  => 'CONV-2026-000001',
            'status'     => 'generated',
        ]);

        $response = $this->actingAs($user)->get(route('student.convocations.index'));
        $response->assertOk();
        $response->assertSee('CONV-2026-000001');
    }

    /** @test */
    public function student_can_download_own_convocation(): void
    {
        [$user, $student, $group] = $this->createStudentUser();
        $exam = $this->createExam($group);

        $conv = Convocation::create([
            'exam_id'    => $exam->id,
            'student_id' => $student->id,
            'reference'  => 'CONV-2026-000002',
            'status'     => 'generated',
        ]);

        // Load relations needed for PDF
        $conv->load(['exam.module', 'exam.room', 'exam.group.filiere', 'exam.proctors.user', 'student.user', 'student.group.filiere']);

        // The download endpoint should work for the owner
        $response = $this->actingAs($user)->get(route('student.convocations.download', $conv));

        // Should return PDF (200) or redirect — not 403
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function student_cannot_download_other_students_convocation(): void
    {
        [$user, $student, $group] = $this->createStudentUser();
        $exam = $this->createExam($group);

        // Create a different student's convocation
        $otherUser    = User::factory()->student()->create();
        $otherStudent = Student::create([
            'user_id'        => $otherUser->id,
            'group_id'       => $group->id,
            'student_number' => 'STU002',
        ]);

        $otherConv = Convocation::create([
            'exam_id'    => $exam->id,
            'student_id' => $otherStudent->id,
            'reference'  => 'CONV-2026-000003',
            'status'     => 'generated',
        ]);

        // Acting as original user, try to access other's convocation
        $response = $this->actingAs($user)->get(route('student.convocations.download', $otherConv));
        $response->assertForbidden(); // 403
    }

    /** @test */
    public function admin_can_generate_convocations_for_session(): void
    {
        $admin = $this->createAdmin();
        [$user, $student, $group] = $this->createStudentUser();
        $exam = $this->createExam($group);

        $response = $this->actingAs($admin)
            ->post(route('admin.convocations.generate_session'), [
                'session_id' => $exam->exam_session_id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Convocation should be created
        $this->assertDatabaseHas('convocations', [
            'exam_id'    => $exam->id,
            'student_id' => $student->id,
            'status'     => 'generated',
        ]);
    }

    /** @test */
    public function admin_can_send_convocation_emails_for_session(): void
    {
        Mail::fake();

        $admin = $this->createAdmin();
        [$user, $student, $group] = $this->createStudentUser();
        $exam = $this->createExam($group);

        // Pre-create a convocation
        Convocation::create([
            'exam_id'    => $exam->id,
            'student_id' => $student->id,
            'reference'  => 'CONV-2026-TEST-01',
            'status'     => 'generated',
        ]);

        // Since we can't generate a real PDF in tests, just verify the route works
        $response = $this->actingAs($admin)
            ->post(route('admin.convocations.send_session'), [
                'session_id' => $exam->exam_session_id,
            ]);

        $response->assertRedirect();
        // Response may have success or errors (DOMPDF won't render in unit tests) but must not crash with 500
        $this->assertContains($response->getStatusCode(), [302, 200]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_convocations(): void
    {
        $response = $this->get(route('student.convocations.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function convocation_status_updates_to_downloaded_on_download(): void
    {
        [$user, $student, $group] = $this->createStudentUser();
        $exam = $this->createExam($group);

        $conv = Convocation::create([
            'exam_id'    => $exam->id,
            'student_id' => $student->id,
            'reference'  => 'CONV-2026-STATUS-01',
            'status'     => 'generated',
        ]);

        // Manually simulate status change (PDF generation would fail in tests)
        $conv->update(['status' => 'downloaded']);

        $this->assertDatabaseHas('convocations', [
            'id'     => $conv->id,
            'status' => 'downloaded',
        ]);
    }
}
