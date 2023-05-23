@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">elements</li>
            <li class="breadcrumb-item active">treatments</li>
        </ol>
    </div>
    <div class="content">
        <elements-treatments-component></elements-treatments-component>
    </div>
@endsection
