@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-1">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/customers/all') }}">Customers</a></li>
            <li class="breadcrumb-item active">Duplicate Record Merging</li>
        </ol>
    </div>
    <div class="content">
        <customers-mnerging-component></customers-mnerging-component>
    </div>
@endsection
