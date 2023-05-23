@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Settings</li>
            <li class="breadcrumb-item active">Configuration</li>
        </ol>
    </div>
    <div class="content">
        <settings-configurations-component></settings-configurations-component>
    </div>
@endsection
