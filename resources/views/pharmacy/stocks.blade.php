@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Pharmacy</li>
            <li class="breadcrumb-item active">stocks</li>
        </ol>
    </div>
    <div class="content">
        <pharmacy-stocks-component></pharmacy-stocks-component>
    </div>
@endsection
