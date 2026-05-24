<?php


namespace App\Http\Controllers;

 
// ─── DepartmentController ─────────────────────────────────────────────────────
class DepartmentController extends Controller
{
    public function index()
    {
        $departments = \App\Models\Department::with('college')->get();
        return view('departments.index', compact('departments'));
    }
 
    public function create()
    {
        $colleges = \App\Models\College::where('is_active', true)->get();
        return view('departments.create', compact('colleges'));
    }
 
    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'name_ar'    => 'required|string|max:255',
            'name_en'    => 'nullable|string|max:255',
            'code'       => 'nullable|string|max:20',
        ]);
 
        $data['is_active'] = true;
        \App\Models\Department::create($data);
 
        return redirect()->route('departments.index')->with('success', 'تم إنشاء القسم بنجاح');
    }
 
    public function show(\App\Models\Department $department)
    {
        $department->load(['college', 'users', 'projects']);
        return view('departments.show', compact('department'));
    }
 
    public function edit(\App\Models\Department $department)
    {
        $colleges = \App\Models\College::where('is_active', true)->get();
        return view('departments.edit', compact('department', 'colleges'));
    }
 
    public function update(\Illuminate\Http\Request $request, \App\Models\Department $department)
    {
        $data = $request->validate([
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'nullable|string|max:255',
            'code'      => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);
 
        $department->update($data);
        return back()->with('success', 'تم تحديث القسم');
    }
 
    public function destroy(\App\Models\Department $department)
    {
        $department->update(['is_active' => false]);
        return redirect()->route('departments.index')->with('success', 'تم تعطيل القسم');
    }
}