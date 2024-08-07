@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Transaction Ranking by Merchant User</h2>
    <table class="table">
        <thead>
            <tr>
                <th>User UID</th>
                <th>Transaction Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankedTransactions as $transaction)
                <tr>
                    <td>{{ $transaction->user_uid }}</td>
                    <td>{{ $transaction->transaction_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
