<?php

namespace App\Exports;

use App\Models\Kegiatan;

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
use App\Transformers\KegiatanTransformer;


class KegiatanExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithDrawings
{
    function __construct($request, $kegiatan) {
        $this->tanggal_mulai = $request->tanggal_mulai;
        $this->tanggal_selesai = $request->tanggal_selesai;
        $this->count = 0;
        $this->kegiatan = $kegiatan;
    }

    public function collection()
    {
        $this->count = count($this->kegiatan);
        return $this->kegiatan;
    }

    public function map($kegiatan): array
    {
        return [
            $kegiatan['pemimpin'],
            $kegiatan['kuat_pers'],
            $kegiatan['kesatuan']['kesatuan'],
            Carbon::parse($kegiatan['w_kegiatan'])->translatedFormat('d F Y'),
            $kegiatan['logistik'],
            $kegiatan['sasaran'],
            $kegiatan['lokasi'],
            $kegiatan['lat'],
            $kegiatan['lng'],
            $kegiatan['hasil'],
            // $kegiatan['sampul']
        ];
    }

    public function headings() : array
    {
        return [
            ['Daftar Kegiatan'],
            ['Pada : '. Carbon::parse($this->tanggal_mulai)->translatedFormat('d F Y'). ' - ' .Carbon::parse($this->tanggal_selesai)->translatedFormat('d F Y')],[],[],
            ['Pemimpin', 'Kuat Pers', 'Kesatuan', 'Tanggal','Logistik', 'Sasaran', 'lokasi', 'lat', 'lng', 'hasil', 'foto']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $spreadsheet = $event->sheet->getParent();

                $event->sheet->mergeCells('A1:K1');
                $event->sheet->mergeCells('A2:K2');

                $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->applyFromArray(
                    array('horizontal' => 'left')
                );

                $styleHeader = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => '000000']
                        ],
                    ],
                ];

                
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);
                // $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
                // $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                // $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                // $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
                $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
                $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(50);

                $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
                $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(15);
                $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(10);
                $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(10);
                $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(20);

                $spreadsheet->getActiveSheet()->getStyle('A1:K2')->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('A1:K1')->getFont()->setSize(16);

                $spreadsheet->getActiveSheet()->getStyle('A2:K2')->getFont()->setSize(12);
                
                $spreadsheet->getActiveSheet()->getStyle('A1:K2')->getAlignment()->setVertical('center');
                $spreadsheet->getActiveSheet()->getStyle('A1:K2')->getAlignment()->applyFromArray(
                    array('horizontal' => 'center')
                );

                $spreadsheet->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);

                $spreadsheet->getActiveSheet()
                            ->getStyle("A5:K".(((int) $this->count) + 5))
                            ->applyFromArray($styleHeader);

                $spreadsheet->getActiveSheet()
                            ->getStyle("A5:K".(((int) $this->count) + 5))
                            ->getAlignment()
                            ->setVertical('center');

                $spreadsheet->getActiveSheet()
                            ->getStyle("A5:K".(((int) $this->count) + 5))
                            ->getAlignment()
                            ->setWrapText(true);

                $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(140);
            },

        ];
    }

    public function drawings()
    {
        $tmp = 4;
        $rowCount = 0;
        $data = [];

        foreach ($this->kegiatan as $key => $value) {
            if($value['sampul'] == null){
                continue;
            }
            $url = explode('/', $value['sampul']);
            $drawing = new Drawing();
            $drawing->setName('foto');
            $drawing->setPath(storage_path('app/kegiatan/'.$url[6]));
            $drawing->setHeight(155);
            $drawing->setOffsetY(($rowCount * 180) + (40 + ($rowCount*6.6)));
            $drawing->setOffsetX(10);
            $drawing->setCoordinates('K5');
            $tmp++;
            $rowCount++;
            $data[$key] = $drawing;
        }

        return $data;
    }

}
