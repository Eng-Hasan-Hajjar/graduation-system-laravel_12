<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ProjectIdea;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // ─── Gates لإجراءات المشاريع ───────────────────────────────────────
        Gate::define('approve-project', function (User $user) {
            return in_array($user->role, ['admin', 'coordinator']);
        });

        Gate::define('archive-project', function (User $user) {
            return in_array($user->role, ['admin', 'coordinator']);
        });

        Gate::define('mark-discussed', function (User $user) {
            return in_array($user->role, ['admin', 'coordinator']);
        });

        // ─── Gates لإجراءات الأفكار ────────────────────────────────────────
        Gate::define('create-idea', function (User $user) {
            // المشرفون والمسؤولون والمنسقون يمكنهم اقتراح أفكار
            return in_array($user->role, ['admin', 'coordinator', 'supervisor']);
        });

        Gate::define('approve-idea', function (User $user) {
            return in_array($user->role, ['admin', 'coordinator']);
        });

        // ─── ProjectIdea Policy (inline بدون ملف Policy منفصل) ────────────
        Gate::define('update', function (User $user, $model) {
            if ($model instanceof ProjectIdea) {
                // المقترح نفسه يمكنه التعديل إذا كانت الفكرة لا تزال pending
                if ($model->proposed_by === $user->id && $model->status === 'pending') {
                    return true;
                }
                // المسؤول والمنسق يمكنهم التعديل دائماً
                return in_array($user->role, ['admin', 'coordinator']);
            }
            return false;
        });

        Gate::define('delete', function (User $user, $model) {
            if ($model instanceof ProjectIdea) {
                if ($model->proposed_by === $user->id && $model->status === 'pending') {
                    return true;
                }
                return in_array($user->role, ['admin', 'coordinator']);
            }
            return false;
        });

        Gate::define('manage-committee', function (User $user) {
            return in_array($user->role, ['admin', 'coordinator']);
        });


        Gate::define('manage-schedule', function (User $user) {
            return in_array($user->role, ['admin', 'coordinator']);
        });

        Gate::define('review-report', function (User $user, \App\Models\ProjectReport $report) {
            // المشرف يمكنه مراجعة تقارير مشاريعه فقط
            if ($user->isSupervisor()) {
                return $report->project?->supervisor_id === $user->id;
            }
            // المسؤول والمنسق يمكنهم مراجعة أي تقرير
            return in_array($user->role, ['admin', 'coordinator']);
        });

    }
}