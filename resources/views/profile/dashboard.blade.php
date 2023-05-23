@extends('layouts.main')

@section('content')
    <div>
        <div class="content-header">
            <div class="mb-0">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                    @if(\Route::current()->parameter('slug') == 'analytics') 
                    <li class="breadcrumb-item active">Analytics</li>
                    @endif
                </ol>
            </div>
        </div>
        <div class="">
            @if(\Route::current()->parameter('slug') == 'analytics') 
                <analytics-dashboard-component></analytics-dashboard-component>
            @else
                <div class="container-fluid">
                    @if(Auth::user()->admin_type_id == 1)
                        <admin-dashboard-component></admin-dashboard-component>
                    @elseif(Auth::user()->admin_type_id == 2)
                        <doctor-dashboard-component></doctor-dashboard-component>
                    @elseif(Auth::user()->admin_type_id == 4)
                        <manager-dashboard-component></manager-dashboard-component>
                    @elseif(Auth::user()->admin_type_id == 5)
                        <reception-dashboard-component></reception-dashboard-component>
                    @elseif(Auth::user()->admin_type_id == 6)
                        <therapists-dashboard-component></therapists-dashboard-component>
                    @elseif(Auth::user()->admin_type_id == 7)
                        <accounts-dashboard-component></accounts-dashboard-component>
                    @elseif(Auth::user()->admin_type_id == 8)
                        <director-dashboard-component></director-dashboard-component>
                    @elseif(Auth::user()->admin_type_id == 9)
                        <manager-dashboard-component></manager-dashboard-component>
                    @else
                        <h4> Welcome {{ Auth::user()->name }},</h4>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection