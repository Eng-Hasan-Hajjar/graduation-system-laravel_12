<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── Index (Admin only) ───────────────────────────────────────────────────
    public function index(Request $request)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isCoordinator(), 403);

        $users = User::with(['university', 'department'])
            ->when($request->role,   fn($q) => $q->where('role', $request->role))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('name_ar', 'like', "%{$request->search}%")
                       ->orWhere('email',  'like', "%{$request->search}%")
                       ->orWhere('student_id', 'like', "%{$request->search}%")
                )
            )
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────
    public function create()
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $departments = Department::where('is_active', true)->get();
        $colleges    = College::where('is_active', true)->get();
        return view('users.create', compact('departments', 'colleges'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:8|confirmed',
            'role'          => 'required|in:admin,supervisor,coordinator,committee_member,student',
            'department_id' => 'nullable|exists:departments,id',
            'college_id'    => 'nullable|exists:colleges,id',
            'student_id'    => 'nullable|string|unique:users',
            'employee_id'   => 'nullable|string|unique:users',
            'academic_rank' => 'nullable|string|max:100',
            'phone'         => 'nullable|string|max:20',
        ]);

        $data['university_id'] = 1;
        $data['status']        = 'active';

        User::create($data);

        return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────
    public function show(User $user)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isCoordinator() || Auth::id() === $user->id, 403);
        $user->load(['department.college', 'studentProjects', 'supervisedProjects', 'committees']);
        return view('users.show', compact('user'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(User $user)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $departments = Department::where('is_active', true)->get();
        $colleges    = College::where('is_active', true)->get();
        return view('users.edit', compact('user', 'departments', 'colleges'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'role'          => 'required|in:admin,supervisor,coordinator,committee_member,student',
            'department_id' => 'nullable|exists:departments,id',
            'status'        => 'required|in:active,inactive,suspended',
            'academic_rank' => 'nullable|string|max:100',
            'phone'         => 'nullable|string|max:20',
        ]);

        $user->update($data);
        return back()->with('success', 'تم تحديث بيانات المستخدم');
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────
    public function destroy(User $user)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        abort_if($user->id === Auth::id(), 403, 'لا يمكن حذف حسابك');
        $user->update(['status' => 'inactive']);
        return redirect()->route('users.index')->with('success', 'تم تعطيل المستخدم');
    }

    // ─── Profile ──────────────────────────────────────────────────────────────
    public function profile()
    {
        $user = Auth::user()->load(['department.college.university']);
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'avatar'  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);
        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|min:8|confirmed',
        ]);

        Auth::user()->update(['password' => $request->password]);
        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    public function updatePreferences(Request $request)
    {
        $data = [];
        if ($request->has('lang'))  $data['lang_preference']  = $request->lang;
        if ($request->has('theme')) $data['theme_preference'] = $request->theme;

        if (!empty($data)) {
            Auth::user()->update($data);
        }

        return back();
    }
}