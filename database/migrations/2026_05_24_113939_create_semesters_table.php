<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar'); // الفصل الأول، الثاني، الصيفي
            $table->string('name_en');
            $table->enum('type', ['first', 'second', 'summer'])->default('first');
            $table->date('start_date');
            $table->date('end_date');
            // مواعيد تسجيل المشاريع
            $table->date('project_registration_start')->nullable();
            $table->date('project_registration_end')->nullable();
            // مواعيد تقديم المشاريع
            $table->date('project_submission_start')->nullable();
            $table->date('project_submission_end')->nullable();
            $table->boolean('is_current')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
