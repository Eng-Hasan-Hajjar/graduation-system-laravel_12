<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// ─── Committee Seeder ─────────────────────────────────────────────────────────
class CommitteeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // لجنة للمشروع المناقَش المؤرشف (project 1)
        DB::table('committees')->insert([
            [
                'project_id'      => 1,
                'name_ar'         => 'لجنة مناقشة مشروع نظام إدارة المستشفيات',
                'name_en'         => 'Hospital Management System Defense Committee',
                'scheduled_at'    => '2023-06-10 10:00:00',
                'actual_start_at' => '2023-06-10 10:05:00',
                'actual_end_at'   => '2023-06-10 11:10:00',
                'location'        => 'كلية الحاسبات',
                'room'            => 'قاعة 3أ',
                'is_completed'    => true,
                'completed_at'    => '2023-06-10 11:10:00',
                'notes_ar'        => 'المناقشة سارت بشكل ممتاز، الطلاب أجابوا على جميع الأسئلة بثقة.',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            // لجنة للمشروع المناقَش (project 2)
            [
                'project_id'      => 2,
                'name_ar'         => 'لجنة مناقشة مشروع التعرف على الوجه',
                'name_en'         => 'Facial Recognition Project Defense Committee',
                'scheduled_at'    => '2024-06-08 09:00:00',
                'actual_start_at' => '2024-06-08 09:10:00',
                'actual_end_at'   => '2024-06-08 10:20:00',
                'location'        => 'كلية الحاسبات وتقنية المعلومات',
                'room'            => 'قاعة 101',
                'is_completed'    => true,
                'completed_at'    => '2024-06-08 10:20:00',
                'notes_ar'        => 'المشروع يعمل بشكل جيد. اللجنة أوصت بتحسين الأداء في بيئات الإضاءة المنخفضة.',
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            // لجنة للمشروع الحالي (project 3) - مجدولة
            [
                'project_id'      => 3,
                'name_ar'         => 'لجنة مناقشة نظام إدارة مشاريع التخرج',
                'name_en'         => 'Graduation Project Management System Defense Committee',
                'scheduled_at'    => '2025-06-12 11:00:00',
                'actual_start_at' => null,
                'actual_end_at'   => null,
                'location'        => 'كلية الحاسبات وتقنية المعلومات',
                'room'            => 'قاعة المناقشات A',
                'is_completed'    => false,
                'completed_at'    => null,
                'notes_ar'        => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            // لجنة للمشروع الفصلي (project 5)
            [
                'project_id'      => 5,
                'name_ar'         => 'لجنة مناقشة نظام حجز المرافق الجامعية',
                'name_en'         => 'University Facilities Booking System Committee',
                'scheduled_at'    => '2025-05-20 14:00:00',
                'actual_start_at' => null,
                'actual_end_at'   => null,
                'location'        => 'كلية الحاسبات',
                'room'            => 'قاعة 202',
                'is_completed'    => false,
                'completed_at'    => null,
                'notes_ar'        => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ]);

        // ── Committee Members ─────────────────────────────────────────────────
        DB::table('committee_members')->insert([
            // Committee 1 (project 1 - archived)
            ['committee_id' => 1, 'user_id' => 7,  'role' => 'chair',   'attended' => true, 'grade_given' => 91.0, 'feedback' => 'مشروع متكامل واحترافي', 'created_at' => $now, 'updated_at' => $now],
            ['committee_id' => 1, 'user_id' => 5,  'role' => 'member',  'attended' => true, 'grade_given' => 92.0, 'feedback' => 'نظام البيانات ممتاز',    'created_at' => $now, 'updated_at' => $now],
            ['committee_id' => 1, 'user_id' => 8,  'role' => 'member',  'attended' => true, 'grade_given' => 91.5, 'feedback' => 'العرض التقديمي رائع',   'created_at' => $now, 'updated_at' => $now],

            // Committee 2 (project 2 - defended)
            ['committee_id' => 2, 'user_id' => 7,  'role' => 'chair',   'attended' => true, 'grade_given' => 87.0, 'feedback' => 'الذكاء الاصطناعي مطبق بشكل جيد', 'created_at' => $now, 'updated_at' => $now],
            ['committee_id' => 2, 'user_id' => 3,  'role' => 'member',  'attended' => true, 'grade_given' => 89.0, 'feedback' => 'النظام يعمل بدقة عالية',           'created_at' => $now, 'updated_at' => $now],
            ['committee_id' => 2, 'user_id' => 8,  'role' => 'member',  'attended' => true, 'grade_given' => 88.0, 'feedback' => null,                               'created_at' => $now, 'updated_at' => $now],

            // Committee 3 (project 3 - upcoming)
            ['committee_id' => 3, 'user_id' => 7,  'role' => 'chair',   'attended' => false, 'grade_given' => null, 'feedback' => null, 'created_at' => $now, 'updated_at' => $now],
            ['committee_id' => 3, 'user_id' => 8,  'role' => 'member',  'attended' => false, 'grade_given' => null, 'feedback' => null, 'created_at' => $now, 'updated_at' => $now],
            ['committee_id' => 3, 'user_id' => 6,  'role' => 'member',  'attended' => false, 'grade_given' => null, 'feedback' => null, 'created_at' => $now, 'updated_at' => $now],

            // Committee 4 (project 5 - semester, upcoming)
            ['committee_id' => 4, 'user_id' => 8,  'role' => 'chair',   'attended' => false, 'grade_given' => null, 'feedback' => null, 'created_at' => $now, 'updated_at' => $now],
            ['committee_id' => 4, 'user_id' => 5,  'role' => 'member',  'attended' => false, 'grade_given' => null, 'feedback' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}