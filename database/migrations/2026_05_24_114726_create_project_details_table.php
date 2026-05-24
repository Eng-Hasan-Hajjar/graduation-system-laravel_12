<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // مراحل المشروع (Milestones)
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->date('due_date');
            $table->date('completed_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'overdue'])->default('pending');
            $table->integer('order')->default(0);
            $table->integer('weight_percentage')->default(0); // الوزن من إجمالي الدرجة
            $table->timestamps();
        });

        // التقارير الدورية
        Schema::create('project_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('content_ar');
            $table->text('content_en')->nullable();
            $table->enum('report_type', [
                'weekly', 'monthly', 'milestone', 'final', 'other'
            ])->default('weekly');
            $table->integer('week_number')->nullable();
            $table->date('report_date');
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected'])
                  ->default('draft');
            $table->text('supervisor_feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->timestamps();
        });

        // الملفات المرفقة
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('report_id')->nullable()->constrained('project_reports')->nullOnDelete();
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // pdf, docx, zip...
            $table->bigInteger('file_size')->nullable(); // bytes
            $table->enum('category', [
                'report', 'presentation', 'source_code', 'documentation',
                'proposal', 'final_report', 'other'
            ])->default('other');
            $table->text('description')->nullable();
            $table->boolean('is_final')->default(false); // هل هو الإصدار النهائي؟
            $table->integer('version')->default(1);
            $table->timestamps();
        });

        // جدول مناقشات المشاريع (Schedule)
        Schema::create('defense_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('committee_id')->nullable()->constrained()->nullOnDelete();
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->string('location')->nullable();
            $table->string('room')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', [
                'scheduled',   // مجدولة
                'confirmed',   // مؤكدة
                'postponed',   // مؤجلة
                'cancelled',   // ملغاة
                'completed',   // مكتملة
            ])->default('scheduled');
            $table->text('postpone_reason')->nullable();
            $table->date('new_scheduled_date')->nullable(); // التاريخ الجديد بعد التأجيل
            $table->time('new_scheduled_time')->nullable();
            $table->boolean('notified_students')->default(false);
            $table->boolean('notified_supervisors')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defense_schedules');
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('project_reports');
        Schema::dropIfExists('project_milestones');
    }
};