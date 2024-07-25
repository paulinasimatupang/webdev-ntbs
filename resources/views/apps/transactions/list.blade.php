@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/pickadate/classic.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/pickadate/classic.date.css')}}">
@endsection

@section('main-content')
    <style type="text/css">
        #b1, #b2, #b3 {
            display: none;
        }
    </style>

    <div class="breadcrumb">
        <h1>Transaction</h1>
        <ul>
            <li><a href="">Selada</a></li>
        </ul>
    </div>

    <div class="separator-breadcrumb border-top"></div>
    
    <div class="2-columns-form-layout">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <form method="GET" action="transaction" id="transaction_filter_form">
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
                                    <label for="tid" class="ul-form__label">TID:</label>
                                    <input type="text" class="form-control" value="{{ request('tid') }}" name="tid" id="tid" placeholder="Terminal ID">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="mid" class="ul-form__label">MID:</label>
                                    <input type="text" class="form-control" value="{{ request('mid') }}" name="mid" id="mid" placeholder="Merchant ID">
                                </div>
                            </div>

                            <div class="custom-separator"></div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="service" class="ul-form__label">Service:</label>
                                    <input type="text" class="form-control" value="{{ request('service') }}" name="service" id="service" placeholder="Service">
                                </div>
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
                                        <a href='{{ url("transaction") }}' class="btn btn-outline-secondary m-1">Clear</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            @foreach ([
                ['Total Transaction', 'total_transaction.png', $dataRevenue['total_trx']],
                ['Amount Transaction', 'amount_transaction.png', $dataRevenue['amount_trx']],
                ['Total Fee', 'total_fee.png', $dataRevenue['total_fee']],
                ['Total Fee Agent', 'total_fee_agent.png', $dataRevenue['total_fee_agent']],
                ['Total Fee BJB', 'total_fee_bjb2.png', $dataRevenue['total_fee_bjb']],
                ['Total Fee Selada', 'total_fee_selada.png', $dataRevenue['total_fee_selada']]
            ] as $stat)
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/transaction/'.$stat[1]) }}" alt="{{ $stat[0] }}" class="mr-3" style="width: 50px">
                                <div class="text-left">
                                    <p class="text-muted mt-2 mb-0">{{ $stat[0] }}</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">@currency($stat[2])</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="col-md-12 mb-3">
            <div class="card text-left">
                <div class="card-body">
                    <div class="row">
                        <h4 class="col-sm-9 col-md-6 card-title mb-3" style="line-height: 2.1rem;">List Transaction</h4>
                        <div class="col-sm-3 col-md-12 mb-3">
                            <div style="display: inline-block; float: right;" class="export-button-wrapper">
                                @if($username == 'selada_produktif')
                                    <a id="btn-sale-bjb" href="#" class="btn btn-outline-secondary">Ubah Status Transaksi</a>
                                @endif
                                <a id="export-fee-to-excel" href='#' class="btn btn-outline-secondary">Export Fee Agen to Excel</a>
                                <a id="export-to-csv" href='#' class="btn btn-outline-secondary">Export to CSV</a>
                                <a id="export-to-excel" href='#' class="btn btn-outline-secondary">Export to Excel</a>
                                <a id="sale-export-to-excel" href='#' class="btn btn-outline-secondary">Export to Excel (Payment Only)</a>
                                <a id="export-to-pdf" href='#' style="display:none" class="btn btn-outline-secondary">Export to PDF</a>
                            </div>
                        </div>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="default_ordering_table" class="display table table-striped table-bordered" style="width:150%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Nomor Rekening</th>
                                    <th>Transaction Code</th>
                                    <th>TID</th>
                                    <th>MID</th>
                                    <th>Amount</th>
                                    <th>Fee</th>
                                    <th>Trace Number</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ optional($item->merchant->user)->username }}</td>
                                        <td>{{ $item->merchant->account_number ?? '-' }}</td>
                                        <td>{{ $item->transaction_code }}</td>
                                        <td>{{ $item->tid }}</td>
                                        <td>{{ $item->mid }}</td>
                                        <td>@currency($item->amount)</td>
                                        <td>@currency($item->fee)</td>
                                        <td>{{ $item->trace_number }}</td>
                                        <td>@currency($item->amount + $item->fee)</td>
                                        <td>{{ $item->created_at }}</td>
                                        <td>{{ $item->status }}</td>
                                        <td>{{ $item->merchant->user->last_login ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Nomor Rekening</th>
                                    <th>Transaction Code</th>
                                    <th>TID</th>
                                    <th>MID</th>
                                    <th>Amount</th>
                                    <th>Fee</th>
                                    <th>Trace Number</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                </tr>
                            </tfoot>
                        </table>
                        <div>
                            {!! $data->links() !!}
                        </div>
                    </div>

                    @foreach(['tambah-reversal', 'tambah'] as $action)
                        <form id="{{ $action }}" method="post" action="{{ route($action) }}">
                            @csrf
                            <input type="hidden" id="{{ $action }}_id" name="id" />
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/pickadate/picker.js')}}"></script>
    <script src="{{asset('assets/js/vendor/pickadate/picker.date.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#default_ordering_table').DataTable();
            $('.start_date, .end_date').pickadate({ format: 'yyyy-mm-dd' });
            
            $('#btn-sale-bjb').click(function () {
                $('#sale-export-to-excel').toggle();
            });

            const exportButtons = {
                'export-fee-to-excel': 'export_fee_agent',
                'export-to-csv': 'export_csv',
                'export-to-excel': 'export_excel',
                'sale-export-to-excel': 'sale_export_excel',
                'export-to-pdf': 'export_pdf'
            };

            for (let buttonId in exportButtons) {
                $('#' + buttonId).click(function () {
                    $('#tambah-reversal_id').val(this.value);
                    $('#tambah-reversal').submit();
                });
            }
        });
    </script>
@endsection
