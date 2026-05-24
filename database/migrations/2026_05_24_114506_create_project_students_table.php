<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // طلاب المشروع
        Schema::create('project_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['leader', 'member'])->default('member'); // قائد / عضو
            $table->date('joined_at')->nullable();
            $table->enum('status', ['active', 'withdrawn', 'transferred'])->default('active');
            $table->decimal('individual_grade', 5, 2)->nullable(); // درجة الطالب الفردية
            $table->timestamps();

            $table->unique(['project_id', 'student_id']);
        });

        // لجان المناقشة
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->timestamp('scheduled_at')->nullable();   // موعد المناقشة المجدول
            $table->timestamp('actual_start_at')->nullable(); // بداية المناقشة الفعلية
            $table->timestamp('actual_end_at')->nullable();   // نهاية المناقشة الفعلية
            $table->string('location')->nullable();
            $table->string('room')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->text('notes_ar')->nullable();
            $table->text('notes_en')->nullable();
            $table->timestamps();
        });

        // أعضاء اللجنة
        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['chair', 'member', 'external'])->default('member'); // رئيس / عضو / خارجي
            $table->boolean('attended')->default(false);
            $table->decimal('grade_given', 5, 2)->nullable(); // الدرجة التي أعطاها العضو
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['committee_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_members');
        Schema::dropIfExists('committees');
        Schema::dropIfExists('project_students');
    }
};