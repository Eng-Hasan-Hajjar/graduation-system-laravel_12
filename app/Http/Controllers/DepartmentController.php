<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\College;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('college')->orderBy('name_ar')->get();
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $colleges = College::where('is_active', true)->get();
        return view('departments.create', compact('colleges'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'name_ar'    => 'required|string|max:255',
            'name_en'    => 'nullable|string|max:255',
            'code'       => 'nullable|string|max:20',
        ]);

        $data['is_active'] = true;
        Department::create($data);

        return redirect()->route('departments.index')
                         ->with('success', app()->getLocale() === 'ar'
                             ? 'تم إنشاء القسم بنجاح'
                             : 'Department created successfully');
    }

    public function show(Department $department)
    {
        $department->load(['college', 'users', 'projects']);
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $colleges = College::where('is_active', true)->get();
        return view('departments.edit', compact('department', 'colleges'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'nullable|string|max:255',
            'code'      => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $department->update($data);

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم تحديث القسم بنجاح'
            : 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        $department->update(['is_active' => false]);
        return redirect()->route('departments.index')
                         ->with('success', app()->getLocale() === 'ar'
                             ? 'تم تعطيل القسم'
                             : 'Department deactivated');
    }
}