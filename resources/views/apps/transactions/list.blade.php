@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@section('main-content')
    <style>
        #b1, #b2, #b3 {
            display: none;
        }
    </style>

    <div class="breadcrumb">
        <h1>Transaction</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="2-columns-form-layout">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <form method="GET" action="{{ route('transaction') }}" id="transaction_filter_form">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="search" class="ul-form__label">Transaction Code:</label>
                                    <input type="text" class="form-control" value="{{ request('search') }}" name="search" id="search" placeholder="Transaction Code">
                                </div>
                            </div>

                            <div class="custom-separator"></div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="start_date" class="ul-form__label">Start Date:</label>
                                    <input type="text" readonly="readonly" class="form-control start_date" value="{{ request('start_date') }}" name="start_date" id="start_date" placeholder="Start Date">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="end_date" class="ul-form__label">End Date:</label>
                                    <input type="text" readonly="readonly" class="form-control end_date" value="{{ request('end_date') }}" name="end_date" id="end_date" placeholder="End Date">
                                </div>
                            </div>

                            <div class="custom-separator"></div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="status" class="ul-form__label">Status:</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="">Select Status</option>
                                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Success" {{ request('status') == 'Success' ? 'selected' : '' }}>Success</option>
                                        <option value="Failed" {{ request('status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <div class="mc-footer">
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <button type="submit" class="btn btn-success m-1">Save</button>
                                        <a href="{{ route('transaction') }}" class="btn btn-outline-secondary m-1">Clear</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/transaction/total_transaction.png') }}" alt="Total Fee Agent" class="mr-3" style="width: 50px">
                        <div class="text-left">
                            <p class="text-muted mt-2 mb-0">Total Transaction</p>
                            <p class="text-primary text-24 line-height-1 mb-2">{{ $dataRevenue['total_trx'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/transaction/amount_transaction.png') }}" alt="Total Fee Agent" class="mr-3" style="width: 60px">
                        <div class="text-left">
                            <p class="text-muted mt-2 mb-0">Amount Transaction</p>
                            <p class="text-primary text-24 line-height-1 mb-2">@currency($dataRevenue['amount_trx'])</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/transaction/fee" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/transaction/total_fee.png') }}" alt="Total Fee Agent" class="mr-3" style="width: 50px">
                            <div class="text-left">
                                <p class="text-muted mt-2 mb-0">Total Fee</p>
                                <p class="text-primary text-24 line-height-1 mb-2">@currency($dataRevenue['total_fee'])</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-12 mb-3">
            <div class="card text-left">
                <div class="card-body">
                    <div class="row">
                        <h4 class="col-sm-9 col-md-6 card-title mb-3" style="line-height: 2.1rem;">List Transaction</h4>
                        <div class="col-sm-3 col-md-12 mb-3">
                            <div class="export-button-wrapper" style="float: right;">
                                @if($username == 'selada_produktif')
                                    <a id="btn-sale-bjb" href="#" class="btn btn-outline-secondary">Ubah Status Transaksi</a>
                                @endif
                                <a id="export-fee-to-csv" href="{{ route('transactions.excel') }}" class="btn btn-outline-secondary">Export to CSV</a>
                                <a id="export-fee-to-txt" href="{{ route('transactions.txt') }}" class="btn btn-outline-secondary">Export to Text</a>
                                <a id="export-fee-to-excel" href="{{ route('transactions.csvFeeOnly') }}" class="btn btn-outline-secondary">Export to Excel (Only Fee)</a>
                                <a id="export-to-excel" href="{{ route('transactions.csv') }}" class="btn btn-outline-secondary">Export to Excel</a>
                                <a id="export-to-pdf" href="{{ route('transactions.pdf') }}" class="btn btn-outline-secondary">Export to PDF</a>
                                <a id="export-payment-to-excel" href="{{ route('transactions.csvPaymentOnly') }}" class="btn btn-outline-secondary">Export to Excel (Payment Only)</a>
                            </div>
                        </div>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="default_ordering_table" class="display table table-striped table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Transaction Code</th>
                                    <th>Amount</th>
                                    <th>Fee</th>
                                    <th>Date</th>
                                    <th>Norek Pengirim</th>
                                    <th>Norek Penerima</th>
                                    <th>Tipe Produk</th>
                                    <th>Kode Agen</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = ($data->perPage() * ($data->currentPage() - 1)) + 1;
                                @endphp
                                @foreach($data as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $item->merchant->name ?? '' }}</td>
                                        <td>{{ $item->transaction_code }}</td>
                                        <td>@currency($item->amount)</td>
                                        <td>@currency($item->fee)</td>
                                        <td>{{ $item->transaction_time }}</td>
                                        <td>{{ $item->rekening_pengirim }}</td>
                                        <td>{{ $item->rekening_penerima }}</td>
                                        <td>{{ $item->transaction_type }}</td>
                                        <td>{{ $item->merchant->mid ?? '' }}</td>
                                        <td>
                                            @switch($item->transaction_status_id)
                                                @case(0)
                                                    Success
                                                    @break
                                                @case(1)
                                                    Failed
                                                    @break
                                                @case(2)
                                                    Pending
                                                    @break
                                                @default
                                                    Unknown
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="{{ asset('assets/scripts/vendor/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/vendor/pickadate/picker.js') }}"></script>
    <script src="{{ asset('assets/scripts/vendor/pickadate/picker.date.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#default_ordering_table').DataTable({
                order: [[5, 'desc']]
            });

            $('.start_date').pickadate({
                format: 'yyyy-mm-dd'
            });

            $('.end_date').pickadate({
                format: 'yyyy-mm-dd'
            });

            $('#export-fee-to-excel').on('click', function() {
                window.location.href = $(this).attr('href');
            });

            $('#export-to-excel').on('click', function() {
                window.location.href = $(this).attr('href');
            });

            $('#export-to-pdf').on('click', function() {
                window.location.href = $(this).attr('href');
            });

            $('#export-payment-to-excel').on('click', function() {
                window.location.href = $(this).attr('href');
            });

            $('#btn-sale-bjb').on('click', function() {
                $('#b1').toggle();
                $('#b2').toggle();
                $('#b3').toggle();
            });
        });
    </script>
@endsection
