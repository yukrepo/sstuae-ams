@extends('layouts.main')

@section('content')
    <div class="content-header">
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Reports</li>
            <li class="breadcrumb-item active">Full Reports</li>
        </ol>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Full Reports</h3>
                        </div>
                        <div class="card-body p-0">
                            <form action="{{ route('icreports') }}" method="POST">
                            @csrf
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="wf-50">SNo</th>
                                        <th>Module</th>
                                        <th class="w-50">Reports</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <th>Stocks</th>
                                        <td>
                                            <a target="_blank" href="/full-reports/stocks" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                <i class="fas fa-file-pdf"></i> PDF Report
                                            </a>
                                            <a target="_blank" href="/full-reports/stocks" class="btn btn-sm btn-outline-dark">
                                                <i class="fas fa-file-excel"></i> EXCEL Report
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <th>Medicines</th>
                                        <td>
                                            <a target="_blank" href="/full-reports/medicines" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                <i class="fas fa-file-pdf"></i> PDF Report
                                            </a>
                                            <a target="_blank" href="/full-reports/medicines" class="btn btn-sm btn-outline-dark">
                                                <i class="fas fa-file-excel"></i> EXCEL Report
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <th>Customers</th>
                                        <td>
                                            <a target="_blank" href="/full-reports/customers" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                <i class="fas fa-file-pdf"></i> PDF Report
                                            </a>
                                            <a target="_blank" href="/full-reports/customers" class="btn btn-sm btn-outline-dark">
                                                <i class="fas fa-file-excel"></i> EXCEL Report
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <th>Treatments</th>
                                        <td>
                                            <a target="_blank" href="/full-reports/treatments" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                <i class="fas fa-file-pdf"></i> PDF Report
                                            </a>
                                            <a target="_blank" href="/full-reports/treatments" class="btn btn-sm btn-outline-dark">
                                                <i class="fas fa-file-excel"></i> EXCEL Report
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <th>Insurances</th>
                                        <td>
                                            <a target="_blank" href="/full-reports/insurances" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                <i class="fas fa-file-pdf"></i> PDF Report
                                            </a>
                                            <a target="_blank" href="/full-reports/insurances" class="btn btn-sm btn-outline-dark">
                                                <i class="fas fa-file-excel"></i> EXCEL Report
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <th>
                                            <select id="insurance_id" name="insurance_id" class="form-control">
                                                <option disabled value="">Select Insurance</option>
                                                @foreach ($insurances as $insurance)
                                                <option value="{{ $insurance->id }}">{{ $insurance->name }}</option>
                                                @endforeach
                                            </select>
                                        </th>
                                        <td>
                                            <a target="_blank" href="/full-reports/insurances" class="btn btn-sm btn-outline-danger mr-2 disabled">
                                                <i class="fas fa-file-pdf"></i> PDF Report
                                            </a>
                                            <button class="btn btn-sm btn-outline-dark" type="submit">
                                                <i class="fas fa-file-excel"></i> Covered Excel Report
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
