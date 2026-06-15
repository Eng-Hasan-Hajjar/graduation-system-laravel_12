@extends('errors.layout')

@section('error_code', '500')
@section('icon_type', 'danger')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
    <line x1="12" y1="9" x2="12" y2="13"></line>
    <line x1="12" y1="17" x2="12.01" y2="17"></line>
</svg>
@endsection

@section('error_title')
    @if(app()->getLocale() === 'ar') خطأ في الخادم @else Server Error @endif
@endsection

@section('error_message')
    @if(app()->getLocale() === 'ar')
        عذراً، حدث خطأ غير متوقع في الخادم. فريقنا التقني تم إشعاره وسيتم العمل على حل المشكلة في أقرب وقت.
    @else
        Sorry, something went wrong on our end. Our team has been notified and is working to fix the issue.
    @endif
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="btn btn-primary">
        @if(app()->getLocale() === 'ar') الصفحة الرئيسية @else Home @endif
    </a>
    <button onclick="location.reload()" class="btn btn-outline">
        @if(app()->getLocale() === 'ar') إعادة المحاولة @else Try Again @endif
    </button>
@endsection