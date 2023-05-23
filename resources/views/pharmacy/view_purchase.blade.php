@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Pharmacy</li>
            <li class="breadcrumb-item"><a href="{{ url('/pharmacy/purchases') }}">purchases</a></li>
            <li class="breadcrumb-item active">view New Purchase</li>
        </ol>
    </div>
    <div class="content">
        <pharmacy-view-purchase-component></pharmacy-view-purchase-component>
    </div>
@endsection
