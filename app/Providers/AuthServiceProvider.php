<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\ProjectIdea;
use App\Policies\ProjectPolicy;
use App\Policies\ProjectIdeaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Project::class     => ProjectPolicy::class,
        ProjectIdea::class => ProjectIdeaPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // ── Gates ─────────────────────────────────────────────────────────────

        // إنشاء مشروع
        Gate::define('create-project', fn($user) =>
            in_array($user->role, ['admin', 'coordinator', 'supervisor', 'student'])
        );

        // اعتماد المشاريع والأفكار
        Gate::define('approve-project', fn($user) =>
            in_array($user->role, ['admin', 'coordinator'])
        );

        Gate::define('approve-idea', fn($user) =>
            in_array($user->role, ['admin', 'coordinator'])
        );

        // إنشاء فكرة مشروع
        Gate::define('create-idea', fn($user) =>
            in_array($user->role, ['admin', 'coordinator', 'supervisor'])
        );

        // إدارة اللجان وجداول المناقشات
        Gate::define('manage-committee', fn($user) =>
            in_array($user->role, ['admin', 'coordinator'])
        );

        Gate::define('manage-schedule', fn($user) =>
            in_array($user->role, ['admin', 'coordinator'])
        );

        // رفع التقارير
        Gate::define('submit-report', function ($user, Project $project) {
            return $project->students()->where('student_id', $user->id)->exists()
                || $user->isAdmin()
                || $user->isCoordinator();
        });

        // مراجعة التقارير
        Gate::define('review-report', function ($user, $report) {
            return $user->isAdmin()
                || $user->isCoordinator()
                || ($user->isSupervisor() && $report->project->supervisor_id === $user->id);
        });

        // أرشفة المشاريع
        Gate::define('archive-project', fn($user) =>
            in_array($user->role, ['admin', 'coordinator'])
        );

        // تأكيد المناقشة
        Gate::define('mark-discussed', function ($user, Project $project) {
            if (in_array($user->role, ['admin', 'coordinator'])) return true;
            return $project->committees()
                           ->whereHas('members', fn($q) => $q->where('user_id', $user->id))
                           ->exists();
        });
    }
}