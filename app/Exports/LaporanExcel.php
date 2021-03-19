<?php

namespace App\Exports;

use DB;
use App\TipeLaporan;
use App\Kegiatan;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;

class LaporanExcel implements FromView, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    //use Queryarea;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function registerEvents() : array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $spreadsheet = $event->sheet->getParent();
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
                $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);
                $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
            }
        ];
    }

    public function view() : view
    {
        $data = $this->data;
        $arr['collection'] = $this->getTipeLaporan($data);

        if ($data['tipeLaporan'] == '1') {
            $viewLink = 'cetak/GiatRutin';
        } elseif ($data['tipeLaporan'] == '2') {
            $viewLink = 'cetak/K2yd';
        } elseif ($data['tipeLaporan'] == '3') {
            $viewLink = 'cetak/kunjungan';
        } elseif ($data['tipeLaporan'] == '4') {
            $viewLink = 'cetak/cegahPermaslahan';
        } elseif ($data['tipeLaporan'] == '5') {
            $viewLink = 'cetak/giatlangkah';
        } elseif ($data['tipeLaporan'] == '6') {
            $viewLink = 'cetak/satgas';
        }

        return view($viewLink, $arr);
    }

    public function getTIpeLaporan($data)
    {
        $data = Kegiatan::whereIn('id', $data['id'])->get();
        return $data;
    }

    public function getImage($data)
    {
        $data = Kegiatan::whereIn('id', $data['id'])->get();
    }
}
