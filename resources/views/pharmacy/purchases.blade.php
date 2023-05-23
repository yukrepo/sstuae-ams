@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Pharmacy</li>
            <li class="breadcrumb-item active">purchases</li>
        </ol>
    </div>
    <div class="content">
        <pharmacy-purchases-component></pharmacy-purchases-component>
    </div>
@endsection
