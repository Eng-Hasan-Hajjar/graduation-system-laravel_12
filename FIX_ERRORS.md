# 🔧 دليل إصلاح الأخطاء خطوة بخطوة

---

## ✅ الخطوة 1: المشكلة الأولى - `laravel/ui` غير مثبت

### الخطأ:
```
RuntimeException: In order to use the Auth::routes() method, please install the laravel/ui package.
```

### الحل - الخيار الأول (الأسهل): تثبيت laravel/ui

```bash
composer require laravel/ui
php artisan ui bootstrap --auth
npm install && npm run build
```

### الحل - الخيار الثاني: بدون laravel/ui (كما في ملفاتنا المحدّثة)

نسخ الملفات المحدثة التالية إلى مشروعك:
- `routes/web.php` ← تم حذف `Auth::routes()` واستبداله
- `app/Http/Controllers/Auth/LoginController.php` ← controller يدوي
- `resources/views/auth/login.blade.php` ← صفحة تسجيل دخول يدوية

---

## ✅ الخطوة 2: نسخ ملفات المشروع

انسخ جميع الملفات من مجلد الإخراج إلى مجلد مشروع Laravel.

**هيكل الملفات المطلوب:**
```
graduation-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php          ← جديد
│   │   │   ├── DashboardController.php
│   │   │   ├── ProjectController.php
│   │   │   ├── ProjectIdeaController.php
│   │   │   ├── CommitteeController.php          ← جديد (مفصول)
│   │   │   ├── DefenseScheduleController.php    ← جديد (مفصول)
│   │   │   ├── ReportController.php             ← جديد (مفصول)
│   │   │   ├── UserController.php               ← جديد (مفصول)
│   │   │   └── AdminControllers.php             ← يحتوي AcademicYear, Department, Setting
│   │   └── Middleware/
│   │       ├── SetLocale.php
│   │       └── CheckRole.php
│   ├── Models/
│   │   ├── University.php
│   │   ├── College.php
│   │   ├── Department.php
│   │   ├── User.php
│   │   ├── AcademicYear.php
│   │   ├── Semester.php
│   │   ├── ProjectIdea.php
│   │   ├── Project.php
│   │   ├── Committee.php
│   │   ├── ProjectMilestone.php
│   │   ├── Setting.php                          ← جديد
│   │   └── ProjectModels.php (ProjectReport, ProjectFile, DefenseSchedule, SupervisorEvaluation)
│   ├── Policies/
│   │   └── ProjectPolicy.php
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── AuthServiceProvider.php
├── database/
│   ├── migrations/  (9 ملفات)
│   └── seeders/     (9 ملفات)
├── lang/
│   ├── ar/
│   │   ├── auth.php     ← جديد
│   │   └── messages.php
│   └── en/
│       ├── auth.php     ← جديد
│       └── messages.php
├── resources/views/
│   ├── auth/
│   │   └── login.blade.php    ← جديد
│   ├── layouts/app.blade.php
│   ├── dashboard/admin.blade.php
│   └── projects/
│       ├── index.blade.php
│       └── show.blade.php
└── routes/web.php    ← محدّث (بدون Auth::routes())
```

---

## ✅ الخطوة 3: فصل الـ Models من ProjectModels.php

افتح الملف `app/Models/ProjectModels.php` وأنشئ ملفاً منفصلاً لكل Model:

### ProjectReport.php
```bash
# انسخ class ProjectReport من ProjectModels.php إلى:
app/Models/ProjectReport.php
# غيّر namespace App\Models; يبقى كما هو
```

### ProjectFile.php
```bash
app/Models/ProjectFile.php
```

### DefenseSchedule.php
```bash
app/Models/DefenseSchedule.php
```

### SupervisorEvaluation.php
```bash
app/Models/SupervisorEvaluation.php
```

**أو** يمكنك إبقاء الملف المدمج وإضافة هذا في `composer.json`:
```json
"autoload": {
    "psr-4": {
        "App\\": "app/"
    },
    "files": [
        "app/Models/ProjectModels.php"
    ]
}
```

ثم نفّذ:
```bash
composer dump-autoload
```

---

## ✅ الخطوة 4: فصل الـ Controllers المدمجة

### CommitteeDefenseController.php يحتوي CommitteeController + DefenseScheduleController
لدينا الآن ملفات مفصولة، استخدمها بدلاً من الملف المدمج.

### UserReportController.php يحتوي ReportController + UserController
لدينا الآن ملفات مفصولة.

### AdminControllers.php يحتوي AcademicYearController + DepartmentController + SettingController
هذا الملف يعمل كما هو لأن PHP يقبل عدة classes في ملف واحد.

---

## ✅ الخطوة 5: إعداد .env

```env
APP_NAME="نظام إدارة مشاريع التخرج"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en
APP_TIMEZONE=Asia/Riyadh

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=graduation_system
DB_USERNAME=root
DB_PASSWORD=YOUR_PASSWORD_HERE

FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
CACHE_STORE=file
```

---

## ✅ الخطوة 6: تشغيل الأوامر بالترتيب

```bash
# 1. توليد مفتاح التطبيق (إذا لم تفعل)
php artisan key:generate

# 2. تنظيف الكاش
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 3. تشغيل Migrations مع Seeders
php artisan migrate:fresh --seed

# 4. إنشاء Storage Link
php artisan storage:link

# 5. تشغيل السيرفر
php artisan serve
```

---

## ❓ أخطاء شائعة أخرى وحلولها

### خطأ: Class not found
```
Error: Class 'App\Http\Controllers\CommitteeController' not found
```
**الحل:**
```bash
composer dump-autoload
```

### خطأ: View not found
```
View [dashboard.admin] not found
```
**الحل:** تأكد أن مجلد `resources/views/dashboard/` موجود وفيه `admin.blade.php`

### خطأ: SQLSTATE foreign key constraint
```
SQLSTATE[23000]: Integrity constraint violation
```
**الحل:** تأكد أن ترتيب الـ Seeders صحيح في `DatabaseSeeder.php`

### خطأ: Gate undefined
```
Gate [manage-committee] not defined
```
**الحل:** تأكد أن `AuthServiceProvider` مسجّل في `bootstrap/app.php`:
```php
->withProviders([
    App\Providers\AuthServiceProvider::class,
])
```

### خطأ: Auth middleware redirect
**الحل:** تأكد من وجود route باسم `login` وهو موجود في `web.php`

---

## 🎯 ترتيب تشغيل سليم 100%

```bash
# 1
composer install

# 2
cp .env.example .env
php artisan key:generate

# 3 - عدّل .env بمعلومات قاعدة البيانات

# 4
composer dump-autoload

# 5
php artisan migrate:fresh --seed

# 6
php artisan storage:link

# 7
php artisan config:clear && php artisan cache:clear

# 8
php artisan serve
```

ثم افتح: http://localhost:8000
وسجّل دخول بـ: admin@kau.edu.sa / password