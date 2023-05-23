@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-0">
            <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
            <li class="breadcrumb-item active">Appointments</li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </div>
    <div class="content">
        <appointment-view-component></appointment-view-component>
    </div>
@endsection
