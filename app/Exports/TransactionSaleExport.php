<?php

namespace App\Exports;

use App\Entities\TransactionBJB;
use App\Entities\TransactionSaleBJB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

//class TransactionSaleExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithEvents, WithStyles, WithTitle
class TransactionSaleExport implements FromView, WithColumnFormatting, WithEvents, WithStyles, WithTitle, ShouldAutoSize
{
    use Exportable;
    public function __construct(Request $request){
    	$this->request = $request;
    }

    public function title(): string
    {
        return 'REPORT_DETAIL';
    }

    public function headings(): array
    {
        return [
	'stan',
	'request_time',
	'tx_time',
	'tid',
	'mid',
	'agent_name',
	'billid',
	'proc_code',
	'message_status',
	'rc',
	'status',
	'reversal_stan',
	'reversal_rc',
	'host_ref',
	'tx_pan',
	'product_name',
	'transaction_name',
	'nominal',
	'fee',
	'agent_fee',
	'bjb_fee',
	'selada_fee',
	'buffer_14',
	'total',
	'src_account',
	'dst_account'
        ];
    }

    public function map($transaction): array{
	return [
	$transaction->stan,
	$transaction->request_time,
	$transaction->tx_time,
	$transaction->tid,
	$transaction->mid,
	$transaction->agent_name,
	$transaction->billid,
	$transaction->proc_code,
	$transaction->message_status,
	$transaction->rc,
	$transaction->status,
	$transaction->reversal_stan,
	$transaction->reversal_rc,
	$transaction->host_ref,
	$transaction->tx_pan,
	$transaction->product_name,
	$transaction->transaction_name,
	$transaction->nominal,
	$transaction->fee,
	$transaction->agent_fee,
	$transaction->bjb_fee,
	$transaction->selada_fee,
	$transaction->buffer_14,
	$transaction->total,
	$transaction->src_account,
	$transaction->dst_account
	];
    }

    public function columnFormats(): array
    {
        return [
	'A' => NumberFormat::FORMAT_NUMBER,
	'O' => NumberFormat::FORMAT_NUMBER,
	'U' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
	'V' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
	'W' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
	'X' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
	'G' => NumberFormat::FORMAT_NUMBER,
	'R' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
	'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
	'T' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
	'Z' => NumberFormat::FORMAT_NUMBER,
	'H' => NumberFormat::FORMAT_TEXT
	];
    }

    public function registerEvents(): array
    {
        return [
        AfterSheet::class    => function(AfterSheet $event) {
		$cellRange = 'E:E';
                $event->sheet->getDelegate()->getStyle($cellRange)
                        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$cellRange = 'H:H';
                $event->sheet->getDelegate()->getStyle($cellRange)
                        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$cellRange = 'U:U';
                $event->sheet->getDelegate()->getStyle($cellRange)
                        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$cellRange = 'V:V';
                $event->sheet->getDelegate()->getStyle($cellRange)
                        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_JUSTIFY)
                                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$cellRange = 'A1:Z1';
		$event->sheet->getDelegate()->getStyle($cellRange)
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
					->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$event->sheet->getDelegate()->getStyle('A1:Z'.($this->rowCount+1))->applyFromArray(
                	[
                    		'borders' => [
                        		'allBorders' => [
                            			'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        		],
                    		]
                	]
            	);
	}
	];
    }

    public function styles(Worksheet $sheet)
    {
        return [
		'A1:Z1' => ['font' => ['bold' => true]]
	];
    }

//    public function query()
    public function view(): View
    {
	$request = $this->request;
	$data = TransactionSaleBJB::select('*');
        //$data = TransactionBJB::query();

//	if(!$this->request->has('service') || $this->request->get('service')=='' || $this->request->get('status')=='Select Service'){
//	
//	        $data->where('product_name', '!=', 'E82561');
//        	$data->where('product_name', '!=', 'E82560');
//	}

	$data->where(function($query)
                {
                    $query->where('product_name', '=', 'E82561')
                ->orWhere('product_name', '=', 'E82560');
                });
        if($this->request->has('search') && $this->request->get('search')!=''){
            $data->where(function($query) use ($request)
            {
                $query->where('tid', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('mid', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('agent_name', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('transaction_name', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('product_name', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('stan', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('message_id', 'like', '%'.$this->request->get("search").'%')
                ->orWhere('status', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('rc', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('message_status', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('tx_pan', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('src_account', 'like', '%' . $this->request->get('search') . '%')
                ->orWhere('dst_account', 'like', '%' . $this->request->get('search') . '%');
            });
        }

        if($this->request->has('start_date') && $this->request->get('start_date')!=''){
            $data->where('tx_time', '>', $this->request->get('start_date').' 00:00:00');
        }

        if($this->request->has('end_date') && $this->request->get('end_date')!=''){
            $data->where('tx_time', '<=', $this->request->get('end_date').' 23:59:59');
        }


        if($this->request->has('tid') && $this->request->get('tid')!=''){
            $data->where('tid', '=', $this->request->get('tid'));
        }
        if($this->request->has('mid') && $this->request->get('mid')!=''){
            $data->where('mid', '=', $this->request->get('mid'));
        }
        if($this->request->has('agent_name') && $this->request->get('agent_name')!=''){
            $data->where('agent_name', '=', $this->request->get('agent_name'));
        }
        if($this->request->has('message_status') && $this->request->get('message_status')!=''){
                $data->where('message_status', '=', $this->request->get('message_status'));
        }
        if($this->request->has('status') && $this->request->get('status')!='' && $this->request->get('status')!='Select Status'){
            //$data->where('status', '=', $this->request->get('status'));
//	    if ($this->request->get('status') == 'Failed'){
//	    	$data->where('rc', '!=', '00');
// 	    }
//	    else{
//	    	$data->where('rc', '=', '00');
//  	    }
	    if ($this->request->get('status') == 'Success'){
                $data->where('rc', '=', '00');
                $data->where('message_status', '=', '00');
                $data->whereNull('reversal_stan');
            }
            else{
                //$data->where('rc', '!=', '00');
                $data->where(function($query)
                {
                    $query->where('rc', '!=', '00')
                    ->orWhereNotNull('reversal_stan');
                });
            }
        }
        if($this->request->has('rc') && $this->request->get('rc')!=''){
            $data->where('rc', '=', $this->request->get('rc'));
        }

        if($this->request->has('stan') && $this->request->get('stan')!=''){
            $data->where('stan', '=', $this->request->get('stan'));
        }
        if($this->request->has('message_id') && $this->request->get('message_id')!=''){
            $data->where('message_id', '=', $this->request->get('message_id'));
        }
        if(false){  // $this->request->has('service') && $this->request->get('service')!='' && $this->request->get('status')!='Select Service'){
            $service = $this->request->get('service');
            if ($service == 'Tarik Tunai'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0010')
                        ->orWhere('product_name', '=', 'MA0012');
                });
                // $data->whereIn('product_name', ['MA0010','MA0012']);
            }
            else if ($service == 'Payment Transfer Antar Bank'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0021')
                    ->orWhere('product_name', '=', 'MA0023');
                });
            }
            else if ($service == 'Pemindahbukuan'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0031')
                ->orWhere('product_name', '=', 'MA0033');
            });
            
            }
            else if ($service == 'Setor Tunai'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0041')
                ->orWhere('product_name', '=', 'MA0043');
                });
            
            }
            else if ($service == 'Mini Statement'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0050')
                ->orWhere('product_name', '=', 'MA0051');
            });
            
            }
            else if ($service == 'Info Saldo'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0060')
                ->orWhere('product_name', '=', 'MA0063');
            });
            
            }
            else if ($service == 'Ganti PIN'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0071')
                ->orWhere('product_name', '=', 'MA0073');
            });
            
            }
            else if ($service == 'Buka Rekening'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0081')
                ->orWhere('product_name', '=', 'MA0083');
            });
            
            }
            else if ($service == 'Batal Rekening'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0091')
                ->orWhere('product_name', '=', 'MA0093');
            });
            }   
            else if ($service == 'PBB'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'P00031')
                ->orWhere('product_name', '=', 'P00033');
                });
            
            }
        }

        $data->orderBy('created_at', 'desc');
//	dd($data->toSql());die();

	$this->rowCount = $data->count();

	$data = $data->get();
	return view('exports.transactions_sale', [
            'data' => $data
        ]);

	//return $data->query();
	//return $data;
   }
}