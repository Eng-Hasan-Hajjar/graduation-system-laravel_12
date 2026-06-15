@extends('errors.layout')

@section('error_code', '405')
@section('icon_type', 'warning')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="10"></circle>
    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
</svg>
@endsection

@section('error_title')
    @if(app()->getLocale() === 'ar') الطريقة غير مسموحة @else Method Not Allowed @endif
@endsection

@section('error_message')
    @if(app()->getLocale() === 'ar')
        طريقة الطلب المستخدمة غير مسموحة لهذا المسار. يرجى الرجوع والمحاولة من خلال الرابط الصحيح.
    @else
        The request method used is not allowed for this route. Please go back and try via the correct link.
    @endif
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="btn btn-primary">
        @if(app()->getLocale() === 'ar') الصفحة الرئيسية @else Home @endif
    </a>
    <button onclick="history.back()" class="btn btn-outline">
        @if(app()->getLocale() === 'ar') الرجوع للخلف @else Go Back @endif
    </button>
@endsection