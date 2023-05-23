@extends('layouts.main')

@section('content')
    <div class="content-header">
        <div class="row m-0">
            <ol class="breadcrumb m-0 col-6">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/customers/all') }}">Customers</a></li>
                <li class="breadcrumb-item active">view</li>
            </ol>
            <ul class="inlinks col-6 text-right  m-0">
                <li>
                    <i class="fas fa-circle text-secondary"></i> <a href="/customers/all">Back To List</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="content">
        <customers-view-component></customers-view-component>
    </div>
@endsection
