<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// ─── Defense Schedule Seeder ──────────────────────────────────────────────────
class DefenseScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('defense_schedules')->insert([
            // مناقشة مكتملة - مشروع 1 (2023)
            [
                'project_id'           => 1,
                'committee_id'         => 1,
                'scheduled_date'       => '2023-06-10',
                'scheduled_time'       => '10:00:00',
                'location'             => 'كلية الحاسبات',
                'room'                 => 'قاعة 3أ',
                'duration_minutes'     => 60,
                'status'               => 'completed',
                'notified_students'    => true,
                'notified_supervisors' => true,
                'notification_sent_at' => '2023-06-03 08:00:00',
                'notes'                => null,
                'created_by'           => 2,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            // مناقشة مكتملة - مشروع 2 (2024)
            [
                'project_id'           => 2,
                'committee_id'         => 2,
                'scheduled_date'       => '2024-06-08',
                'scheduled_time'       => '09:00:00',
                'location'             => 'كلية الحاسبات وتقنية المعلومات',
                'room'                 => 'قاعة 101',
                'duration_minutes'     => 75,
                'status'               => 'completed',
                'notified_students'    => true,
                'notified_supervisors' => true,
                'notification_sent_at' => '2024-06-01 08:00:00',
                'notes'                => null,
                'created_by'           => 2,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            // مناقشة مجدولة - مشروع 3 (2025) - قادمة
            [
                'project_id'           => 3,
                'committee_id'         => 3,
                'scheduled_date'       => '2025-06-12',
                'scheduled_time'       => '11:00:00',
                'location'             => 'كلية الحاسبات وتقنية المعلومات',
                'room'                 => 'قاعة المناقشات A',
                'duration_minutes'     => 60,
                'status'               => 'scheduled',
                'notified_students'    => true,
                'notified_supervisors' => true,
                'notification_sent_at' => now()->subDays(3),
                'notes'                => 'يرجى التأكد من أن الجهاز اللاحظ جاهز قبل 30 دقيقة من الموعد',
                'created_by'           => 2,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            // مناقشة مجدولة - مشروع 5 (فصلي 2025)
            [
                'project_id'           => 5,
                'committee_id'         => 4,
                'scheduled_date'       => '2025-05-20',
                'scheduled_time'       => '14:00:00',
                'location'             => 'كلية الحاسبات',
                'room'                 => 'قاعة 202',
                'duration_minutes'     => 45,
                'status'               => 'confirmed',
                'notified_students'    => true,
                'notified_supervisors' => true,
                'notification_sent_at' => now()->subDays(7),
                'notes'                => null,
                'created_by'           => 2,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
        ]);
    }
}

// ─── Setting Seeder ───────────────────────────────────────────────────────────
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'app_name_ar',        'value' => 'نظام إدارة مشاريع التخرج الذكي', 'type' => 'string',  'group' => 'general',  'label_ar' => 'اسم النظام بالعربية',    'label_en' => 'App Name (Arabic)'],
            ['key' => 'app_name_en',        'value' => 'Smart Graduation Project System', 'type' => 'string',  'group' => 'general',  'label_ar' => 'اسم النظام بالإنجليزية', 'label_en' => 'App Name (English)'],
            ['key' => 'default_language',   'value' => 'ar',                              'type' => 'string',  'group' => 'general',  'label_ar' => 'اللغة الافتراضية',       'label_en' => 'Default Language'],
            ['key' => 'default_theme',      'value' => 'light',                           'type' => 'string',  'group' => 'general',  'label_ar' => 'السمة الافتراضية',       'label_en' => 'Default Theme'],

            // Project Rules
            ['key' => 'max_students_per_project', 'value' => '4',   'type' => 'integer', 'group' => 'projects', 'label_ar' => 'الحد الأقصى للطلاب في المشروع', 'label_en' => 'Max Students Per Project'],
            ['key' => 'min_students_per_project', 'value' => '1',   'type' => 'integer', 'group' => 'projects', 'label_ar' => 'الحد الأدنى للطلاب في المشروع',  'label_en' => 'Min Students Per Project'],
            ['key' => 'auto_approve_ideas',       'value' => '0',   'type' => 'boolean', 'group' => 'projects', 'label_ar' => 'الموافقة التلقائية على الأفكار',  'label_en' => 'Auto-approve Ideas'],
            ['key' => 'allow_idea_duplicates',    'value' => '0',   'type' => 'boolean', 'group' => 'projects', 'label_ar' => 'السماح بتكرار الأفكار',           'label_en' => 'Allow Idea Duplicates'],

            // Defense
            ['key' => 'defense_notice_days',      'value' => '7',   'type' => 'integer', 'group' => 'defense',  'label_ar' => 'أيام الإشعار قبل المناقشة',      'label_en' => 'Defense Notice Days'],
            ['key' => 'defense_duration_minutes', 'value' => '60',  'type' => 'integer', 'group' => 'defense',  'label_ar' => 'مدة المناقشة (دقيقة)',           'label_en' => 'Defense Duration (minutes)'],
            ['key' => 'min_committee_members',    'value' => '2',   'type' => 'integer', 'group' => 'defense',  'label_ar' => 'الحد الأدنى لأعضاء اللجنة',      'label_en' => 'Minimum Committee Members'],
            ['key' => 'max_committee_members',    'value' => '5',   'type' => 'integer', 'group' => 'defense',  'label_ar' => 'الحد الأقصى لأعضاء اللجنة',      'label_en' => 'Maximum Committee Members'],

            // Grading
            ['key' => 'passing_grade',            'value' => '60',  'type' => 'integer', 'group' => 'grading',  'label_ar' => 'درجة النجاح',                    'label_en' => 'Passing Grade'],
            ['key' => 'supervisor_weight',        'value' => '40',  'type' => 'integer', 'group' => 'grading',  'label_ar' => 'وزن درجة المشرف (%)',             'label_en' => 'Supervisor Grade Weight (%)'],
            ['key' => 'committee_weight',         'value' => '60',  'type' => 'integer', 'group' => 'grading',  'label_ar' => 'وزن درجة اللجنة (%)',             'label_en' => 'Committee Grade Weight (%)'],

            // Notifications
            ['key' => 'notify_on_approval',       'value' => '1',   'type' => 'boolean', 'group' => 'notifications', 'label_ar' => 'إشعار عند الموافقة',        'label_en' => 'Notify on Approval'],
            ['key' => 'notify_on_rejection',      'value' => '1',   'type' => 'boolean', 'group' => 'notifications', 'label_ar' => 'إشعار عند الرفض',           'label_en' => 'Notify on Rejection'],
            ['key' => 'notify_defense_reminder',  'value' => '1',   'type' => 'boolean', 'group' => 'notifications', 'label_ar' => 'تذكير بموعد المناقشة',      'label_en' => 'Defense Reminder Notification'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'university_id' => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]));
        }
    }
}