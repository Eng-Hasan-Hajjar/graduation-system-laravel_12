<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollegeDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // ── Colleges ─────────────────────────────────────────────────────────
        $colleges = [
            ['name_ar' => 'كلية الحاسبات وتقنية المعلومات', 'name_en' => 'College of Computing & Information Technology', 'code' => 'CCIT'],
            ['name_ar' => 'كلية الهندسة',                   'name_en' => 'College of Engineering',                         'code' => 'ENG'],
            ['name_ar' => 'كلية العلوم',                     'name_en' => 'College of Science',                             'code' => 'SCI'],
        ];

        foreach ($colleges as $college) {
            DB::table('colleges')->insert(array_merge($college, [
                'university_id' => 1,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]));
        }

        // ── Departments ───────────────────────────────────────────────────────
        $departments = [
            // CCIT (college_id = 1)
            ['college_id' => 1, 'name_ar' => 'قسم علم الحاسب',              'name_en' => 'Computer Science',              'code' => 'CS'],
            ['college_id' => 1, 'name_ar' => 'قسم نظم المعلومات',           'name_en' => 'Information Systems',           'code' => 'IS'],
            ['college_id' => 1, 'name_ar' => 'قسم تقنية المعلومات',         'name_en' => 'Information Technology',        'code' => 'IT'],
            ['college_id' => 1, 'name_ar' => 'قسم الذكاء الاصطناعي',        'name_en' => 'Artificial Intelligence',       'code' => 'AI'],
            // Engineering (college_id = 2)
            ['college_id' => 2, 'name_ar' => 'قسم هندسة البرمجيات',         'name_en' => 'Software Engineering',          'code' => 'SE'],
            ['college_id' => 2, 'name_ar' => 'قسم هندسة الشبكات',           'name_en' => 'Network Engineering',           'code' => 'NE'],
            // Science (college_id = 3)
            ['college_id' => 3, 'name_ar' => 'قسم الرياضيات',               'name_en' => 'Mathematics',                   'code' => 'MATH'],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->insert(array_merge($dept, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}