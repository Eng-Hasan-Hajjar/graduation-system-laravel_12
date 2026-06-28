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
                'name_ar'    => 'جامعة  حلب',
                'name_en'    => ' Aleppo University',
                'website'    => 'https://www.kau.edu.sa',
                'email'      => 'info@kau.edu.sa',
                'phone'      => '+963-12-695-2000',
                'address_ar' => 'حلب سوريا   ',
                'address_en' => 'Aleppo, Syria ',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}