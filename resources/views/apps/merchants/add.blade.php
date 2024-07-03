@extends('layouts.master')
@section('main-content')
    <div class="breadcrumb">
                <h1>Add Merchant</h1>
                <ul>
                    <li><a href="">Selada</a></li>
                   
                </ul>
            </div>
            <div class="separator-breadcrumb border-top"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-5">
                        <div class="card-body">
                            <form action="{{route('merchant_store')}}" method="POST">
                                {{-- <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Terminal</label>
                                    <div class="col-sm-10">
                                        <select name="terminal_id" class="form-control" required>
                                            <option value="">Select Terminal</option>
                                            @foreach($terminal as $item)
                                                <option value="{{$item->tid}}">{{$item->tid}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Merchant ID</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('mid')}}" name="mid" placeholder="Merchant ID" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('username')}}" name="username" placeholder="Username" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" value="{{Request::old('email')}}" name="email" placeholder="Email" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" value="{{Request::old('password')}}" name="password" placeholder="Password" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Fullname</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('fullname')}}" name="fullname" placeholder="Fullname" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Account Number</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('no')}}" name="no" placeholder="Account Number" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Address</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('address')}}" name="address" placeholder="Address" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">City</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('city')}}" name="city" placeholder="City" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Phone</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('phone')}}" name="phone" placeholder="Phone" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Screen</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('screen_id')}}" name="screen_id" placeholder="Screen" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Is User Mireta</label>
                                    <div class="col-sm-10">
                                        <select name="is_user_mireta" class="form-control">
                                            <option value="">Select</option>
                                            <option value="true">Yes</option>
                                            <option value="false">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <a href="{{route('merchant')}}">
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

@endsection
