@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Settings</li>
            <li class="breadcrumb-item active">Locations</li>
        </ol>
    </div>
    <div class="content">
        <settings-locations-component></settings-locations-component>
    </div>
@endsection
