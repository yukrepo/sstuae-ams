@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-1">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Invoices</li>
            <li class="breadcrumb-item active">Direct @if(\Route::current()->parameter('slug') == 'direct') Pharmacy @endif Invoices</li>
        </ol>
    </div>
    <div class="content">
        @if(\Route::current()->parameter('slug') == 'direct') 
            <invoices-direct-pharmacy-component></invoices-direct-pharmacy-component> 
        @endif
        @if(\Route::current()->parameter('slug') == 'directinv') 
            <invoices-direct-component></invoices-direct-component> 
        @endif
        
    </div>
@endsection
