<?php
namespace App\Http\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;

class ClassroomAuthorization {
    /**
     * Authorize a student to access a classroom.
     * Returns true if the user is a student belonging to the requested group.
     */
    public static function authorizeStudent($user, $groupId): bool
    {
        return $user && method_exists($user, 'isStudent') && $user->isStudent() && $user->student && $user->student->group_id == $groupId;
    }

    /**
     * Authorize a professor to access a classroom.
     * Returns true if the professor teaches the given group and module.
     */
    public static function authorizeProfessor($user, $groupId, $moduleId): bool
    {
        if (!($user && method_exists($user, 'isProfessor') && $user->isProfessor())) {
            return false;
        }
        $professorId = $user->professor->id ?? null;
        if (!$professorId) return false;
        return Schedule::where('professor_id', $professorId)
            ->where('group_id', $groupId)
            ->where('module_id', $moduleId)
            ->exists();
    }
}
