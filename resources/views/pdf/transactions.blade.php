<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 4px;
            text-align: left;
            overflow: hidden;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        h1 {
            font-size: 12px;
            text-align: center;
            margin-bottom: 10px;
        }
        th {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <h1>Transactions Report</h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Transaction Code</th>
                <th>Amount</th>
                <th>Fee</th>
                <th>Date</th>
                <th>Norek Pengirim</th>
                <th>Norek Penerima</th>
                <th>Tipe Produk</th>
                <th>Kode Agen</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction[0] }}</td>
                    <td>{{ $transaction[1] }}</td>
                    <td>{{ $transaction[2] }}</td>
                    <td>{{ $transaction[3] }}</td>
                    <td>{{ $transaction[4] }}</td>
                    <td>{{ $transaction[5] }}</td>
                    <td>{{ $transaction[6] }}</td>
                    <td>{{ $transaction[7] }}</td>
                    <td>{{ $transaction[8] }}</td>
                    <td>{{ $transaction[9] }}</td>
                    <td>{{ $transaction[10] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
