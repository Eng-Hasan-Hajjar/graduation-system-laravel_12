<?php
 
namespace App\Http\Controllers;
 
// ─── AcademicYearController ───────────────────────────────────────────────────
class AcademicYearController extends Controller
{
    public function index()
    {
        $years = \App\Models\AcademicYear::with('semesters')->orderByDesc('year_start')->get();
        return view('academic-years.index', compact('years'));
    }
 
    public function create()
    {
        return view('academic-years.create');
    }
 
    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'name_ar'    => 'required|string|max:50',
            'name_en'    => 'nullable|string|max:50',
            'year_start' => 'required|integer|min:2000|max:2100',
            'year_end'   => 'required|integer|min:2000|max:2100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);
 
        if ($request->boolean('is_current')) {
            \App\Models\AcademicYear::where('university_id', 1)->update(['is_current' => false]);
        }
 
        $data['university_id'] = 1;
        $data['is_current']    = $request->boolean('is_current');
        $data['is_active']     = true;
 
        \App\Models\AcademicYear::create($data);
 
        return redirect()->route('academic-years.index')
                         ->with('success', 'تم إنشاء السنة الأكاديمية بنجاح');
    }
 
    public function show(\App\Models\AcademicYear $academicYear)
    {
        $academicYear->load('semesters');
        return view('academic-years.show', compact('academicYear'));
    }
 
    public function edit(\App\Models\AcademicYear $academicYear)
    {
        return view('academic-years.edit', compact('academicYear'));
    }
 
    public function update(\Illuminate\Http\Request $request, \App\Models\AcademicYear $academicYear)
    {
        $data = $request->validate([
            'name_ar'    => 'required|string|max:50',
            'name_en'    => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);
 
        if ($request->boolean('is_current')) {
            \App\Models\AcademicYear::where('university_id', 1)
                                    ->where('id', '!=', $academicYear->id)
                                    ->update(['is_current' => false]);
        }
 
        $academicYear->update($data);
 
        return back()->with('success', 'تم تحديث السنة الأكاديمية');
    }
 
    public function destroy(\App\Models\AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->route('academic-years.index')->with('success', 'تم الحذف');
    }
}