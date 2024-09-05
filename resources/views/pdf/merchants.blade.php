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
    <h1>Merchants Report</h1>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 7%;">Id</th>
                <th style="width: 15%;">Account No</th>
                <th style="width: 17%;">Name</th>
                <th style="width: 22%;">Email</th>
                <th style="width: 15%;">Address</th>
                <th style="width: 10%;">City</th>
                <th style="width: 15%;">Phone</th>
                <th style="width: 8%;">TID</th>
                <th style="width: 18%;">Status Agen</th>
                <th style="width: 18%;">Activate Date</th>
                <th style="width: 15%;">Resign Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($merchants as $index => $merchant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $merchant->id }}</td>
                    <td>{{ $merchant->account_no ?? 'N/A' }}</td>
                    <td>{{ $merchant->name ?? 'N/A' }}</td>
                    <td>{{ $merchant->email ?? 'N/A' }}</td>
                    <td>{{ $merchant->address ?? 'N/A' }}</td>
                    <td>{{ $merchant->city ?? 'N/A' }}</td>
                    <td>{{ $merchant->phone ?? 'N/A' }}</td>
                    <td>{{ $merchant->tid ?? 'N/A' }}</td>
                    <td>{{ $merchant->status_agen ?? 'N/A' }}</td>
                    <td>{{ $merchant->activate_date ? $merchant->activate_date->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $merchant->resign_date ? $merchant->resign_date->format('Y-m-d') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
