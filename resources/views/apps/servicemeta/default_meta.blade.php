@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Meta Default untuk Service: {{ $service_id }}</h2>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Meta ID</th>
                <th>Meta Default</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($serviceMeta as $meta)
            <tr>
                <td>{{ $meta->meta_id }}</td>
                <td>{{ $meta->meta_default }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center">Tidak ada meta default yang ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('servicemeta') }}" class="btn btn-primary">Kembali</a>
</div>
@endsection
