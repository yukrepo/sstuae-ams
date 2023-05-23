@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Assets</li>
            <li class="breadcrumb-item active">lab categories</li>
        </ol>
    </div>
    <div class="content">
        <assets-labcategories-component></assets-labcategories-component>
    </div>
@endsection
