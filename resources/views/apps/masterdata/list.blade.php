@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Master Data</h1>
        <ul>
            <li>Selada</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center mb-3">
            <div class="input-group">
                <a href="{{ route('masterdata_create') }}">
                    <button class="btn btn-add-new ripple m-1" type="button">Add New</button>
                </a>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="card text-left">
                <div class="card-body">
                    <div class="row">
                        <h4 class="col-sm-12 card-title mb-3">List Master Data</h4>
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
                        <table id="default_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groups as $group)
                                    <tr>
                                        <td>{{ $group->id }}</td>
                                        <td>{{ $group->name }}</td>
                                        <td>{{ $group->description }}</td>
                                        <td>{{ $group->status ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            <a href="{{ route('masterdata_edit', $group->id) }}">
                                                <button class="btn btn-edit ripple btn-sm m-1" type="button">Edit</button>
                                            </a>
                                            <a href="#" onclick="deleteConfirm({{ $group->id }}); return false;" class="btn btn-delete ripple btn-sm m-1">Delete</a>
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
    <script src="{{ asset('assets/js/vendor/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatables.script.js') }}"></script>
@endsection

@section('bottom-js')
    <script src="{{ asset('assets/js/form.basic.script.js') }}"></script>
    <script>
        function deleteConfirm(id) {
            var r = confirm("Are you sure?");
            if (r == true) {
                var url = '{{ route("masterdata_destroy", ":id") }}';
                url = url.replace(':id', id);

                $.post(url, {
                    _token: "{{ csrf_token() }}",
                }).done(function() {
                    location.reload(true);
                }).fail(function() {
                    alert("Error, Please try again later!");
                });
            }
        }
    </script>
    <style>
        .btn-add-new {
            background-color: #0a6e44;
            border: none;
            color: white;
        }

        .btn-edit {
            background-color: #0182bd;
            border: none;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545; /* Red */
            border: none;
            color: white;
        }

        .btn-add-new:hover, .btn-edit:hover, .btn-delete:hover {
            opacity: 0.8; /* Slightly darken on hover */
        }
    </style>
@endsection
