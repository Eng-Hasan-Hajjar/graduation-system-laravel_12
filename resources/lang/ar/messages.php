<?php

return [

    // ── App ───────────────────────────────────────────────────────────────────
    'app' => [
        'name'          => 'نظام إدارة مشاريع التخرج',
        'switch_lang'   => 'English',
        'toggle_theme'  => 'تبديل السمة',
        'notifications' => 'الإشعارات',
        'no_notifications' => 'لا توجد إشعارات',
    ],

    // ── Navigation ────────────────────────────────────────────────────────────
    'nav' => [
        'dashboard'      => 'لوحة التحكم',
        'projects'       => 'المشاريع',
        'all_projects'   => 'جميع المشاريع',
        'my_project'     => 'مشروعي',
        'ideas'          => 'أفكار المشاريع',
        'my_reports'     => 'تقاريري',
        'discussions'    => 'المناقشات',
        'schedules'      => 'جداول المناقشات',
        'committees'     => 'اللجان',
        'archive'        => 'الأرشيف',
        'management'     => 'الإدارة',
        'users'          => 'المستخدمون',
        'academic_years' => 'السنوات الأكاديمية',
        'departments'    => 'الأقسام',
        'settings'       => 'الإعدادات',
        'profile'        => 'الملف الشخصي',
        'logout'         => 'تسجيل الخروج',
    ],

    // ── Dashboard ─────────────────────────────────────────────────────────────
    'dashboard' => [
        'welcome'            => 'مرحباً',
        'today'              => 'اليوم',
        'current_semester'   => 'الفصل الحالي',
        'upcoming_defenses'  => 'المناقشات القادمة',
        'pending_approvals'  => 'تنتظر الاعتماد',
        'recent_projects'    => 'آخر المشاريع',
    ],

    // ── Stats ─────────────────────────────────────────────────────────────────
    'stats' => [
        'total_projects'   => 'إجمالي المشاريع',
        'active_projects'  => 'المشاريع النشطة',
        'pending'          => 'في انتظار الاعتماد',
        'defended'         => 'تمت مناقشتها',
        'archived'         => 'المؤرشفة',
        'students'         => 'الطلاب',
        'supervisors'      => 'المشرفون',
        'pending_ideas'    => 'أفكار معلقة',
    ],

    // ── Projects ──────────────────────────────────────────────────────────────
    'projects' => [
        'new_project'       => 'مشروع جديد',
        'results'           => 'مشروع',
        'number'            => 'رقم المشروع',
        'title'             => 'عنوان المشروع',
        'type'              => 'النوع',
        'supervisor'        => 'المشرف',
        'co_supervisor'     => 'المشرف المشارك',
        'no_supervisor'     => 'لا يوجد مشرف',
        'students'          => 'طلاب',
        'progress'          => 'نسبة الإنجاز',
        'status'            => 'الحالة',
        'department'        => 'القسم',
        'semester'          => 'الفصل الدراسي',
        'description'       => 'وصف المشروع',
        'objectives'        => 'الأهداف',
        'expected_outcomes' => 'النتائج المتوقعة',
        'methodology'       => 'المنهجية',
        'tools'             => 'الأدوات والتقنيات',
        'milestones'        => 'مراحل المشروع',
        'reports'           => 'التقارير',
        'files'             => 'الملفات المرفقة',
        'team'              => 'فريق العمل',
        'details'           => 'تفاصيل المشروع',
        'registered'        => 'تاريخ التسجيل',
        'start_date'        => 'تاريخ البدء',
        'end_date'          => 'تاريخ الانتهاء المتوقع',
        'defense_schedule'  => 'جدول المناقشة',
        'discussed'         => 'تمت مناقشته',
        'not_discussed'     => 'لم تتم مناقشته',
        'discussed_on'      => 'تمت المناقشة بتاريخ',
        'defense_on'        => 'موعد المناقشة',
        'no_projects'       => 'لا توجد مشاريع',
        'grade'             => 'الدرجة النهائية',
        'grade_letter'      => 'التقدير',
        'committee_notes'   => 'ملاحظات اللجنة',
        'actual_defense_date' => 'تاريخ المناقشة الفعلي',
        'mark_discussed'    => 'تأكيد المناقشة',
        'confirm_discussion'=> 'تأكيد اكتمال المناقشة',
        'reject_title'      => 'رفض المشروع',
        'rejection_reason_placeholder' => 'اذكر سبب الرفض بوضوح...',
        'year_level'        => 'السنة الدراسية',
        'year'              => 'السنة',
        'archived_at'       => 'تاريخ الأرشفة',
    ],

    // ── Project Types ─────────────────────────────────────────────────────────
    'project_type' => [
        'graduation' => 'مشروع تخرج',
        'semester'   => 'مشروع فصلي',
        'year4'      => 'مشروع السنة الرابعة',
        'research'   => 'بحث علمي',
    ],

    // ── Status ────────────────────────────────────────────────────────────────
    'status' => [
        'pending'      => 'في الانتظار',
        'approved'     => 'معتمد',
        'rejected'     => 'مرفوض',
        'in_progress'  => 'قيد التنفيذ',
        'submitted'    => 'تم التسليم',
        'under_review' => 'تحت المراجعة',
        'defended'     => 'تمت المناقشة',
        'archived'     => 'مؤرشف',
        'cancelled'    => 'ملغى',
        'taken'        => 'مُختار',
        'draft'        => 'مسودة',
        'reviewed'     => 'تمت المراجعة',
    ],

    // ── Roles ─────────────────────────────────────────────────────────────────
    'roles' => [
        'admin'            => 'مدير النظام',
        'supervisor'       => 'مشرف',
        'coordinator'      => 'منسق',
        'committee_member' => 'عضو لجنة',
        'student'          => 'طالب',
        'leader'           => 'قائد الفريق',
    ],

    // ── Semester ──────────────────────────────────────────────────────────────
    'semester' => [
        'first'  => 'الفصل الأول',
        'second' => 'الفصل الثاني',
        'summer' => 'الفصل الصيفي',
    ],

    // ── Defense ───────────────────────────────────────────────────────────────
    'defense' => [
        'scheduled'  => 'مجدولة',
        'confirmed'  => 'مؤكدة',
        'postponed'  => 'مؤجلة',
        'cancelled'  => 'ملغاة',
        'completed'  => 'مكتملة',
    ],

    // ── Ideas ─────────────────────────────────────────────────────────────────
    'ideas' => [
        'new_idea' => 'فكرة جديدة',
    ],

    // ── Reports ───────────────────────────────────────────────────────────────
    'reports' => [
        'new'        => 'تقرير جديد',
        'no_reports' => 'لا توجد تقارير',
    ],

    // ── Milestone ─────────────────────────────────────────────────────────────
    'milestone' => [
        'pending'     => 'لم تبدأ',
        'in_progress' => 'جارية',
        'completed'   => 'مكتملة',
        'overdue'     => 'متأخرة',
    ],

    // ── Schedule ──────────────────────────────────────────────────────────────
    'schedule' => [
        'rescheduled_from' => 'أُعيد جدولته من تاريخ',
    ],

    // ── Messages ─────────────────────────────────────────────────────────────
    'messages' => [
        'project_created'    => 'تم إنشاء المشروع بنجاح وهو في انتظار الاعتماد.',
        'project_updated'    => 'تم تحديث المشروع بنجاح.',
        'project_approved'   => 'تم اعتماد المشروع بنجاح.',
        'project_rejected'   => 'تم رفض المشروع.',
        'project_discussed'  => 'تم تسجيل نتيجة المناقشة بنجاح.',
        'project_archived'   => 'تم أرشفة المشروع بنجاح.',
        'project_deleted'    => 'تم حذف المشروع.',
        'idea_created'       => 'تم إرسال الفكرة وهي في انتظار الاعتماد.',
        'idea_updated'       => 'تم تحديث الفكرة بنجاح.',
        'idea_approved'      => 'تمت الموافقة على الفكرة.',
        'idea_rejected'      => 'تم رفض الفكرة.',
        'idea_deleted'       => 'تم حذف الفكرة.',
        'committee_created'  => 'تم إنشاء اللجنة بنجاح.',
        'committee_completed'=> 'تم تسجيل اكتمال اجتماع اللجنة.',
        'committee_deleted'  => 'تم حذف اللجنة.',
        'schedule_created'   => 'تم جدولة موعد المناقشة بنجاح.',
        'schedule_postponed' => 'تم تأجيل المناقشة وجدولة موعد جديد.',
        'schedule_cancelled' => 'تم إلغاء المناقشة.',
        'schedule_completed' => 'تم تسجيل اكتمال المناقشة.',
        'report_submitted'   => 'تم رفع التقرير بنجاح.',
        'report_reviewed'    => 'تم مراجعة التقرير.',
        'user_created'       => 'تم إنشاء المستخدم بنجاح.',
        'user_updated'       => 'تم تحديث بيانات المستخدم.',
        'user_deleted'       => 'تم حذف المستخدم.',
        'profile_updated'    => 'تم تحديث الملف الشخصي بنجاح.',
        'password_changed'   => 'تم تغيير كلمة المرور بنجاح.',
        'already_processed'  => 'هذا الطلب تمت معالجته مسبقاً.',
    ],

    // ── Common ────────────────────────────────────────────────────────────────
    'common' => [
        'search'        => 'بحث',
        'all'           => 'الكل',
        'all_statuses'  => 'جميع الحالات',
        'all_types'     => 'جميع الأنواع',
        'all_semesters' => 'جميع الفصول',
        'view'          => 'عرض',
        'view_all'      => 'عرض الكل',
        'edit'          => 'تعديل',
        'delete'        => 'حذف',
        'approve'       => 'اعتماد',
        'reject'        => 'رفض',
        'cancel'        => 'إلغاء',
        'save'          => 'حفظ',
        'back'          => 'رجوع',
        'archive'       => 'أرشفة',
        'at'            => 'في',
        'yes'           => 'نعم',
        'no'            => 'لا',
    ],

    // ── Log Actions ───────────────────────────────────────────────────────────
    'log' => [
        'project_created'  => 'تم إنشاء مشروع جديد',
        'project_updated'  => 'تم تحديث بيانات المشروع',
        'project_approved' => 'تم اعتماد المشروع',
        'project_rejected' => 'تم رفض المشروع',
        'project_discussed'=> 'تمت مناقشة المشروع',
        'project_archived' => 'تمت أرشفة المشروع',
        'project_deleted'  => 'تم حذف المشروع',
    ],
];