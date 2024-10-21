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
<div class="breadcrumb">
    <h1>Parameter Nilai</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row mb-4">
    <div class="col-md-12 mb-3">
        <div class="card text-left">
            <div class="card-body">
                <div class="row">
                    <h4 class="col-sm-12 col-md-6 card-title mb-3">Daftar Parameter Nilai</h4>
                </div>

                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif

                @if ($message = Session::get('failed'))
                    <div class="alert alert-failed">
                        <p>{{ $message }}</p>
                    </div>
                @endif

                <div class="table-responsive">
                <table id="default_ordering_table" class="display table table-striped table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Fitur</th>
                                <th scope="col">Nilai</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($data as $item)
                                <tr>
                                    <th scope="row">{{ $no }}</th>
                                    <td>{{ $item->comp_lbl  }}</td>
                                    <td>{{ $item->comp_act }}</td>
                                    <td>
                                        @if (in_array('edit_parameter', $routes_user))
                                            <a
                                                href="{{ route('masterdata_edit_parameter', ['id' => $item->comp_id]) }}">
                                                <button class="btn btn-primary ripple btn-sm m-1 edit-btn"
                                                    type="button">Edit</button>
                                            </a>
                                        @endif
                                    </td>

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
@endsection

@section('bottom-js')
<script src="{{ asset('assets/js/form.basic.script.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#deafult_ordering_table').DataTable({
            "searching": false
        });
    });
    function deleteConfirm(meta_id, service_id, seq) {
        var r = confirm("Apakah Anda yakin akan menghapus ini?");
        if (r == true) {
            var url = '{{ route("fee_destroy", ["meta_id" => ":meta_id", "service_id" => ":service_id", "seq" => ":seq"]) }}';
            url = url.replace(':meta_id', meta_id).replace(':service_id', service_id).replace(':seq', seq);

            $.post(url, {
                _token: "{{ csrf_token() }}",
            },
                function (data, status) {
                    location.reload(true);
                }).done(function () {
                    location.reload(true);
                }).fail(function () {
                    alert("Error, Please try again later!");
                })
        }
    }
</script>
@endsection