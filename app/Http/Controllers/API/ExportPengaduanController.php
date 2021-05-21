<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\PengaduanTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ExportPengaduanController extends Controller
{
    public function index (Request $request) {
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);
        $pengaduan = Pengaduan::filteredCetak($request->option, $request->rentang);

        if (count($pengaduan->paginate(1)) === 0)
            return response()->json(['message' => 'Tidak ada content'], 204);

        $paginator = $pengaduan->paginate(10);
        $collection = $paginator->getCollection();


        return fractal()
                ->collection($collection)
                ->transformWith(new PengaduanTransformer(true))
                ->serializeWith(new DataArraySansIncludeSerializer)
                ->paginateWith(new IlluminatePaginatorAdapter($paginator))
                ->respond();
    }

    public function cetak (Request $request) {
        $user = $request->user();
        if(!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        $pengaduan = Pengaduan::filteredCetak($request->option, $request->rentang);
        $pengaduan = $pengaduan->whereNotIn('pengaduan.id', $request->id ?? [])->get();

        $this->generateExcel($pengaduan);
    }

    public function generateExcel($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/pengaduan.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 7;
        $colFoto = 'F';

        foreach ($data as $val) {
            $currentIndex = $startRowIndex + ($no - 1);

            $nama = ($val->user->pemilik->nik ? $val->user->pemilik->nik.' - ' : null)
                    .$val->user->pemilik->nama;
            $sheet->setCellValue('A' . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $nama);
            $sheet->setCellValue('C' . $currentIndex, $val->lokasi);
            $sheet->setCellValue('D' . $currentIndex, $val->keterangan);
            $sheet->setCellValue('E' . $currentIndex, $val->created_at);

            if ($val['foto'] && Storage::exists($val['foto'])) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val['id']);
                $imageDraw->setCoordinates('F' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['foto']));
                $imageDraw->setWidth(160);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
            }
            $no++;
        }

        $sheet->getColumnDimension($colFoto)->setWidth(40);
        $writer = IOFactory::createWriter($excel, 'Xlsx');

        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

}
