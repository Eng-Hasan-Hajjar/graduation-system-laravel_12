<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Priority: session → user preference → config default
        if (session()->has('locale')) {
            App::setLocale(session('locale'));
        } elseif (auth()->check() && auth()->user()->lang_preference) {
            App::setLocale(auth()->user()->lang_preference);
            session(['locale' => auth()->user()->lang_preference]);
        } else {
            App::setLocale(config('app.locale', 'ar'));
        }

        return $next($request);
    }
}