@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Pharmacy</li>
            <li class="breadcrumb-item active">categories</li>
        </ol>
    </div>
    <div class="content">
        <pharmacy-categories-component></pharmacy-categories-component>
    </div>
@endsection
