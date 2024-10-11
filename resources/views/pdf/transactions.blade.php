<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4 landscape; /* Mengatur ukuran kertas A4 horizontal */
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
    </style>
</head>
<body>
    <h1>Data Transaksi Agen Laku Pandai</h1>
    <table>
        <thead>
            <tr>
                @foreach(['Kode Transaksi', 'Tanggal', 'Kode Agen', 'Cabang', 'Tipe Transaksi', 'Tipe Produk', 'Nominal', 'Fee', 'Nomor Rekening Penerima', 'Nomor Rekening Pengirim', 'Status'] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_code ?? 'N/A' }}</td>
                    <td>{{ $transaction->transaction_time ?? 'N/A' }}</td>
                    <td>{{ $transaction->kode_agen ?? 'N/A' }}</td>
                    <td>{{ $transaction->kode_agen ?? 'N/A' }}</td>
                    <td>{{ $transaction->transaction_type ?? 'N/A' }}</td>
                    <td>{{ $transaction->service_id ?? 'N/A' }}</td>
                    <td>{{ number_format($transaction->amount ?? 0, 2, ',', '.') }}</td>
                    <td>{{ number_format($transaction->fee ?? 0, 2, ',', '.') }}</td>
                    <td>{{ $transaction->rekening_penerima ?? 'N/A' }}</td>
                    <td>{{ $transaction->rekening_pengirim ?? 'N/A' }}</td>
                    <td>{{ $transaction->transaction_status_id ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
