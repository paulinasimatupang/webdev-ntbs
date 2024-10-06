@extends('layouts.master')
@section('main-content')
    <div class="breadcrumb">
                <h1>Edit Terminal</h1>
                <!-- <ul>
                    <li><a href="">Selada</a></li>
                   
                </ul> -->
            </div>
            <div class="separator-breadcrumb border-top"></div>

            @if(Session('error'))
                @if(is_array(Session('error')))
                    @foreach (Session('error') as $key => $item)    
                    <div class="alert alert-danger" role="alert">
                        <strong class="text-capitalize">Error : </strong> {{ $item[0] }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong class="text-capitalize">Error : </strong> {{ Session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-5">
                        <div class="card-body">
                            <form action="{{route('terminal_update',[$terminal->id])}}" method="POST">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Merchant</label>
                                    <div class="col-sm-8">
                                        <select id="selectMerchant" name="merchant_id" class="form-control" readonly>
                                            <option value="">Select Merchant</option>
                                            @foreach($merchant as $item)
                                                <option @if($item->mid == $terminal->merchant_id) selected @endif value="{{$item->mid}}">{{$item->name}} ( {{$item->mid }} )</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- <div class="col-sm-2 text-left">
                                        <button id="buttonDelete" onClick="deleteConfirm()" type="button" class="btn btn-danger">Delete</button>
                                    </div> -->
                                </div>
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Terminal ID</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $terminal->tid }}" name="tid" placeholder="Terminal ID" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">ID Perangkat</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $terminal->imei }}" name="imei" placeholder="IMEI" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <a href="{{route('terminal')}}">
                                            <button type="button" class="btn btn-primary">Back</button>
                                        </a>
                                        <button type="submit" class="btn btn-primary">Save</button>
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
    <script>
        $(document).ready(function(){
            var value = $('#selectMerchant').find(":selected").val();
            if(value == ""){
                $('#selectMerchant').prop("readonly", false);
                $('#buttonDelete').prop("readonly", true);
            }
        });

        function deleteConfirm() {
            var r = confirm("Are you sure?");
            if (r == true) {
                $.post("{{route('terminal_delete',[$terminal->id, $terminal->merchant_id])}}",
                {
                    _token: "{{ csrf_token() }}",
                },
                function(data,status){
                    location.reload(true);
                }).success(function() {
                    location.reload(true);
                }).error(function() {
                    alert( "Error, Please try again later!" );
                })
            }
        }
    </script>

@endsection
