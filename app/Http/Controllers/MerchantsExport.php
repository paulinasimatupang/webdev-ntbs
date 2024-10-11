<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles; 
use Maatwebsite\Excel\Concerns\WithColumnWidths; 
use Maatwebsite\Excel\Events\AfterSheet; 
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use App\Models\Merchant;
use Carbon\Carbon; 

class MerchantsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $query;
    protected $viewType;

    public function __construct($query, $viewType)
    {
        $this->query = $query;
        $this->viewType = $viewType;
    }

    public function collection(): Collection
    {
        $agen = $this->query->get();

        $sortedAgen = $agen->sortBy(function ($agen) {
            return Carbon::parse($agen->active_at);
        });

        return $sortedAgen->map(function ($item) {
            return $this->map($item);
        });
    }

    protected function map($item)
    {
        return [
            $item->mid,
            $item->name,
            $item->no_telp,
            $item->phone,
            $item->email,
            $item->no_ktp,
            $item->no_npwp,
            $item->pekerjaan,
            $item->address,
            $item->rt,
            $item->rw,
            $item->kelurahan,
            $item->kecamatan,
            $item->city,
            $item->provinsi,
            $item->kode_pos,
            $item->jenis_agen,
            $item->no,
            $item->active_at,
        ];
    }

    public function headings(): array
    {
        switch ($this->viewType) {
            case 1:
                return [
                    'Kode Agen',
                    'Nama',
                    'No Telepon Rumah',
                    'No Telepon HP',
                    'Email',
                    'NIK',
                    'NPWP',
                    'Pekerjaan',
                    'Alamat',
                    'RT',
                    'RW',
                    'Kelurahan',
                    'Kecamatan',
                    'Kota/Kabupaten',
                    'Provinsi',
                    'Kode Pos',
                    'Jenis Agen',
                    'No Rekening',
                    'Tanggal Approve',
                ];
            default:
                return [];
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 30,
            'F' => 20,
            'G' => 20,
            'H' => 15,
            'I' => 70,
            'J' => 10,
            'K' => 10,
            'L' => 15,
            'M' => 15,
            'N' => 20,
            'O' =>20,
            'P' => 10,
            'Q' => 20,
            'R' => 20,
            'S' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    }
}
