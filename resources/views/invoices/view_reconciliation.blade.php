@extends('layouts.main')

@section('content')
<div class="content-header">
    <div class="row m-0">
        <ol class="breadcrumb col-6 m-b-5">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="/invoices/reconciliation">Reconciliation</a></li>
            <li class="breadcrumb-item active">View Settlement</li>
        </ol>
        <ul class="inlinks col-6 text-right  m-b-5">
            <li>
                <i class="fas fa-arrow-left fa-fw"></i>
                <a href="/invoices/reconciliation">Back To List</a>
            </li>
        </ul>
    </div>
</div>
    <div class="content">
        <invoices-reconciliation-view-component></invoices-reconciliation-view-component>
    </div>
@endsection
