<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')
                           ->orderBy('key')
                           ->get()
                           ->groupBy('group');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'value' => 'nullable',
        ]);

        // boolean checkbox: إن لم يُرسل = false
        $value = $setting->type === 'boolean'
            ? ($request->has('value') ? '1' : '0')
            : $request->value;

        $setting->update(['value' => $value]);

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم حفظ الإعداد بنجاح'
            : 'Setting saved successfully');
    }
}