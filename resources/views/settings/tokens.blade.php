@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Settings</li>
            <li class="breadcrumb-item active">Tokens</li>
        </ol>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 tokenbox">
                    <passport-clients></passport-clients>
                    <passport-authorized-clients></passport-authorized-clients>
                    <passport-personal-access-tokens></passport-personal-access-tokens>
                </div>
            </div>
        </div>
    </div>
@endsection
