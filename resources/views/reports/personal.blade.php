@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </div>
    <div class="content">
        @if(Auth::user()->admin_type_id ==  5)
            <reception-reports-component></reception-reports-component>
        @elseif(Auth::user()->admin_type_id ==  6)
            <therapist-reports-component></therapist-reports-component>
        @else

        @endif
    </div>
@endsection
