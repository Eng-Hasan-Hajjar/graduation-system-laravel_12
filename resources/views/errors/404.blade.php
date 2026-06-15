@extends('errors.layout')

@section('error_code', '404')
@section('icon_type', 'info')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="11" cy="11" r="8"></circle>
    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
</svg>
@endsection

@section('error_title')
    @if(app()->getLocale() === 'ar') الصفحة غير موجودة @else Page Not Found @endif
@endsection

@section('error_message')
    @if(app()->getLocale() === 'ar')
        عذراً، الصفحة التي تحاول الوصول إليها غير موجودة، أو تم حذفها، أو تم نقلها إلى رابط آخر.
    @else
        Sorry, the page you are looking for doesn't exist, has been removed, or moved to another location.
    @endif
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="btn btn-primary">
        @if(app()->getLocale() === 'ar') العودة للصفحة الرئيسية @else Back to Home @endif
    </a>
    <button onclick="history.back()" class="btn btn-outline">
        @if(app()->getLocale() === 'ar') الرجوع للخلف @else Go Back @endif
    </button>
@endsection