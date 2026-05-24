<?php


namespace App\Http\Controllers;

// ─── SettingController ────────────────────────────────────────────────────────
class SettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\Setting::orderBy('group')->orderBy('key')->get()
                                       ->groupBy('group');
        return view('settings.index', compact('settings'));
    }
 
    public function update(\Illuminate\Http\Request $request, \App\Models\Setting $setting)
    {
        $setting->update(['value' => $request->value]);
        return back()->with('success', 'تم حفظ الإعداد');
    }
}