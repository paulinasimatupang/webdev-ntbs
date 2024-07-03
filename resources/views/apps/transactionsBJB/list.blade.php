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
                <h1>Transaction BJB</h1>
                <ul>
                    <li><a href="">Selada</a></li>
                    
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="2-columns-form-layout">
                <div class="">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <form method="GET" action="transactionBJB" id="transaction_filter_form">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="inputtext11" class="ul-form__label">Search All Columns:</label>
                                                <input type="text" class="form-control" value="{{app('request')->input('search')}}" name="search" id="search" placeholder="Search Everything">
                                            </div>
                                        </div>

                                        <div class="custom-separator"></div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputtext14" class="ul-form__label">Start Date:</label>
                                                <input type="text" readonly="readonly" class="form-control start_date" value="{{app('request')->input('start_date')}}" name="start_date" id="start_date" placeholder="Start Date">
                                                
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputtext14" class="ul-form__label">End Date:</label>
                                                <div class="input-right-icon">
                                                    <input type="text" readonly="readonly" class="form-control end_date" value="{{app('request')->input('end_date')}}" name="end_date" id="end_date" placeholder="End Date">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="custom-separator"></div>


                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">TID:</label>
                                                <input type="text" class="form-control" value="{{app('request')->input('tid')}}" name="tid" id="tid" placeholder="Terminal ID">
                                                
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">MID:</label>
                                                <div class="input-right-icon">
                                                    <input type="text" class="form-control" value="{{app('request')->input('mid')}}" name="mid" id="mid" placeholder="Merchant ID">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">Agent Name:</label>
                                                <div class="input-right-icon">
                                                    <input type="text" class="form-control" value="{{app('request')->input('agent_name')}}" name="agent_name" id="agent_name" placeholder="Agent Name">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="custom-separator"></div>


                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">STAN:</label>
                                                <input type="text" class="form-control" value="{{app('request')->input('stan')}}" name="stan" id="stan" placeholder="STAN">
                                                
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">Message ID:</label>
                                                <div class="input-right-icon">
                                                    <input type="text" class="form-control" value="{{app('request')->input('message_id')}}" name="message_id" id="message_id" placeholder="Message ID">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">Service:</label>
                                                <div class="input-right-icon">
                                                    <!-- <input type="text" class="form-control" value="{{app('request')->input('service')}}" name="service" id="service" placeholder="Service Name"> -->
                                                    <select class="form-control" name="service" id="service" placeholder="Service Name">
                                                        <option>Select Service</option>
                                                        <option @php if (app('request')->input('service') == 'Tarik Tunai') echo 'selected' @endphp>Tarik Tunai</option>
                                                        <option @php if (app('request')->input('service') == 'Transfer Antar Bank') echo 'selected' @endphp>Transfer Antar Bank</option>
                                                        <option @php if (app('request')->input('service') == 'Pemindahbukuan') echo 'selected' @endphp>Pemindahbukuan</option>
                                                        <option @php if (app('request')->input('service') == 'Setor Tunai') echo 'selected' @endphp>Setor Tunai</option>
                                                        <option @php if (app('request')->input('service') == 'Mini Statement') echo 'selected' @endphp>Mini Statement</option>
                                                        <option @php if (app('request')->input('service') == 'Info Saldo') echo 'selected' @endphp>Info Saldo</option>
                                                        <option @php if (app('request')->input('service') == 'Ganti PIN') echo 'selected' @endphp>Ganti PIN</option>
                                                        <option @php if (app('request')->input('service') == 'Buka Rekening') echo 'selected' @endphp>Buka Rekening</option>
                                                        <option @php if (app('request')->input('service') == 'Batal Rekening') echo 'selected' @endphp>Batal Rekening</option>
                                                        <option @php if (app('request')->input('service') == 'PBB') echo 'selected' @endphp>PBB</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="custom-separator"></div>


                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">Message Status:</label>
                                                <input type="text" class="form-control" value="{{app('request')->input('message_status')}}" name="message_status" id="message_status" placeholder="Message Status">
                                                
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">Response Code:</label>
                                                <div class="input-right-icon">
                                                    <input type="text" class="form-control" value="{{app('request')->input('rc')}}" name="rc" id="rc" placeholder="Response Code">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">Status:</label>
                                                <div class="input-right-icon">
                                                    <!-- <input type="text" class="form-control" value="{{app('request')->input('service')}}" name="service" id="service" placeholder="Service Name"> -->
                                                    <select class="form-control" name="status" id="status" placeholder="Select Status">
                                                        <option>Select Status</option>
                                                        <option @php if (app('request')->input('status') == 'Success') echo 'selected' @endphp>Success</option>
                                                        <option @php if (app('request')->input('status') == 'Failed') echo 'selected' @endphp>Failed</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputtext14" class="ul-form__label">Fee:</label>
                                                <div class="input-right-icon">
                                                    <!-- <input type="text" class="form-control" value="{{app('request')->input('service')}}" name="service" id="service" placeholder="Service Name"> -->
                                                    <select class="form-control" name="fee" id="fee" placeholder="Select Status">
                                                        <option @php if (app('request')->input('fee')=='false') echo 'selected' @endphp value="false">Semua Transaksi</option>
                                                        <option @php if (app('request')->input('fee')=='true') echo 'selected' @endphp value="true">Transaksi dengan fee saja</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card-footer">
                                        <div class="mc-footer">
                                            <div class="row">
                                                <div class="col-lg-12 text-center">
                                                    <button type="submit" class="btn  btn-success m-1">Save</button>
                                                    <a href='https://report.selada.id/transactionBJB' type="button" class="btn btn-outline-secondary m-1">Clear</a>



                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </form>

                                <!-- end::form 3-->

                            </div>
                            
                        </div>

                    </div>
                    <!-- end of main row -->
                </div>
            </div>
            <div class="row mb-4">
                <!-- <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center mb-3">
                    <div class="input-group">
                        <div id="picker3" class="input-group-append filter-dashboard-2 form-control">Filter</div>
                    </div>
                </div> -->

                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/transaction/total_transaction.png') }}" alt="Total Fee Agent" class="mr-3" style="width: 50px">
                                <div class="text-left">
                                    <p class="text-muted mt-2 mb-0">Total Transaction</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">{{ $dataRevenue->count() }}</p>
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
                                    <p class="text-primary text-24 line-height-1 mb-2">@currency($dataRevenue->sum('nominal'))</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/transaction/total_fee.png') }}" alt="Total Fee Agent" class="mr-3" style="width: 50px">
                                <div class="text-left">
                                    <p class="text-muted mt-2 mb-0">Total Fee</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">@currency($dataRevenue->sum('fee'))</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/transaction/total_fee_agent.png') }}" alt="Total Fee Agent" class="mr-3" style="width: 50px">
                                <div class="text-left">
                                    <p class="text-muted mt-2 mb-0">Total Fee Agent</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">@currency($dataRevenue->sum('agent_fee'))</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/transaction/total_fee_bjb2.png') }}" alt="Total Fee Agent" class="mr-3" style="width: 50px">
                                <div class="text-left">
                                    <p class="text-muted mt-2 mb-0">Total Fee BJB</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">@currency($dataRevenue->sum('bjb_fee'))</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/transaction/total_fee_selada.png') }}" alt="Total Fee Agent" class="mr-3" style="width: 50px">
                                <div class="text-left">
                                    <p class="text-muted mt-2 mb-0">Total Fee Selada</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">@currency($dataRevenue->sum('selada_fee'))</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="card text-left">
                        <div class="card-body">
                            <div class="row">
                                <h4 class=" col-sm-9 col-md-6 card-title mb-3" style="line-height: 2.1rem;">List Transaction </h4>
                                <div class="col-sm-3 col-md-6 mb-3">
                                <a id="export-to-csv" 
                                style="float:right; margin-left: 10px" href='#' 
                                type="button" class="btn btn-outline-secondary">
                                        Export to CSV
                                </a>
					            <a id="export-to-excel" 
                                style="float:right" href='#' 
                                type="button" class="btn btn-outline-secondary">
                                        Export to Excel
                                </a>
                                <a id="export-fee-to-excel" 
                    			style="float:right; margin-right: 10px" href='#' 
                    			type="button" class="btn btn-outline-secondary">
                    			        Export Fee Agen to Excel
                    			</a>
					            <a id="export-to-pdf" 
                                style="float: right; margin-right: 10px; display:none" href='#' 
                                type="button" class="btn btn-outline-secondary">
                                        Export to PDF
                                </a>
				</div>
                            </div>
                            
                            <div class="table-responsive">
                                <table id="deafult_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">TID</th>
                                            <th scope="col">MID</th>
                                            <th scope="col">Agent Name</th>
                                            <th scope="col">Transaction Name</th>
                                            <th scope="col">Product</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Stan</th>
                                            <th scope="col">Message ID</th>
                                            <th scope="col">Nominal</th>
                                            <th scope="col">Fee</th>
                                            <th scope="col">Agent Fee</th>
                                            <th scope="col">BJB Fee</th>
                                            <th scope="col">Selada Fee</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Message Status</th>
                                            <th scope="col">Response Code</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">PAN</th>
                                            <th scope="col">Src Account</th>
                                            <th scope="col">Dst Account</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 0;
                                            $no = ($data->perPage() * ($data->currentPage() - 1))+1;    
                                        @endphp
                                        @foreach($data as $item)
                                        <tr>
                                            <th scope="row">{{ $no }}</th>
                                            <td>{{$item->tid}}</td>
                                            <td>{{$item->mid}}</td>
                                            <td>{{$item->agent_name}}</td>
                                            <td>{{$item->transaction_name}}</td>
                                            <td>{{$item->product_name}}</td>
                                            <td>{{substr($item->tx_time, 0, 19)}}</td>
                                            <td>{{$item->stan}}</td>
                                            <td>{{$item->message_id}}</td>
                                            <td>@currency($item->nominal)</td>
                                            <td>@currency($item->fee)</td>
                                            <td>@currency($item->agent_fee)</td>
                                            <td>@currency($item->bjb_fee)</td>
                                            <td>@currency($item->selada_fee)</td>
                                            <td>@currency($item->total)</td>
                                            <td>{{$item->message_status}}</td>
                                            <td>{{$item->rc}}</td>
                                            <td>{{$item->status}}</td>
                                            <td>{{$item->tx_pan}}</td>
                                            <td>{{$item->src_account}}</td>
                                            <td>{{$item->dst_account}}</td>
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

                    <center><div class="mt-4 center">{{$data->appends(request()->input())->links()}}</div></center>
                </div>
            </div>
@endsection

@section('page-js')
     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <!-- <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script> -->
     <!-- <script src="{{asset('assets/js/datatables.script.js')}}"></script> -->
     <script src="{{asset('assets/js/es5/dashboard.v4.script.js')}}"></script>
     <script src="{{asset('assets/js/vendor/pickadate/picker.js')}}"></script>
     <script src="{{asset('assets/js/vendor/pickadate/picker.date.js')}}"></script>

     <script>
	$("#export-to-excel").on('click', function() {
		window.location = "transactionBJB/export?"+$('#transaction_filter_form').serialize()
		// window.location = "http://laravel.test/transactionBJB/export?"+$('#transaction_filter_form').serialize()
        });
	$("#export-to-pdf").on('click', function() {
                window.location = "transactionBJB/exportPDF?"+$('#transaction_filter_form').serialize()
        });
	$("#export-to-csv").on('click', function() {
                window.location = "transactionBJB/exportCSV?"+$('#transaction_filter_form').serialize()
        });
    $("#export-fee-to-excel").on('click', function() {
                window.location = "transactionBJB/feeExport?"+$('#transaction_filter_form').serialize()
                // window.location = "http://laravel.test/transactionBJB/feeExport?"+$('#transaction_filter_form').serialize()
        });

        var $dateFrom = $('.start_date').pickadate({format:'yyyy-mm-dd'}),
            dateFromPicker = $dateFrom.pickadate('picker');

        var $dateTo = $('.end_date').pickadate({format:'yyyy-mm-dd'}),
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
            }
            else if (!$dateFrom.val()) {
            console.log('User left start_date empty. Not popping end_date');
            }
            console.log('Close start_date');
            // workaround for github issue #160
            $(document.activeElement).blur();
        }
        });
        dateToPicker.on({
        open: function(e) {
            console.log('Poppped end_date');
            if ($dateTo.val()) {
            console.log('User is making a change to end_date');
            }
        },
        close: function(e) {
            if (!$dateTo.val()) {
            console.log('User left end_date empty. Not popping start_date or end_date');
            }
            console.log('Close end_date');
            // workaround for github issue #160
            $(document.activeElement).blur();
        }
        });



    </script>
@endsection
@section('bottom-js')
<script src="{{asset('assets/js/form.basic.script.js')}}"></script>
<script>
    function changeStatusBJB(id) {
        var r = confirm("Anda akan merubah status transaksi, Apakah anda yakin?");
        if (r == true) {
            // var e = document.getElementById("change_status_bjb");
            // var strType = e.options[e.selectedIndex].text;
            // var url = '{{route("transaction_updatebjb",[":stan|:date|:type"])}}';
            var split = "|";
            var url = '{{route("transaction_updatebjb",[":id"])}}';
            url = url.replace(':id', id);
            // url = url.replace(':id', id + split + date.substring(0,10));
            // url = url.replace(':date', date.substring(0,10));
            // url = url.replace(':type', strType);
            $.post(url,
            {
                _token: "{{ csrf_token() }}",
            },
            function(data,status){
                location.reload(true);
            }).done(function() {
                location.reload(true);
            }).fail(function() {
                console.error();
                alert( "Error, Please try again later!" );
            })
        }
    }
</script>

@endsection
