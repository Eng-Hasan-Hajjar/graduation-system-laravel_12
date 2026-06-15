@extends('errors.layout')

@section('error_code', '419')
@section('icon_type', 'warning')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="23 4 23 10 17 10"></polyline>
    <polyline points="1 20 1 14 7 14"></polyline>
    <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"></path>
</svg>
@endsection

@section('error_title')
    @if(app()->getLocale() === 'ar') انتهت صلاحية الصفحة @else Page Expired @endif
@endsection

@section('error_message')
    @if(app()->getLocale() === 'ar')
        لقد انتهت صلاحية هذه الصفحة بسبب طول مدة عدم النشاط. يرجى تحديث الصفحة والمحاولة مرة أخرى.
    @else
        This page has expired due to inactivity. Please refresh the page and try again.
    @endif
@endsection

@section('actions')
    <button onclick="location.reload()" class="btn btn-primary">
        @if(app()->getLocale() === 'ar') تحديث الصفحة @else Refresh Page @endif
    </button>
    <a href="{{ url('/') }}" class="btn btn-outline">
        @if(app()->getLocale() === 'ar') الصفحة الرئيسية @else Home @endif
    </a>
@endsection