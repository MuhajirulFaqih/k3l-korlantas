<?php

namespace App\Exports;

use App\Absensi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsensiExport implements FromView, WithEvents
{
	protected $data = null;

	public function __construct($data)
    {
        $this->data = $data;
    }

    public function registerEvents(): array
    {
        return [
        	AfterSheet::class => function (AfterSheet $event) {
        		$spreadsheet = $event->sheet->getParent();
        		$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(3);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(11);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        	}
        ];
    }

    public function view() : View
    {
    	return view('cetak/absensi', ['data' => $this->data]);
    }
}
