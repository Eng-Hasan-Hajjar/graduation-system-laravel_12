<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // الإشعارات
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // سجل النشاطات
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, updated, deleted, approved, rejected...
            $table->string('model_type')->nullable(); // Project, Report...
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // إعدادات النظام
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, json, integer
            $table->string('group')->default('general');
            $table->string('label_ar')->nullable();
            $table->string('label_en')->nullable();
            $table->timestamps();
        });

        // تقييمات المشرف للطلاب
        Schema::create('supervisor_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('commitment_grade', 5, 2)->nullable();   // الالتزام
            $table->decimal('technical_grade', 5, 2)->nullable();    // المستوى التقني
            $table->decimal('presentation_grade', 5, 2)->nullable(); // العرض والتقديم
            $table->decimal('report_grade', 5, 2)->nullable();       // التقرير
            $table->decimal('total_grade', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->date('evaluation_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisor_evaluations');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifications');
    }
};