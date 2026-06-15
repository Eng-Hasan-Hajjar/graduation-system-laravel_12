@extends('errors.layout')

@section('error_code', '403')
@section('icon_type', 'danger')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
    <path d="M7 11V7a5 5 0 0110 0v4"></path>
</svg>
@endsection

@section('error_title')
    @if(app()->getLocale() === 'ar') غير مصرح لك بالوصول @else Access Forbidden @endif
@endsection

@section('error_message')
    @if(app()->getLocale() === 'ar')
        عذراً، لا تملك الصلاحية الكافية للوصول إلى هذه الصفحة أو تنفيذ هذا الإجراء. إذا كنت تعتقد أن هذا خطأ، تواصل مع مسؤول النظام.
    @else
        Sorry, you don't have permission to access this page or perform this action. If you believe this is a mistake, please contact the system administrator.
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