<?php

namespace App\Exports;

use App\Models\Kejadian;
use App\Models\TindakLanjut;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithMapping;

use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\KejadianTransformer;


class KejadianExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    private $number = 1;

    function __construct($request, $kejadian) {
        $this->rentang = $request->rentang;
        $this->count = 0;
        $this->kejadian = $kejadian;
    }

    public function collection()
    {
        $this->count = count($this->kejadian);
        return $this->kejadian;
    }

    public function map($kejadian): array
    {
        $t = '';
        $nt = 0;
        foreach($kejadian['tindak_lanjut'] as $v => $r) { $nt++;
            $t .= $nt.'.) ';
            $t .= (isset($r['user']['nrp']) ? $r['user']['nrp'].' - ' : '');
            $t .= $r['user']['nama']."\r";
            $t .= "     ".$r['created_at']."";
            $t .= " - ".$r['status'] == "selesai" ? " (Selesai)" : " (Proses penanganan)";
            $t .= "\r";
            $t .= "     ".$r['keterangan']." \r";
        }

        return [
            $this->number++,
            $kejadian['kejadian'],
            $kejadian['lokasi'],
            ($kejadian['user']['nrp'] ? $kejadian['user']['nrp'] . ' - ' : '' ) . $kejadian['user']['nama'],
            Carbon::parse($kejadian['w_kejadian'])->translatedFormat('d F Y'),
            $kejadian['keterangan'],
            $kejadian['lat'],
            $kejadian['lng'],
            $t,
        ];
    }

    public function headings() : array
    {
        $pada = '';
        if ($this->rentang != '') {
            list($mulai, $selesai) = $this->rentang;
            $pada = 'Pada ' . Carbon::parse($mulai)->translatedFormat('d F Y'). ' - ' .Carbon::parse($selesai)->translatedFormat('d F Y');
        }


        return [
            ["Daftar Kejadian"],
            [ $pada ],[],[],
            ['No.', 'Kejadian', 'Lokasi ', 'Nama', 'Waktu kejadian', 'Keterangan', 'Lat', 'Lng', 'Tindak lanjut']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $spreadsheet = $event->sheet->getParent();

                $event->sheet->mergeCells('A1:I1');
                $event->sheet->mergeCells('A2:I2');

                $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->applyFromArray( array('horizontal' => 'left') );

                $styleHeader = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => '000000']
                        ],
                    ],
                ];

                
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(8);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(45);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(26);
                $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(30);
                $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(50);

                $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
                $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(15);
                $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(10);
                $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(10);
                $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(20);

                $spreadsheet->getActiveSheet()->getStyle('A1:I2')->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('A1:I1')->getFont()->setSize(16);

                $spreadsheet->getActiveSheet()->getStyle('A2:I2')->getFont()->setSize(12);
                
                $spreadsheet->getActiveSheet()->getStyle('A1:I2')->getAlignment()->setVertical('center');
                $spreadsheet->getActiveSheet()->getStyle('A1:I2')->getAlignment()->applyFromArray(
                    array('horizontal' => 'center')
                );

                $spreadsheet->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);

                $spreadsheet->getActiveSheet()
                            ->getStyle("A5:I".(((int) $this->count) + 5))
                            ->applyFromArray($styleHeader);

                $spreadsheet->getActiveSheet()
                            ->getStyle("A5:I".(((int) $this->count) + 5))
                            ->getAlignment()
                            ->setVertical('center');

                $spreadsheet->getActiveSheet()
                            ->getStyle("A5:I".(((int) $this->count) + 5))
                            ->getAlignment()
                            ->setWrapText(true);

                $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(140);

            },

        ];
    }

}
