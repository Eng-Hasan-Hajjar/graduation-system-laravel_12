@extends('errors.layout')

@section('error_code', '401')
@section('icon_type', 'danger')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="7.5" cy="15.5" r="5.5"></circle>
    <path d="M21 2l-9.6 9.6M15.5 7.5l3 3L21 8l-3-3"></path>
</svg>
@endsection

@section('error_title')
    @if(app()->getLocale() === 'ar') يجب تسجيل الدخول @else Authentication Required @endif
@endsection

@section('error_message')
    @if(app()->getLocale() === 'ar')
        انتهت جلستك أو لم تقم بتسجيل الدخول بعد. يرجى تسجيل الدخول للوصول إلى هذه الصفحة.
    @else
        Your session has expired or you are not logged in yet. Please sign in to access this page.
    @endif
@endsection

@section('actions')
    <a href="{{ route('login') }}" class="btn btn-primary">
        @if(app()->getLocale() === 'ar') تسجيل الدخول @else Login @endif
    </a>
    <a href="{{ url('/') }}" class="btn btn-outline">
        @if(app()->getLocale() === 'ar') الصفحة الرئيسية @else Home @endif
    </a>
@endsection