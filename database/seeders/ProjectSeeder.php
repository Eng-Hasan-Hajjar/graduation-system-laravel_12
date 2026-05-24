<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── Projects ──────────────────────────────────────────────────────────
        $projects = [

            // 1) مشروع مناقَش ومؤرشف (2022-2023)
            [
                'project_idea_id'       => 5, // نظام إدارة المستشفيات
                'department_id'         => 2,
                'semester_id'           => 2, // الفصل الثاني 2022-2023
                'supervisor_id'         => 5, // د. خالد
                'created_by'            => 5,
                'project_number'        => 'GRAD-2023-0001',
                'title_ar'              => 'نظام إدارة المستشفيات الإلكتروني',
                'title_en'              => 'Electronic Hospital Management System',
                'description_ar'        => 'منصة شاملة لإدارة العمليات اليومية في المستشفيات تشمل تسجيل المرضى وجدولة المواعيد وإدارة الطواقم الطبية.',
                'objectives_ar'         => "رقمنة العمليات الإدارية للمستشفى\nتحسين تجربة المريض\nتوفير تقارير إدارية شاملة",
                'project_type'          => 'graduation',
                'academic_year_level'   => 4,
                'tools'                 => json_encode(['Laravel', 'Vue.js', 'MySQL', 'Pusher']),
                'technologies'          => json_encode(['REST API', 'WebSockets']),
                'registration_date'     => '2023-02-05',
                'start_date'            => '2023-02-10',
                'expected_end_date'     => '2023-06-01',
                'submission_date'       => '2023-05-28',
                'defense_date'          => '2023-06-10',
                'actual_defense_date'   => '2023-06-10',
                'defense_time'          => '10:00:00',
                'defense_location'      => 'كلية الحاسبات',
                'defense_room'          => 'قاعة 3أ',
                'status'                => 'archived',
                'approved_by'           => 1,
                'approved_at'           => '2023-02-08',
                'is_discussed'          => true,
                'discussed_at'          => '2023-06-10 10:00:00',
                'final_grade'           => 91.5,
                'grade_letter'          => 'A',
                'committee_notes_ar'    => 'مشروع متميز يعكس مستوى عالٍ من الفهم والتطبيق. العرض التقديمي كان واضحاً واحترافياً، والنظام يعمل بكفاءة عالية.',
                'is_archived'           => true,
                'archived_at'           => '2023-07-01',
                'progress_percentage'   => 100,
                'supervisor_notes'      => 'الطلاب أبدوا التزاماً ممتازاً طوال فترة المشروع',
            ],

            // 2) مشروع مناقَش (2023-2024) - غير مؤرشف بعد
            [
                'project_idea_id'       => 2, // تعرف على الوجه
                'department_id'         => 1,
                'semester_id'           => 4, // الفصل الثاني 2023-2024
                'supervisor_id'         => 3, // د. أحمد
                'created_by'            => 3,
                'project_number'        => 'GRAD-2024-0001',
                'title_ar'              => 'تطبيق تتبع الحضور الجامعي باستخدام التعرف على الوجه',
                'title_en'              => 'University Attendance Tracking with Facial Recognition',
                'description_ar'        => 'نظام ذكي لتسجيل حضور الطلاب تلقائياً عن طريق التعرف على الوجه باستخدام كاميرات القاعات الدراسية وتقنيات الذكاء الاصطناعي.',
                'objectives_ar'         => "أتمتة تسجيل الحضور والغياب\nتقليل التلاعب والغش في الحضور\nتوفير تقارير فورية للمشرفين",
                'methodology_ar'        => 'استخدام نموذج DeepFace للتعرف على الوجوه مع قاعدة بيانات MySQL لتخزين البيانات',
                'project_type'          => 'graduation',
                'academic_year_level'   => 4,
                'tools'                 => json_encode(['Python', 'OpenCV', 'TensorFlow', 'Django', 'MySQL']),
                'technologies'          => json_encode(['Deep Learning', 'CNN', 'REST API']),
                'registration_date'     => '2024-02-03',
                'start_date'            => '2024-02-10',
                'expected_end_date'     => '2024-06-01',
                'submission_date'       => '2024-05-25',
                'defense_date'          => '2024-06-08',
                'actual_defense_date'   => '2024-06-08',
                'defense_time'          => '09:00:00',
                'defense_location'      => 'كلية الحاسبات وتقنية المعلومات',
                'defense_room'          => 'قاعة 101',
                'status'                => 'defended',
                'approved_by'           => 1,
                'approved_at'           => '2024-02-07',
                'is_discussed'          => true,
                'discussed_at'          => '2024-06-08 09:00:00',
                'final_grade'           => 88.0,
                'grade_letter'          => 'B+',
                'committee_notes_ar'    => 'مشروع جيد جداً مع تطبيق ناجح للذكاء الاصطناعي. يُنصح بتطوير دقة النظام في بيئات الإضاءة المنخفضة.',
                'is_archived'           => false,
                'progress_percentage'   => 100,
                'supervisor_notes'      => 'الفريق أبدى جهداً كبيراً في التحسين المستمر للدقة',
            ],

            // 3) مشروع قيد التنفيذ (الفصل الحالي)
            [
                'project_idea_id'       => 1, // نظام إدارة مشاريع التخرج
                'department_id'         => 1,
                'semester_id'           => 6, // الفصل الثاني 2024-2025
                'supervisor_id'         => 3, // د. أحمد
                'created_by'            => 9, // محمد
                'project_number'        => 'GRAD-2025-0001',
                'title_ar'              => 'نظام إدارة مشاريع التخرج الذكي للجامعات',
                'title_en'              => 'Smart Graduation Project Management System for Universities',
                'description_ar'        => 'تطوير منصة إلكترونية متكاملة لإدارة مشاريع التخرج في الجامعات، حيث تساعد المنصة الطلاب والمشرفين وإدارة الكلية على تنظيم جميع مراحل مشروع التخرج بشكل رقمي وفعال.',
                'description_en'        => 'Developing a comprehensive electronic platform for managing graduation projects in universities, helping students, supervisors, and faculty administration organize all stages of graduation projects digitally and effectively.',
                'objectives_ar'         => "تنظيم عملية إدارة مشاريع التخرج داخل الجامعة\nتسهيل التواصل بين الطلاب والمشرفين\nتوفير نظام إلكتروني لإدارة ومتابعة المشاريع\nإنشاء قاعدة بيانات للأبحاث السابقة لمنع التكرار",
                'expected_outcomes_ar'  => "منصة ويب متكاملة باللغتين العربية والإنجليزية\nنظام صلاحيات متعدد الأدوار\nتقارير وإحصاءات شاملة\nأرشيف قابل للبحث",
                'methodology_ar'        => 'منهجية Agile مع دورات تطوير أسبوعية ومتابعة دورية مع المشرف',
                'project_type'          => 'graduation',
                'academic_year_level'   => 4,
                'tools'                 => json_encode(['Laravel 12', 'MySQL', 'Bootstrap 5', 'JavaScript', 'jQuery']),
                'technologies'          => json_encode(['PHP 8.3', 'Blade Templates', 'Eloquent ORM', 'Spatie Permissions']),
                'registration_date'     => '2025-02-05',
                'start_date'            => '2025-02-10',
                'expected_end_date'     => '2025-06-01',
                'submission_date'       => null,
                'defense_date'          => '2025-06-12',
                'defense_time'          => '11:00:00',
                'defense_location'      => 'كلية الحاسبات وتقنية المعلومات',
                'defense_room'          => 'قاعة المناقشات A',
                'status'                => 'in_progress',
                'approved_by'           => 1,
                'approved_at'           => '2025-02-08',
                'is_discussed'          => false,
                'is_archived'           => false,
                'progress_percentage'   => 75,
                'supervisor_notes'      => 'الفريق يسير بشكل ممتاز. يُطلب تسريع العمل على وحدة التقارير.',
            ],

            // 4) مشروع معلق في انتظار الاعتماد
            [
                'project_idea_id'       => null,
                'department_id'         => 4,
                'semester_id'           => 6,
                'supervisor_id'         => 4, // د. فاطمة
                'created_by'            => 12, // نورة
                'project_number'        => 'GRAD-2025-0002',
                'title_ar'              => 'نظام توصية المقررات الدراسية باستخدام الذكاء الاصطناعي',
                'title_en'              => 'AI-based Course Recommendation System',
                'description_ar'        => 'تطوير نظام ذكي يوصي الطلاب بالمقررات الدراسية المناسبة بناءً على أدائهم السابق واهتماماتهم وأهدافهم المهنية.',
                'objectives_ar'         => "تقديم توصيات مخصصة للطلاب\nتحسين مسيرة الطالب الأكاديمية\nدمج تعلم الآلة مع نظام إدارة التعلم",
                'expected_outcomes_ar'  => "نموذج توصية دقيق\nواجهة مستخدم سهلة\nتكامل مع نظام الجامعة",
                'project_type'          => 'graduation',
                'academic_year_level'   => 4,
                'tools'                 => json_encode(['Python', 'Flask', 'Scikit-learn', 'React', 'PostgreSQL']),
                'technologies'          => json_encode(['Collaborative Filtering', 'Content-Based Filtering', 'Hybrid Recommender']),
                'registration_date'     => '2025-02-10',
                'start_date'            => null,
                'expected_end_date'     => '2025-06-01',
                'status'                => 'pending',
                'is_discussed'          => false,
                'is_archived'           => false,
                'progress_percentage'   => 0,
            ],

            // 5) مشروع فصلي قيد التنفيذ
            [
                'project_idea_id'       => 9, // نظام حجز المرافق
                'department_id'         => 2,
                'semester_id'           => 6,
                'supervisor_id'         => 5, // د. خالد
                'created_by'            => 14, // ليلى
                'project_number'        => 'SEM-2025-0001',
                'title_ar'              => 'نظام حجز مواعيد المرافق الجامعية',
                'title_en'              => 'University Facilities Booking System',
                'description_ar'        => 'نظام لحجز القاعات والملاعب والمعامل في الجامعة مع التحقق من التوافر والمنع من التضارب.',
                'objectives_ar'         => "تسهيل حجز المرافق الجامعية\nمنع تضارب الحجوزات\nتوفير تقارير الاستخدام",
                'project_type'          => 'semester',
                'academic_year_level'   => 3,
                'tools'                 => json_encode(['Laravel', 'FullCalendar.js', 'MySQL', 'Bootstrap']),
                'technologies'          => json_encode(['REST API', 'Email Notifications', 'AJAX']),
                'registration_date'     => '2025-02-08',
                'start_date'            => '2025-02-15',
                'expected_end_date'     => '2025-05-15',
                'defense_date'          => '2025-05-20',
                'defense_time'          => '14:00:00',
                'status'                => 'approved',
                'approved_by'           => 1,
                'approved_at'           => '2025-02-10',
                'is_discussed'          => false,
                'is_archived'           => false,
                'progress_percentage'   => 55,
            ],

            // 6) مشروع AI مناقَش ومؤرشف من 2022-2023
            [
                'project_idea_id'       => null,
                'department_id'         => 4,
                'semester_id'           => 1,
                'supervisor_id'         => 4,
                'created_by'            => 4,
                'project_number'        => 'GRAD-2022-0001',
                'title_ar'              => 'تصنيف المشاعر في التغريدات العربية',
                'title_en'              => 'Sentiment Analysis in Arabic Tweets',
                'description_ar'        => 'تطوير نموذج تعلم آلي لتحليل المشاعر (إيجابي/سلبي/محايد) في تغريدات اللغة العربية باستخدام تقنيات NLP المتقدمة.',
                'objectives_ar'         => "تصنيف المشاعر في النصوص العربية بدقة عالية\nمعالجة اللهجات العربية المختلفة\nبناء مجموعة بيانات عربية مُصنَّفة",
                'expected_outcomes_ar'  => "نموذج تصنيف بدقة أعلى من 85%\nمجموعة بيانات عربية للمشاعر\nواجهة تجريبية للنموذج",
                'project_type'          => 'graduation',
                'academic_year_level'   => 4,
                'tools'                 => json_encode(['Python', 'AraBERT', 'Transformers', 'FastAPI', 'Streamlit']),
                'technologies'          => json_encode(['BERT', 'Transfer Learning', 'Arabic NLP']),
                'registration_date'     => '2022-09-05',
                'start_date'            => '2022-09-10',
                'expected_end_date'     => '2023-01-10',
                'submission_date'       => '2023-01-05',
                'defense_date'          => '2023-01-15',
                'actual_defense_date'   => '2023-01-15',
                'defense_time'          => '10:00:00',
                'defense_location'      => 'كلية الحاسبات',
                'defense_room'          => 'قاعة المناقشات ب',
                'status'                => 'archived',
                'approved_by'           => 1,
                'approved_at'           => '2022-09-08',
                'is_discussed'          => true,
                'discussed_at'          => '2023-01-15 10:00:00',
                'final_grade'           => 95.0,
                'grade_letter'          => 'A+',
                'committee_notes_ar'    => 'مشروع استثنائي يرقى إلى مستوى الأبحاث الأكاديمية المنشورة. النتائج متفوقة على الدراسات السابقة في هذا المجال.',
                'is_archived'           => true,
                'archived_at'           => '2023-02-01',
                'archive_notes'         => 'مشروع متميز تم توصيته للنشر الأكاديمي',
                'progress_percentage'   => 100,
            ],
        ];

        foreach ($projects as $project) {
            DB::table('projects')->insert(array_merge($project, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ── Project Students ──────────────────────────────────────────────────
        // project 1 (GRAD-2023-0001) - مناقَش مؤرشف
        DB::table('project_students')->insert([
            ['project_id' => 1, 'student_id' => 9,  'role' => 'leader', 'joined_at' => '2023-02-05', 'status' => 'active', 'individual_grade' => 92.0, 'created_at' => $now, 'updated_at' => $now],
            ['project_id' => 1, 'student_id' => 10, 'role' => 'member', 'joined_at' => '2023-02-05', 'status' => 'active', 'individual_grade' => 91.0, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // project 2 (GRAD-2024-0001) - مناقَش
        DB::table('project_students')->insert([
            ['project_id' => 2, 'student_id' => 11, 'role' => 'leader', 'joined_at' => '2024-02-03', 'status' => 'active', 'individual_grade' => 88.0, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // project 3 (GRAD-2025-0001) - قيد التنفيذ
        DB::table('project_students')->insert([
            ['project_id' => 3, 'student_id' => 9,  'role' => 'leader', 'joined_at' => '2025-02-05', 'status' => 'active', 'individual_grade' => null, 'created_at' => $now, 'updated_at' => $now],
            ['project_id' => 3, 'student_id' => 10, 'role' => 'member', 'joined_at' => '2025-02-05', 'status' => 'active', 'individual_grade' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // project 4 (GRAD-2025-0002) - معلق
        DB::table('project_students')->insert([
            ['project_id' => 4, 'student_id' => 12, 'role' => 'leader', 'joined_at' => '2025-02-10', 'status' => 'active', 'individual_grade' => null, 'created_at' => $now, 'updated_at' => $now],
            ['project_id' => 4, 'student_id' => 13, 'role' => 'member', 'joined_at' => '2025-02-10', 'status' => 'active', 'individual_grade' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // project 5 (SEM-2025-0001) - فصلي
        DB::table('project_students')->insert([
            ['project_id' => 5, 'student_id' => 14, 'role' => 'leader', 'joined_at' => '2025-02-08', 'status' => 'active', 'individual_grade' => null, 'created_at' => $now, 'updated_at' => $now],
            ['project_id' => 5, 'student_id' => 15, 'role' => 'member', 'joined_at' => '2025-02-08', 'status' => 'active', 'individual_grade' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // project 6 (GRAD-2022-0001) - مؤرشف قديم
        DB::table('project_students')->insert([
            ['project_id' => 6, 'student_id' => 11, 'role' => 'leader', 'joined_at' => '2022-09-05', 'status' => 'active', 'individual_grade' => 95.0, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── Milestones for project 3 (active) ────────────────────────────────
        DB::table('project_milestones')->insert([
            ['project_id' => 3, 'title_ar' => 'تحليل المتطلبات وتصميم قاعدة البيانات', 'title_en' => 'Requirements Analysis & DB Design', 'due_date' => '2025-02-28', 'completed_date' => '2025-02-26', 'status' => 'completed', 'order' => 1, 'weight_percentage' => 15, 'created_at' => $now, 'updated_at' => $now],
            ['project_id' => 3, 'title_ar' => 'تطوير الـ Backend وقاعدة البيانات',       'title_en' => 'Backend & Database Development',   'due_date' => '2025-03-31', 'completed_date' => '2025-03-28', 'status' => 'completed', 'order' => 2, 'weight_percentage' => 25, 'created_at' => $now, 'updated_at' => $now],
            ['project_id' => 3, 'title_ar' => 'تطوير واجهة المستخدم (Frontend)',          'title_en' => 'Frontend Development',             'due_date' => '2025-04-30', 'completed_date' => null,         'status' => 'in_progress', 'order' => 3, 'weight_percentage' => 25, 'created_at' => $now, 'updated_at' => $now],
            ['project_id' => 3, 'title_ar' => 'الاختبار والتحقق من الجودة',              'title_en' => 'Testing & Quality Assurance',       'due_date' => '2025-05-20', 'completed_date' => null,         'status' => 'pending',     'order' => 4, 'weight_percentage' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['project_id' => 3, 'title_ar' => 'التوثيق والتقرير النهائي',                 'title_en' => 'Documentation & Final Report',      'due_date' => '2025-06-01', 'completed_date' => null,         'status' => 'pending',     'order' => 5, 'weight_percentage' => 15, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── Reports for project 3 ─────────────────────────────────────────────
        DB::table('project_reports')->insert([
            [
                'project_id'          => 3,
                'submitted_by'        => 9,
                'title_ar'            => 'تقرير الأسبوع الأول - تحليل المتطلبات',
                'content_ar'          => 'تم خلال هذا الأسبوع: مراجعة المتطلبات الوظيفية وغير الوظيفية، تصميم مخطط قاعدة البيانات الأولي، مراجعة الأنظمة المشابهة واستخلاص الفروق.',
                'report_type'         => 'weekly',
                'week_number'         => 1,
                'report_date'         => '2025-02-17',
                'status'              => 'approved',
                'supervisor_feedback' => 'عمل جيد. يرجى إضافة مخطط UML لعلاقات الكيانات في التقرير القادم.',
                'reviewed_by'         => 3,
                'reviewed_at'         => '2025-02-19',
                'grade'               => 88.0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'project_id'          => 3,
                'submitted_by'        => 9,
                'title_ar'            => 'تقرير الأسبوع الثاني - تصميم قاعدة البيانات',
                'content_ar'          => 'تم خلال هذا الأسبوع: إنهاء تصميم قاعدة البيانات بالكامل (15 جدولاً)، إنشاء الـ Migrations، كتابة الـ Seeders الأساسية.',
                'report_type'         => 'weekly',
                'week_number'         => 2,
                'report_date'         => '2025-02-24',
                'status'              => 'approved',
                'supervisor_feedback' => 'ممتاز. التصميم احترافي ومدروس جيداً.',
                'reviewed_by'         => 3,
                'reviewed_at'         => '2025-02-26',
                'grade'               => 92.0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'project_id'          => 3,
                'submitted_by'        => 9,
                'title_ar'            => 'تقرير مرحلة الـ Backend',
                'content_ar'          => 'تم الانتهاء من تطوير الـ Controllers الرئيسية وربطها بقاعدة البيانات. تم اختبار جميع الـ APIs باستخدام Postman.',
                'report_type'         => 'milestone',
                'week_number'         => 6,
                'report_date'         => '2025-03-28',
                'status'              => 'approved',
                'supervisor_feedback' => 'جيد جداً. يرجى التأكد من إضافة التحقق من الصلاحيات (Policies) لجميع العمليات.',
                'reviewed_by'         => 3,
                'reviewed_at'         => '2025-03-30',
                'grade'               => 90.0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'project_id'  => 3,
                'submitted_by'=> 9,
                'title_ar'    => 'تقرير الأسبوع العاشر - تطوير الواجهة',
                'content_ar'  => 'جارٍ تطوير واجهة المستخدم. تم الانتهاء من 60% من الصفحات الرئيسية.',
                'report_type' => 'weekly',
                'week_number' => 10,
                'report_date' => '2025-04-21',
                'status'      => 'submitted',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'grade'       => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }
}