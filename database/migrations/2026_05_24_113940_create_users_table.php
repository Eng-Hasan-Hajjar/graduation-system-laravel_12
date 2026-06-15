<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول users بدون Foreign Keys أولاً
     * سيتم إضافة الـ FK في migration منفصل لاحقاً (000010)
     *
     * ملاحظة: نحذف الجداول الثلاثة أولاً (dropIfExists) بشكل صريح
     * لضمان عدم فشل migrate:fresh بسبب أي تعارض من migrations
     * افتراضية أو سابقة (مثل sessions / password_reset_tokens / users)
     */
    public function up(): void
    {
        // حذف صريح قبل الإنشاء لضمان عدم وجود تعارض
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // بدون constrained() هنا — سنضيفها لاحقاً في migration 000010
            $table->unsignedBigInteger('university_id')->nullable();
            $table->unsignedBigInteger('college_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();

            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('national_id')->nullable()->unique();
            $table->string('student_id')->nullable()->unique();
            $table->string('employee_id')->nullable()->unique();
            $table->enum('role', ['admin','supervisor','coordinator','committee_member','student'])
                  ->default('student');
            $table->string('academic_rank')->nullable();
            $table->string('specialization_ar')->nullable();
            $table->string('specialization_en')->nullable();
            $table->integer('academic_year')->nullable();
            $table->string('avatar')->nullable();
            $table->string('password');
            $table->enum('status', ['active','inactive','suspended'])->default('active');
            $table->string('lang_preference')->default('ar');
            $table->string('theme_preference')->default('light');
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};