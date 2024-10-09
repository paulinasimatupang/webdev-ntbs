<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class TransactionsExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    protected $query;
    protected $viewType;

    public function __construct($query, $viewType)
    {
        $this->query = $query->with(['merchant', 'service', 'transactionStatus']);
        $this->viewType = $viewType;
    }

    public function collection(): Collection
    {
        $transactions = $this->query->get();
    
        $sortedTransactions = $transactions->sortBy(function ($transaction) {
            return Carbon::parse($transaction->transaction_time);
        });
    
        $totalAmount = 0;
        $totalFee = 0;
    
        foreach ($sortedTransactions as $transaction) {
            $totalAmount += $transaction->amount;
            $totalFee += $transaction->fee;
        }
    
        $mappedData = $sortedTransactions->map(function ($item) {
            return $this->map($item);
        });
    
        $mappedData->push($this->mapTotal($totalAmount, $totalFee));
    
        return $mappedData;
    }
    


    public function headings(): array
    {
        switch ($this->viewType) {
            case 1:
                return ['Kode Transaksi', 'Tanggal', 'Kode Agen', 'Cabang', 'Tipe Transaksi', 'Tipe Produk', 'Nominal', 'Fee', 'Nomor Rekening Penerima', 'Nomor Rekening Pengirim', 'Status'];
            case 2:
                return ['Name', 'Fee', 'Status'];
            case 3:
                return ['Name', 'Amount', 'Status'];
            default:
                return [];
        }
    }

    public function map($item): array
    {
        switch ($this->viewType) {
            case 1:
                return [
                    $item->transaction_code,
                    $this->formatTransactionTime($item->transaction_time),
                    $item->kode_agen,
                    optional(optional($item->merchant)->user)->branchid ?? '',
                    trim(preg_replace('/\s+/', ' ', ucwords(preg_replace('/\b(form|review|otp|bayar)\b/i', '', $item->service->service_name ?? '')))),
                    $item->transaction_type,
                    number_format($item->amount, 2, ',', '.'),
                    number_format($item->fee, 2, ',', '.'),
                    $item->rekening_penerima,
                    $item->rekening_pengirim,
                    $item->transactionStatus->transaction_status_desc ?? '',
                ];
            case 2:
                return [
                    optional($item->merchant)->name ?? '',
                    number_format($item->fee, 2, ',', '.'),
                    $item->transactionStatus->transaction_status_desc ?? '',
                ];
            case 3:
                return [
                    optional($item->merchant)->name ?? '',
                    number_format($item->amount, 2, ',', '.'),
                    $item->transactionStatus->transaction_status_desc ?? '',
                ];
            default:
                return [];
        }
    }

    private function mapTotal($totalAmount, $totalFee): array
    {
        switch ($this->viewType) {
            case 1:
                return [
                    'Total',
                    '',
                    '',
                    '',
                    '',
                    '',
                    number_format($totalAmount, 2, ',', '.'),
                    number_format($totalFee, 2, ',', '.'),
                    '',
                    '',
                    '',
                ];
            case 2:
                return [
                    'Total',
                    number_format($totalFee, 2, ',', '.'),
                    '',
                ];
            case 3:
                return [
                    'Total',
                    number_format($totalAmount, 2, ',', '.'),
                    '',
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
            'D' => 15,
            'E' => 25,
            'F' => 20,
            'G' => 20,
            'H' => 15,
            'I' => 25,
            'J' => 25,
            'K' => 15,
        ];
    }

    private function formatTransactionTime($transactionTime)
    {
        if ($transactionTime instanceof \Carbon\Carbon) {
            return $transactionTime->format('Y-m-d H:i:s');
        } elseif (is_string($transactionTime)) {
            $carbonTime = Carbon::parse($transactionTime);
            return $carbonTime->format('Y-m-d H:i:s');
        } else {
            return '';
        }
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    }
}
