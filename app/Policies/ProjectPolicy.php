<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // عرض المشروع
    public function view(User $user, Project $project): bool
    {
        if ($user->isStaff()) return true;

        // الطالب يرى مشاريعه فقط
        return $project->students()->where('student_id', $user->id)->exists();
    }

    // إنشاء مشروع
    public function create(User $user): bool
    {
        return true; // جميع المستخدمين يمكنهم طلب مشروع
    }

    // تعديل المشروع
    public function update(User $user, Project $project): bool
    {
        if ($user->isAdmin() || $user->isCoordinator()) return true;

        // المشرف يعدّل مشاريعه فقط
        if ($user->isSupervisor()) {
            return $project->supervisor_id === $user->id
                || $project->co_supervisor_id === $user->id;
        }

        // الطالب يعدّل مشروعه فقط وفقط إذا كان في مرحلة معينة
        if ($user->isStudent()) {
            return $project->students()->where('student_id', $user->id)->exists()
                && in_array($project->status, ['approved', 'in_progress']);
        }

        return false;
    }

    // حذف المشروع (Admin فقط)
    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }

    // اعتماد المشروع
    public function approveProject(User $user): bool
    {
        return $user->isAdmin() || $user->isCoordinator();
    }

    // تأكيد المناقشة
    public function markDiscussed(User $user, Project $project): bool
    {
        if ($user->isAdmin() || $user->isCoordinator()) return true;

        // رئيس أو عضو اللجنة
        return $project->committees()
                       ->whereHas('members', fn($q) => $q->where('user_id', $user->id))
                       ->exists();
    }

    // أرشفة المشروع
    public function archiveProject(User $user): bool
    {
        return $user->isAdmin() || $user->isCoordinator();
    }
}