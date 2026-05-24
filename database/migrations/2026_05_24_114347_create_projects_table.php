<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_idea_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('co_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // بيانات المشروع
            $table->string('project_number')->unique(); // رقم المشروع
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('description_ar');
            $table->text('description_en')->nullable();
            $table->text('objectives_ar')->nullable();
            $table->text('objectives_en')->nullable();
            $table->text('expected_outcomes_ar')->nullable();
            $table->text('expected_outcomes_en')->nullable();
            $table->text('methodology_ar')->nullable(); // المنهجية
            $table->text('methodology_en')->nullable();

            // التصنيف
            $table->enum('project_type', ['graduation', 'semester', 'year4', 'research'])
                ->default('graduation');
            $table->integer('academic_year_level')->nullable(); // السنة (1-4)
            $table->json('tools')->nullable();
            $table->json('technologies')->nullable();

            // المواعيد المهمة
            $table->date('registration_date')->nullable();   // تاريخ التسجيل
            $table->date('start_date')->nullable();          // تاريخ البدء
            $table->date('expected_end_date')->nullable();   // تاريخ الانتهاء المتوقع
            $table->date('submission_date')->nullable();     // تاريخ التسليم الفعلي
            $table->date('defense_date')->nullable();        // تاريخ المناقشة المجدولة
            $table->date('actual_defense_date')->nullable(); // تاريخ المناقشة الفعلي
            $table->time('defense_time')->nullable();        // وقت المناقشة
            $table->string('defense_location')->nullable();  // مكان المناقشة
            $table->string('defense_room')->nullable();      // قاعة المناقشة

            // الحالة والاعتماد
            $table->enum('status', [
                'pending',       // في انتظار الاعتماد
                'approved',      // معتمد
                'rejected',      // مرفوض
                'in_progress',   // قيد التنفيذ
                'submitted',     // تم التسليم
                'under_review',  // تحت المراجعة
                'defended',      // تمت المناقشة
                'archived',      // مؤرشف
                'cancelled',     // ملغى
            ])->default('pending');

            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // بعد المناقشة
            $table->boolean('is_discussed')->default(false);  // هل تمت المناقشة؟
            $table->timestamp('discussed_at')->nullable();
            $table->decimal('final_grade', 5, 2)->nullable(); // الدرجة النهائية
            $table->enum('grade_letter', ['A+', 'A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F'])->nullable();
            $table->text('committee_notes_ar')->nullable();  // ملاحظات اللجنة
            $table->text('committee_notes_en')->nullable();

            // الأرشفة
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->text('archive_notes')->nullable();

            // إضافية
            $table->text('supervisor_notes')->nullable();
            $table->integer('progress_percentage')->default(0); // نسبة الإنجاز
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
