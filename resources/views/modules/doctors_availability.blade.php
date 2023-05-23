@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">elements</li>
            <li class="breadcrumb-item"><a href="{{ url('/elements/doctors') }}">doctors</a></li>
            <li class="breadcrumb-item active">Availability</li>
        </ol>
    </div>
    <div>
        <div class="row bg-gray m-0">
            <div class="col-md-6 text-right py-1">
                <h5 class="text-white m-0 pt-1">{{ $doctor->designation_type->designation }} - {{ $doctor->name }} - Availability</h5>
            </div>
            <div class="col-md-6 text-right py-1">
                <a href="/elements/doctors" class="btn btn-sm btn-dark"> <i class="fas fa-arrow-left"></i> Back To List</a>
            </div>
        </div>
        <elements-doctors-availability-component></elements-doctors-availability-component>
    </div>
@endsection
