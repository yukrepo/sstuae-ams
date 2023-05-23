@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-1">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Invoices</li>
            <li class="breadcrumb-item active">Edit Direct Pharmacy Invoice</li>
        </ol>
    </div>
    <div class="content">
        <invoices-edit-dpharmacy-component></invoices-edit-dpharmacy-component>
    </div>
@endsection
