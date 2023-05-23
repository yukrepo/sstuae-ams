@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </div>
    <div class="content">
        <reports-component></reports-component>
    </div>
@endsection
