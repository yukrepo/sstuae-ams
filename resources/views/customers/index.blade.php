@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-1">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Customers</li>
        </ol>
    </div>
    <div class="content">
        <customers-component></customers-component>
    </div>
@endsection
