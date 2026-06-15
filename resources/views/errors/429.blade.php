@extends('errors.layout')

@section('error_code', '429')
@section('icon_type', 'warning')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="10"></circle>
    <polyline points="12 6 12 12 16 14"></polyline>
</svg>
@endsection

@section('error_title')
    @if(app()->getLocale() === 'ar') طلبات كثيرة جداً @else Too Many Requests @endif
@endsection

@section('error_message')
    @if(app()->getLocale() === 'ar')
        لقد قمت بإجراء عدد كبير جداً من الطلبات في وقت قصير. يرجى الانتظار قليلاً قبل المحاولة مرة أخرى.
    @else
        You have made too many requests in a short period of time. Please wait a moment before trying again.
    @endif
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="btn btn-primary">
        @if(app()->getLocale() === 'ar') الصفحة الرئيسية @else Home @endif
    </a>
@endsection