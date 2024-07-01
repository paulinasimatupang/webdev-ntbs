@extends('layouts.master')
@section('main-content')
    <div class="breadcrumb">
                <h1>Transaction BJB</h1>
                <ul>
                    <li><a href="">Selada</a></li>
                   
                </ul>
            </div>
            <div class="separator-breadcrumb border-top"></div>

            @if(Session('error'))
                @foreach (Session('error') as $key => $item)
                <div class="alert alert-danger" role="alert">
                    <strong class="text-capitalize">Error : </strong> {{ $item[0] }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endforeach
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-5">
                        <div class="card-body">
                            <form action="{{route('transactionBJB_update', [$data->message_id])}}" method="POST">
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">STAN</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{$data->stan}}" name="stan" placeholder="STAN" required disabled>
                                        <div class="invalid-tooltip">
                                            Nama Lengkap tidak boleh kosong.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Date</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{$data->tx_time}}" name="tx_time" placeholder="Date" required disabled>
                                        <div class="invalid-tooltip">
                                            Nama Toko tidak boleh kosong.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Trx Amount</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{$data->total}}" name="total" placeholder="Trx Amount" required disabled>
                                        <div class="invalid-tooltip">
                                            Nama Merek tidak boleh kosong.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Response Code</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{$data->rc}}" name="rc" placeholder="RC" required disabled>
                                        <div class="invalid-tooltip">
                                            Alamat Toko tidak boleh kosong.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Status Reversal 1</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="tx_mti" placeholder="Select Status">
                                                        <option>Select Status</option>
                                                        <option @if($data->tx_mti == '0200') selected @endif value="0200">Non Reversal</option>
                                                        <option @if($data->tx_mti == '0400') selected @endif value="0400">Reversal</option>
                                        </select>    
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Status Reversal 2</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="rp_mti" placeholder="Select Status">
                                                        <option>Select Status</option>
                                                        <option @if($data->rp_mti == '0210') selected @endif value="0210">Non Reversal</option>
                                                        <option @if($data->rp_mti == '0410') selected @endif value="0410">Reversal</option>
                                        </select>    
                                    </div>
                                </div>
                            
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <a href="{{route('transactionBJB')}}">
                                            <button type="button" class="btn btn-primary">Back</button>
                                        </a>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


@endsection

@section('page-js')

@endsection

@section('bottom-js')

    <script src="{{asset('assets/js/form.validation.script.js')}}"></script>

@endsection
