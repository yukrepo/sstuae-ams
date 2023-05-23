@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Reports</li>
            <li class="breadcrumb-item active">treatments Reports</li>
        </ol>
    </div>
    <div class="content">
        <reports-treatments-component></reports-treatments-component>
    </div>
@endsection
