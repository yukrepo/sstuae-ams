@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Accounts</li>
            <li class="breadcrumb-item active">List</li>
        </ol>
    </div>
    <div class="content">
        <accounts-component></accounts-component>
    </div>
@endsection
