<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Kesatuan;

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
use App\Transformers\absensiTransformer;


class absensiExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    private $number = 1;

    function __construct($request, $absensi) {
        $this->rentang = $request->rentang;
        $this->kesatuan = $request->kesatuan;
        $this->count = 0;
        $this->absensi = $absensi;
    }

    public function collection()
    {
        $this->count = count($this->absensi);
        return $this->absensi;
    }

    public function map($absensi): array
    {

        return [
            $this->number++,
            $absensi['personil']['nrp'],
            $absensi['personil']['pangkat'] . ' ' . $absensi['personil']['nama'] . ' ' . $absensi['personil']['jabatan'] ,
            $absensi['personil']['kesatuan_lengkap'],
            Carbon::parse($absensi['waktu_mulai'])->translatedFormat('d F Y H:i:s'),
            $absensi['lokasi_datang'],
            $absensi['kondisi_datang'],
            Carbon::parse($absensi['waktu_selesai'])->translatedFormat('d F Y H:i:s'),
            $absensi['lokasi_pulang'],
            $absensi['kondisi_pulang'],
        ];
    }

    public function headings() : array
    {
        $pada = ''; $kesatuan = '';
        if ($this->rentang != '') {
            list($mulai, $selesai) = $this->rentang;
            $pada = 'Pada ' . Carbon::parse($mulai)->translatedFormat('d F Y'). ' - ' .Carbon::parse($selesai)->translatedFormat('d F Y');
        }

        if($this->kesatuan != '') {
            $kesatuan = Kesatuan::find($this->kesatuan);
            $kesatuan = $kesatuan->kesatuan;
        }

        return [
            ["Daftar Absensi " . (ucwords(strtolower($kesatuan)))],
            [ $pada ],[],[],
            ['No.', 'NRP', 'Nama ', 'Kesatuan', 'Datang', 'Lokasi', 'Kondisi', 'Pulang', 'Lokasi', 'Kondisi', 'Dokumentasi (Datang)', 'Dokumentasi (Pulang)']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $spreadsheet = $event->sheet->getParent();

                $event->sheet->mergeCells('A1:L1');
                $event->sheet->mergeCells('A2:L2');

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
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(25);

                $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
                $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(15);
                $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(10);
                $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(10);
                $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(20);

                $n=0; foreach($this->absensi as $a) { $n++;
                    $spreadsheet->getActiveSheet()->getRowDimension((5 + (int) $n))->setRowHeight(90);
                }

                $spreadsheet->getActiveSheet()->getStyle('A1:L2')->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('A1:L1')->getFont()->setSize(16);

                $spreadsheet->getActiveSheet()->getStyle('A2:L2')->getFont()->setSize(12);
                
                $spreadsheet->getActiveSheet()->getStyle('A1:L2')->getAlignment()->setVertical('center');
                $spreadsheet->getActiveSheet()->getStyle('A1:L2')->getAlignment()->applyFromArray(
                    array('horizontal' => 'center')
                );

                $spreadsheet->getActiveSheet()->getStyle('A5:L5')->getFont()->setBold(true);

                $spreadsheet->getActiveSheet()
                            ->getStyle("A5:L".(((int) $this->count) + 5))
                            ->applyFromArray($styleHeader);

                $spreadsheet->getActiveSheet()
                            ->getStyle("A5:L".(((int) $this->count) + 5))
                            ->getAlignment()
                            ->setVertical('center');

                $spreadsheet->getActiveSheet()
                            ->getStyle("A5:L".(((int) $this->count) + 5))
                            ->getAlignment()
                            ->setWrapText(true);

                $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(140);

                $tmp = 6;
                $rowCount = 0;
                $data = [];

                foreach ($this->absensi as $key => $value) {
                    if($value['dokumentasi_datang'] != null){
                        
                        $url = explode('/', $value['dokumentasi_datang']);
                        $drawing = new Drawing();
                        $drawing->setName('foto');
                        $drawing->setPath(storage_path('app/absensi/'.$url[6]));
                        $drawing->setHeight(100);
                        $drawing->setOffsetY(10);
                        $drawing->setOffsetX(10);
                        $drawing->setCoordinates('K'.($tmp));
                        $drawing->setWorksheet($event->sheet->getDelegate());
                        
                        if($value['dokumentasi_pulang'] != null) {
                            $url = explode('/', $value['dokumentasi_pulang']);
                            $drawing2 = new Drawing();
                            $drawing2->setName('foto');
                            $drawing2->setPath(storage_path('app/absensi/'.$url[6]));
                            $drawing2->setHeight(100);
                            $drawing2->setOffsetY(10);
                            $drawing2->setOffsetX(10);
                            $drawing2->setCoordinates('L'.($tmp));
                            $drawing2->setWorksheet($event->sheet->getDelegate());
                        }

                        $tmp++;
                        $rowCount++;
                        $data[] = $drawing;
                    }
                }

            },

        ];
    }

}
