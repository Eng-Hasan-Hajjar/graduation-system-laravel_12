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
        Schema::create('project_ideas', function (Blueprint $table) {
            $table->id();
              $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proposed_by')->constrained('users')->cascadeOnDelete(); // المشرف المقترح
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
 
            // العنوان والوصف
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('description_ar');
            $table->text('description_en')->nullable();
            $table->text('objectives_ar')->nullable(); // الأهداف
            $table->text('objectives_en')->nullable();
            $table->text('expected_outcomes_ar')->nullable(); // النتائج المتوقعة
            $table->text('expected_outcomes_en')->nullable();
 
            // التصنيف
            $table->enum('project_type', ['graduation', 'semester', 'year4', 'research'])
                  ->default('graduation'); // تخرج / فصلي / رابعة / بحث
            $table->string('category_ar')->nullable(); // الفئة/التخصص
            $table->string('category_en')->nullable();
            $table->json('tags')->nullable(); // وسوم
 
            // الأدوات والتقنيات
            $table->json('tools')->nullable(); // الأدوات مثل Laravel, React...
            $table->json('technologies')->nullable();
            $table->text('requirements_ar')->nullable(); // المتطلبات المسبقة
            $table->text('requirements_en')->nullable();
 
            // عدد الطلاب
            $table->integer('min_students')->default(1);
            $table->integer('max_students')->default(3);
 
            // الحالة
            $table->enum('status', ['pending', 'approved', 'rejected', 'taken', 'archived'])
                  ->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
 
            // الأولوية
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('is_featured')->default(false); // مميز
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_ideas');
    }
};
