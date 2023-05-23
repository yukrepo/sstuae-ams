@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-5">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">elements</li>
            <li class="breadcrumb-item active">doctors</li>
        </ol>
    </div>
    <elements-doctors-component></elements-doctors-component>
@endsection
