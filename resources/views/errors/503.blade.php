@extends('errors.layout')

@section('error_code', '503')
@section('icon_type', 'info')

@section('icon')
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"></path>
</svg>
@endsection

@section('error_title')
    @if(app()->getLocale() === 'ar') الموقع تحت الصيانة @else Under Maintenance @endif
@endsection

@section('error_message')
    @if(app()->getLocale() === 'ar')
        نقوم حالياً بإجراء بعض التحديثات والصيانة على النظام. نعتذر عن الإزعاج، يرجى المحاولة بعد قليل.
    @else
        We're currently performing some maintenance and updates. Sorry for the inconvenience — please check back shortly.
    @endif
@endsection

@section('actions')
    <button onclick="location.reload()" class="btn btn-primary">
        @if(app()->getLocale() === 'ar') إعادة المحاولة @else Try Again @endif
    </button>
@endsection