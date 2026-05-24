<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UniversitySeeder::class,
            CollegeDepartmentSeeder::class,
            UserSeeder::class,
            AcademicYearSeeder::class,
            ProjectIdeaSeeder::class,
            ProjectSeeder::class,
            CommitteeSeeder::class,
            DefenseScheduleSeeder::class,
            SettingSeeder::class,
        ]);
    }
}