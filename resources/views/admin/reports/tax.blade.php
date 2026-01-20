@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 dark:text-gray-200">Tax Report (VAT)</h1>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">Back to Reports</a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.tax') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Update Report</button>
                </div>
                <div class="col-md-4 text-end">
                    <button onclick="window.print()" class="btn btn-outline-secondary"><i class="fas fa-print"></i> Print</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Output Tax (Sales)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 dark:text-gray-200">{{ number_format($totalOutputTax, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Input Tax (Purchases)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 dark:text-gray-200">{{ number_format($totalInputTax, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Net VAT Payable</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 dark:text-gray-200 {{ $netTaxPayable < 0 ? 'text-success' : '' }}">
                                {{ number_format($netTaxPayable, 2) }}
                                {{ $netTaxPayable < 0 ? '(Refund)' : '' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Output Tax Detail -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Output Tax (Sales Invoices)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice #</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Tax</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outputTaxInvoices as $inv)
                                <tr>
                                    <td>{{ $inv->date->format('Y-m-d') }}</td>
                                    <td>{{ $inv->invoice_number }}</td>
                                    <td class="text-end">{{ number_format($inv->total_amount, 2) }}</td>
                                    <td class="text-end font-weight-bold">{{ number_format($inv->tax, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Tax Detail -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Input Tax (Expenses)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Tax</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inputTaxExpenses as $exp)
                                <tr>
                                    <td>{{ $exp->date->format('Y-m-d') }}</td>
                                    <td>{{ $exp->reference_number ?? ('EXP-'.$exp->id) }} <br> <small>{{ $exp->vendor_name }}</small></td>
                                    <td class="text-end">{{ number_format($exp->amount, 2) }}</td>
                                    <td class="text-end font-weight-bold">{{ number_format($exp->tax_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
