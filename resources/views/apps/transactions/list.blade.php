@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@php
    $permissionService = new \App\Services\FeatureService();
    $routes_user = $permissionService->getUserAllowedRoutes();
@endphp

@section('main-content')
    <style>
        #b1, #b2, #b3 {
            display: none;
        }
    </style>

    <div class="breadcrumb">
        <h1>Transaksi</h1>
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
                                    <label for="search" class="ul-form__label">Kode Transaksi:</label>
                                    <input type="text" class="form-control" value="{{ request('search') }}" name="search" id="search" placeholder="Transaction Code">
                                </div>
                            </div>
                            <div class="custom-separator"></div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="start_date" class="ul-form__label">Tanggal Awal:</label>
                                    <input type="date" class="form-control" value="{{ request('start_date') }}" name="start_date" id="start_date" placeholder="Start Date">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="end_date" class="ul-form__label">Tanggal Akhir:</label>
                                    <input type="date" class="form-control" value="{{ request('end_date') }}" name="end_date" id="end_date" placeholder="End Date">
                                </div>
                            </div>

                            <div class="custom-separator"></div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="mid" class="ul-form__label">Kode Agen:</label>
                                    <input type="text" class="form-control" value="{{ request('mid') }}" name="mid" id="mid" placeholder="Kode Agen">
                                </div>
                                <div class="form-group col-md-6">
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
                                        <button type="submit" class="btn btn-success m-1">Simpan</button>
                                        <a href="{{ route('transaction') }}" class="btn btn-danger m-1">Hapus Filter</a>
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
                            <p class="text-muted mt-2 mb-0">Jumlah Transaksi</p>
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
                            <p class="text-muted mt-2 mb-0">Total Transaksi</p>
                            <p class="text-primary text-24 line-height-1 mb-2">@currency($dataRevenue['amount_trx'])</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4">
            @if (in_array('transaction_fee', $routes_user))
                <a href="/transaction/fee" class="card-link">
            @else
                <a href="javascript:void(0);" class="card-link disabled">
            @endif
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
                        <h4 class="col-sm-9 col-md-6 card-title mb-3" style="line-height: 2.1rem;">Daftar Transaksi Agen</h4>
                        <div class="col-sm-3 col-md-12 mb-3">
                            <div class="export-button-wrapper" style="float: right;">
                            @if (in_array('transactions.csv', $routes_user))
                                <a id="export-to-excel" href="{{ route('transactions.csv') }}" 
                                class="btn" 
                                style="background-color: #4CAF50; color: white; border-color: #4CAF50;">
                                    <img src="{{ asset('assets/images/new_features/xls.png') }}" alt="Excel Icon" 
                                        style="width: 20px; height: 20px; margin-right: 5px;">
                                    Ekspor Excel
                                </a>
                            @endif
                                <!-- <a id="export-to-pdf" href="{{ route('transactions.pdf') }}" class="btn btn-outline-secondary">Export to PDF</a> -->
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
                                     <th scope="col">No</th>
                                     <th scope="col">Kode Transaksi</th>
                                     <th scope="col">Tanggal</th>
                                     <th scope="col">Kode Agen</th>
                                     <th scope="col">Kode Cabang</th>
                                     <th scope="col">Tipe Transaksi</th>
                                     <th scope="col">Tipe Produk</th>
                                     <th scope="col">Nominal</th>
                                     <th scope="col">Fee</th>
                                     <th scope="col">Nomor Rekening Pengirim</th>
                                     <th scope="col">Nomor Rekening Penerima</th>
                                     <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach($data as $item)
                                    <tr>
                                    <th scope="row">{{ $no }}</td>
                                        <td>{{ $item->transaction_code }}</td>
                                        <td>{{ $item->transaction_time }}</td>
                                        <td>{{ $item->kode_agen}}</td>
                                        <td>{{ optional(optional($item->merchant)->user)->branchid ?? '' }}</td>
                                        <td>
                                            {{ ucwords(preg_replace('/\b(form|review|otp|bayar)\b/i', '', $item->service->service_name ?? '')) }}
                                        </td>
                                        <td>{{ $item->transaction_type}}</td>
                                        <td>@currency($item->amount)</td>
                                        <td>@currency($item->fee)</td>
                                        <td>{{ $item->rekening_pengirim }}</td>
                                        <td>{{ $item->rekening_penerima }}</td>
                                        <td>{{$item->transactionStatus->transaction_status_desc ?? '' }}</td>
                                    </tr>
                                    @php
                                            $no++;
                                        @endphp
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
    <script src="{{ asset('assets/js/vendor/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/js/es5/echart.options.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatables.script.js') }}"></script>
    <script src="{{ asset('assets/js/es5/dashboard.v4.script.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/pickadate/picker.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/pickadate/picker.date.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#default_ordering_table').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "order": [], 
            });
        });

        var $dateFrom = $('.start_date').pickadate({format: 'yyyy-mm-dd'}),
            dateFromPicker = $dateFrom.pickadate('picker');

        var $dateTo = $('.end_date').pickadate({format: 'yyyy-mm-dd'}),
            dateToPicker = $dateTo.pickadate('picker');

        dateFromPicker.on({
            open: function(e) {
                console.log('Open start_date');
                if ($dateFrom.val()) {
                    console.log('User is making a change to start_date');
                }
            },
            close: function(e) {
                if ($dateFrom.val() && !$dateTo.val()) {
                    console.log('Open end_date via start_date');
                    dateToPicker.open();
                } else if (!$dateFrom.val()) {
                    console.log('User left start_date empty. Not popping end_date');
                }
                console.log('Close start_date');
                $(document.activeElement).blur();
            }
        });

        dateToPicker.on({
            open: function(e) {
                console.log('Popped end_date');
                if ($dateTo.val()) {
                    console.log('User is making a change to end_date');
                }
            },
            close: function(e) {
                if (!$dateTo.val()) {
                    console.log('User left end_date empty. Not popping start_date or end_date');
                }
                console.log('Close end_date');
                $(document.activeElement).blur();
            }
        });
    </script>
@endsection