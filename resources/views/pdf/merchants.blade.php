<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4 landscape;
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
    <h1>Data Agen Laku Pandai</h1>
    <table>
        <thead>
            <tr>
                <th style="width: 7%;">Kode Agen</th>
                <th style="width: 12%;">Nama</th>
                <th style="width: 12%;">No Telepon Rumah</th>
                <th style="width: 12%;">No Telepon HP</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 10%;">NIK</th>
                <th style="width: 10%;">NPWP</th>
                <th style="width: 12%;">Pekerjaan</th>
                <th style="width: 10%;">Alamat</th>
                <th style="width: 5%;">RT</th>
                <th style="width: 5%;">RW</th>
                <th style="width: 10%;">Kecamatan</th>
                <th style="width: 10%;">Kelurahan</th>
                <th style="width: 10%;">Kota/Kabupaten</th>
                <th style="width: 10%;">Provinsi</th>
                <th style="width: 8%;">Kode Pos</th>
                <th style="width: 10%;">Jenis Agen</th>
                <th style="width: 12%;">No Rekening</th>
                <th style="width: 12%;">Tanggal Approve</th>
            </tr>
        </thead>
        <tbody>
            @foreach($merchants as $index => $merchant)
                <tr>
                    <td>{{ $merchant->mid }}</td>
                    <td>{{ $merchant->name }}</td>
                    <td>{{ $merchant->no_telp }}</td>
                    <td>{{ $merchant->phone }}</td>
                    <td>{{ $merchant->email }}</td>
                    <td>{{ $merchant->no_ktp }}</td>
                    <td>{{ $merchant->no_npwp }}</td>
                    <td>{{ $merchant->pekerjaan }}</td>
                    <td>{{ $merchant->address }}</td>
                    <td>{{ $merchant->rt }}</td>
                    <td>{{ $merchant->rw }}</td>
                    <td>{{ $merchant->kecamatan }}</td>
                    <td>{{ $merchant->kelurahan }}</td>
                    <td>{{ $merchant->city }}</td>
                    <td>{{ $merchant->provinsi }}</td>
                    <td>{{ $merchant->kode_pos }}</td>
                    <td>{{ $merchant->jenis_agen }}</td>
                    <td>{{ $merchant->no }}</td>
                    <td>{{ $merchant->active_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
