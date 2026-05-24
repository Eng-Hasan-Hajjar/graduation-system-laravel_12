<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // ── Academic Years ────────────────────────────────────────────────────
        DB::table('academic_years')->insert([
            [
                'university_id' => 1,
                'name_ar'       => '2022-2023',
                'name_en'       => '2022-2023',
                'year_start'    => 2022,
                'year_end'      => 2023,
                'start_date'    => '2022-09-01',
                'end_date'      => '2023-06-30',
                'is_current'    => false,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'university_id' => 1,
                'name_ar'       => '2023-2024',
                'name_en'       => '2023-2024',
                'year_start'    => 2023,
                'year_end'      => 2024,
                'start_date'    => '2023-09-01',
                'end_date'      => '2024-06-30',
                'is_current'    => false,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'university_id' => 1,
                'name_ar'       => '2024-2025',
                'name_en'       => '2024-2025',
                'year_start'    => 2024,
                'year_end'      => 2025,
                'start_date'    => '2024-09-01',
                'end_date'      => '2025-06-30',
                'is_current'    => true,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);

        // ── Semesters ─────────────────────────────────────────────────────────
        DB::table('semesters')->insert([
            // 2022-2023
            [
                'academic_year_id'           => 1,
                'name_ar'                    => 'الفصل الأول 2022-2023',
                'name_en'                    => 'First Semester 2022-2023',
                'type'                       => 'first',
                'start_date'                 => '2022-09-01',
                'end_date'                   => '2023-01-15',
                'project_registration_start' => '2022-09-01',
                'project_registration_end'   => '2022-09-30',
                'project_submission_start'   => '2022-12-01',
                'project_submission_end'     => '2023-01-10',
                'is_current'                 => false,
                'is_active'                  => true,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'academic_year_id'           => 1,
                'name_ar'                    => 'الفصل الثاني 2022-2023',
                'name_en'                    => 'Second Semester 2022-2023',
                'type'                       => 'second',
                'start_date'                 => '2023-02-01',
                'end_date'                   => '2023-06-30',
                'project_registration_start' => '2023-02-01',
                'project_registration_end'   => '2023-02-28',
                'project_submission_start'   => '2023-05-01',
                'project_submission_end'     => '2023-06-15',
                'is_current'                 => false,
                'is_active'                  => true,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            // 2023-2024
            [
                'academic_year_id'           => 2,
                'name_ar'                    => 'الفصل الأول 2023-2024',
                'name_en'                    => 'First Semester 2023-2024',
                'type'                       => 'first',
                'start_date'                 => '2023-09-01',
                'end_date'                   => '2024-01-15',
                'project_registration_start' => '2023-09-01',
                'project_registration_end'   => '2023-09-30',
                'project_submission_start'   => '2023-12-01',
                'project_submission_end'     => '2024-01-10',
                'is_current'                 => false,
                'is_active'                  => true,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'academic_year_id'           => 2,
                'name_ar'                    => 'الفصل الثاني 2023-2024',
                'name_en'                    => 'Second Semester 2023-2024',
                'type'                       => 'second',
                'start_date'                 => '2024-02-01',
                'end_date'                   => '2024-06-30',
                'project_registration_start' => '2024-02-01',
                'project_registration_end'   => '2024-02-28',
                'project_submission_start'   => '2024-05-01',
                'project_submission_end'     => '2024-06-15',
                'is_current'                 => false,
                'is_active'                  => true,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            // 2024-2025 (Current)
            [
                'academic_year_id'           => 3,
                'name_ar'                    => 'الفصل الأول 2024-2025',
                'name_en'                    => 'First Semester 2024-2025',
                'type'                       => 'first',
                'start_date'                 => '2024-09-01',
                'end_date'                   => '2025-01-15',
                'project_registration_start' => '2024-09-01',
                'project_registration_end'   => '2024-09-30',
                'project_submission_start'   => '2024-12-01',
                'project_submission_end'     => '2025-01-10',
                'is_current'                 => false,
                'is_active'                  => true,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'academic_year_id'           => 3,
                'name_ar'                    => 'الفصل الثاني 2024-2025',
                'name_en'                    => 'Second Semester 2024-2025',
                'type'                       => 'second',
                'start_date'                 => '2025-02-01',
                'end_date'                   => '2025-06-30',
                'project_registration_start' => '2025-02-01',
                'project_registration_end'   => '2025-02-28',
                'project_submission_start'   => '2025-05-01',
                'project_submission_end'     => '2025-06-15',
                'is_current'                 => true,
                'is_active'                  => true,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
        ]);
    }
}