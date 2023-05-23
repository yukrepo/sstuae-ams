@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Settings</li>
            <li class="breadcrumb-item active">Discounts</li>
        </ol>
    </div>
    <settings-discounts-component></settings-discounts-component>
@endsection
