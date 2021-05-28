<?php

namespace App\Exports;

use App\Models\Kegiatan;
use App\Models\JenisKegiatan;

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


class KegiatanExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    private $number = 1;

    function __construct($request, $kegiatan) {
        $this->rentang = $request->rentang;
        $this->id_jenis = $request->id_jenis;
        $this->tipe = $request->is_quick_response ? 'Quick Response' : 'Kegiatan';
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
            $this->number++,
            $kegiatan['detail'],
            $this->formatJenis($kegiatan['jenis']),
            Carbon::parse($kegiatan['w_kegiatan'])->translatedFormat('d F Y'),
            $kegiatan['user']['nrp'],
            $kegiatan['user']['nama'],
            $kegiatan['daftar_rekan'],
            $kegiatan['nomor_polisi'],
            $kegiatan['lat'],
            $kegiatan['lng'],
        ];
    }

    public function headings() : array
    {
        $jenis = ''; $pada = '';

        if($this->id_jenis != '') {
            $jenis = JenisKegiatan::whereIn('id', $this->id_jenis)->get()->pluck('jenis')->all();
            $jenis = implode(' , ', $jenis);
        }

        if ($this->rentang != '') {
            list($mulai, $selesai) = $this->rentang;
            $pada = 'Pada ' . Carbon::parse($mulai)->translatedFormat('d F Y'). ' - ' .Carbon::parse($selesai)->translatedFormat('d F Y');
        }


        return [
            ["Daftar " . $this->tipe . " \r\n ".$jenis],
            [ $pada ],[],[],
            ['No.', 'Detail', 'Jenis '.$this->tipe, 'Waktu '.$this->tipe, 'Nrp','Nama', 'Daftar Rekan', 'Nomor Polisi', 'Lat', 'Lng', 'Foto']
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
                $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(25);

                $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
                $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(15);
                $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(10);
                $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(10);
                $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
                $n=0; foreach($this->kegiatan as $k) { $n++;
                    $spreadsheet->getActiveSheet()->getRowDimension((5 + (int) $n))->setRowHeight(90);
                }

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

                $tmp = 6;
                $rowCount = 0;
                $data = [];

                foreach ($this->kegiatan as $key => $value) {
                    if($value['dokumentasi'] != null){
                        $url = explode('/', $value['dokumentasi']);
                        $drawing = new Drawing();
                        $drawing->setName('foto');
                        $drawing->setPath(storage_path('app/dokumentasi/'.$url[6]));
                        $drawing->setHeight(100);
                        $drawing->setOffsetY(10);
                        $drawing->setOffsetX(10);
                        $drawing->setCoordinates('K'.($tmp));
                        $drawing->setWorksheet($event->sheet->getDelegate());
                        $tmp++;
                        $rowCount++;
                        $data[] = $drawing;
                    }
                }

            },

        ];
    }

    public function formatJenis($jenis)
    {
        $viewJenis = '';
        foreach ($jenis as $key => $value) {
            switch ($value['jenis']['keterangan']) {
                case 'jenis_kegiatan':
                    $viewJenis .= "Jenis " . $this->tipe .  " : " . $value['jenis']['jenis'] . "\r\n";
                    break;
                case 'subjenis':
                    $viewJenis .= $value['jenis']['parent']['jenis'] . " : " . $value['jenis']['jenis'] . "\r\n";
                    break;
                case 'dropdown_subjenis':
                    $viewJenis .= $value['jenis']['parent']['jenis'] . " : " . $value['jenis']['jenis'] . "\r\n";
                    break;
                
                default:
                    break;
            }
        }

        return $viewJenis;
    }

}
