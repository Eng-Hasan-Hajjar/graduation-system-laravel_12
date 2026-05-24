# 🎓 نظام إدارة مشاريع التخرج الذكي
## دليل التثبيت الكامل - Terminal Commands

---

## ⚙️ المتطلبات
- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 18.x & npm
- MySQL >= 8.0
- Git

---

## 📁 الخطوة 1: إنشاء المشروع

```bash
# إنشاء مشروع Laravel 12 جديد
composer create-project laravel/laravel graduation-system "12.*"

# الدخول إلى مجلد المشروع
cd graduation-system
```

---

## 📦 الخطوة 2: تثبيت الحزم المطلوبة

```bash
# حزمة واجهة المستخدم (Bootstrap)
composer require laravel/ui

# إنشاء واجهة Bootstrap مع المصادقة
php artisan ui bootstrap --auth

# تثبيت اعتماديات Node
npm install

# بناء الملفات (Development)
npm run dev

# أو بناء للإنتاج
npm run build
```

---

## 🗄️ الخطوة 3: إعداد قاعدة البيانات

```bash
# أولاً: أنشئ قاعدة بيانات في MySQL
mysql -u root -p
```

```sql
CREATE DATABASE graduation_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'grad_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';
GRANT ALL PRIVILEGES ON graduation_system.* TO 'grad_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 🔧 الخطوة 4: إعداد ملف .env

```bash
# نسخ ملف الإعدادات
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate
```

**عدّل ملف `.env` بالبيانات التالية:**

```env
APP_NAME="نظام إدارة مشاريع التخرج"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=graduation_system
DB_USERNAME=grad_user
DB_PASSWORD=StrongPassword123!

FILESYSTEM_DISK=public
```

---

## 📂 الخطوة 5: نسخ ملفات المشروع

انسخ جميع الملفات من هذا المجلد إلى مجلد مشروع Laravel:

```bash
# بنية الملفات المطلوبة:
graduation-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProjectController.php
│   │   │   ├── ProjectIdeaController.php
│   │   │   ├── CommitteeDefenseController.php  ← يحتوي CommitteeController & DefenseScheduleController
│   │   │   └── UserReportController.php        ← يحتوي ReportController & UserController
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
│   │   ├── ProjectModels.php  ← يحتوي ProjectReport, ProjectFile, DefenseSchedule, SupervisorEvaluation
│   │   └── ActivityLog.php
│   ├── Policies/
│   │   └── ProjectPolicy.php
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── AuthServiceProvider.php
├── bootstrap/
│   └── app.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_universities_table.php
│   │   ├── 2024_01_01_000002_create_colleges_departments_table.php
│   │   ├── 2024_01_01_000003_create_users_table.php
│   │   ├── 2024_01_01_000004_create_academic_years_table.php
│   │   ├── 2024_01_01_000005_create_project_ideas_table.php
│   │   ├── 2024_01_01_000006_create_projects_table.php
│   │   ├── 2024_01_01_000007_create_project_students_committees_table.php
│   │   ├── 2024_01_01_000008_create_project_details_table.php
│   │   └── 2024_01_01_000009_create_notifications_logs_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UniversitySeeder.php
│       ├── CollegeDepartmentSeeder.php
│       ├── UserSeeder.php
│       ├── AcademicYearSeeder.php
│       ├── ProjectIdeaSeeder.php
│       ├── ProjectSeeder.php
│       ├── CommitteeSeeder.php
│       └── DefenseScheduleSeeder.php  ← يحتوي DefenseScheduleSeeder & SettingSeeder
├── lang/
│   ├── ar/messages.php
│   └── en/messages.php
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── dashboard/admin.blade.php
│   └── projects/
│       ├── index.blade.php
│       └── show.blade.php
└── routes/web.php
```

---

## ⚠️ الخطوة 6: فصل Controllers من الملفات المجمّعة

بعض الـ Controllers موجودة في ملف واحد، يجب فصلها:

```bash
# من CommitteeDefenseController.php استخرج:
# - CommitteeController   → app/Http/Controllers/CommitteeController.php
# - DefenseScheduleController → app/Http/Controllers/DefenseScheduleController.php

# من UserReportController.php استخرج:
# - ReportController → app/Http/Controllers/ReportController.php
# - UserController   → app/Http/Controllers/UserController.php

# من ProjectModels.php استخرج كل Model إلى ملف منفصل:
# - ProjectReport.php
# - ProjectFile.php
# - DefenseSchedule.php
# - SupervisorEvaluation.php
```

---

## 🚀 الخطوة 7: تشغيل Migrations

```bash
# تشغيل جميع الـ Migrations
php artisan migrate

# في حالة وجود خطأ وتريد البدء من جديد
php artisan migrate:fresh
```

---

## 🌱 الخطوة 8: تشغيل Seeders

```bash
# تشغيل جميع الـ Seeders
php artisan db:seed

# أو كل seeder بشكل منفرد:
php artisan db:seed --class=UniversitySeeder
php artisan db:seed --class=CollegeDepartmentSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=AcademicYearSeeder
php artisan db:seed --class=ProjectIdeaSeeder
php artisan db:seed --class=ProjectSeeder
php artisan db:seed --class=CommitteeSeeder
php artisan db:seed --class=DefenseScheduleSeeder

# إعادة Migration مع Seeder في أمر واحد
php artisan migrate:fresh --seed
```

---

## 🔗 الخطوة 9: إنشاء Storage Link

```bash
php artisan storage:link
```

---

## 🧹 الخطوة 10: تنظيف الكاش

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## ▶️ الخطوة 11: تشغيل المشروع

```bash
php artisan serve
```

افتح المتصفح على: **http://localhost:8000**

---

## 👤 بيانات الدخول

| الدور         | البريد الإلكتروني            | كلمة المرور |
|---------------|------------------------------|-------------|
| مدير النظام   | admin@kau.edu.sa             | password    |
| منسق          | coordinator@kau.edu.sa       | password    |
| مشرف (د.أحمد) | ahmed.ghamdi@kau.edu.sa      | password    |
| مشرف (د.فاطمة)| fatima.zahrani@kau.edu.sa    | password    |
| مشرف (د.خالد) | khalid.otaibi@kau.edu.sa     | password    |
| مشرف (د.منى)  | mona.shahri@kau.edu.sa       | password    |
| عضو لجنة      | qahtani@kau.edu.sa           | password    |
| طالب (محمد)   | s.mohammed@kau.edu.sa        | password    |
| طالب (سلمى)   | s.salma@kau.edu.sa           | password    |
| طالب (نورة)   | s.noura@kau.edu.sa           | password    |
| طالب (ليلى)   | s.layla@kau.edu.sa           | password    |

---

## 📊 البيانات التجريبية الموجودة

### المشاريع:
| رقم المشروع   | العنوان                              | الحالة          | السنة     |
|---------------|--------------------------------------|-----------------|-----------|
| GRAD-2022-0001| تصنيف المشاعر في التغريدات العربية   | مؤرشف (95 A+)  | 2022-2023 |
| GRAD-2023-0001| نظام إدارة المستشفيات الإلكتروني     | مؤرشف (91.5 A) | 2022-2023 |
| GRAD-2024-0001| تتبع الحضور بالتعرف على الوجه        | مناقَش (88 B+)  | 2023-2024 |
| GRAD-2025-0001| نظام إدارة مشاريع التخرج            | قيد التنفيذ 75%| 2024-2025 |
| GRAD-2025-0002| توصية المقررات بالذكاء الاصطناعي     | معلق            | 2024-2025 |
| SEM-2025-0001 | حجز مواعيد المرافق الجامعية          | معتمد 55%      | 2024-2025 |

---

## 🛠️ أوامر مفيدة إضافية

```bash
# عرض جميع الـ Routes
php artisan route:list

# إنشاء Controller جديد
php artisan make:controller AcademicYearController --resource

# إنشاء Migration جديد
php artisan make:migration create_example_table

# إنشاء Model مع Migration و Seeder
php artisan make:model Example -ms

# تشغيل Queue (لو أضفت notifications)
php artisan queue:work

# فحص حالة النظام
php artisan about
```

---

## 🌐 تغيير اللغة

اللغة الافتراضية **عربية**. للتغيير إلى الإنجليزية:
- من الواجهة: انقر زر `EN` في الـ Topbar
- من الـ URL: `/lang/en` أو `/lang/ar`

---

## 🎨 تغيير السمة (Dark/Light)

انقر أيقونة القمر/الشمس في الـ Topbar لتبديل السمة.
يتم حفظ التفضيل في قاعدة البيانات لكل مستخدم.

---

## 📝 ملاحظات مهمة

1. **الـ Controllers المجمّعة**: بعض الملفات تحتوي على أكثر من Controller، افصلها إلى ملفات منفصلة.
2. **الـ Models المجمّعة**: ملف `ProjectModels.php` يحتوي على 4 Models، افصلها.
3. **الصلاحيات**: استخدم الـ `Gate::define` في `AuthServiceProvider` أو أضف `Spatie/Permission` لنظام أكثر مرونة.
4. **الإشعارات**: جاهزة للتفعيل - فعّل Queue وأضف Notification Classes.
5. **المزيد من الـ Views**: الـ Views الأساسية موجودة، يمكن إضافة باقي الـ Views بنفس الأسلوب.