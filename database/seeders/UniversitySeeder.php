<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('universities')->insert([
            [
                'name_ar'    => 'جامعة الملك عبدالعزيز',
                'name_en'    => 'King Abdulaziz University',
                'website'    => 'https://www.kau.edu.sa',
                'email'      => 'info@kau.edu.sa',
                'phone'      => '+966-12-695-2000',
                'address_ar' => 'جدة، المملكة العربية السعودية',
                'address_en' => 'Jeddah, Saudi Arabia',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}