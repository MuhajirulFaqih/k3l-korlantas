<?php

namespace App\Http\Controllers\API;

use App\Models\BelanjaBidang;
use App\Models\DansosLaporan;
use App\Models\DansosPagu;
use App\Models\GiatDanaDesa;
use App\Http\Controllers\Controller;
use App\Models\Pendapatan;
use App\RincianBelanja;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\BelanjaBidangTransformer;
use App\Transformers\DansosLaporanTransformer;
use App\Transformers\DansosPaguTransformer;
use App\Transformers\GiatDanaDesaTransformer;
use App\Transformers\PendapatanTransformer;
use App\Transformers\RincianBelanjaTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class DanaReportController extends Controller
{
	public function pendapatan(Request $request)
    {
        $user = $request->user();
        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => "Terlarang"], 403);

        $pendapatan = Pendapatan::report($request)->orderBy('created_at', 'desc');

		$paginator = $pendapatan->paginate(10);
        $collection = $paginator->getCollection();
        
        if($request->desa == '')
        	return null;

        return fractal()
            ->collection($collection)
            ->transformWith(new PendapatanTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function belanja(Request $request)
    {
        $user = $request->user();
        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => "Terlarang"], 403);

        $belanja = BelanjaBidang::report($request)->orderBy('created_at', 'desc');

		$paginator = $belanja->paginate(10);
        $collection = $paginator->getCollection();
        
        if($request->desa == '')
        	return null;

        return fractal()
            ->collection($collection)
            ->transformWith(new BelanjaBidangTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function rincian(Request $request)
    {
        $user = $request->user();
        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => "Terlarang"], 403);

        $rincian = RincianBelanja::report($request)->orderBy('created_at', 'desc');

		$paginator = $rincian->paginate(10);
        $collection = $paginator->getCollection();
        
        if($request->desa == '')
        	return null;

        return fractal()
            ->collection($collection)
            ->transformWith(new RincianBelanjaTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function pendapatanDansos(Request $request)
    {
        $user = $request->user();
        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => "Terlarang"], 403);

        $pendapatanDansos = DansosPagu::report($request)->orderBy('created_at', 'desc');

		$paginator = $pendapatanDansos->paginate(10);
        $collection = $paginator->getCollection();
        
        if($request->desa == '')
        	return null;

        return fractal()
            ->collection($collection)
            ->transformWith(new DansosPaguTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function dansos(Request $request)
    {
        $user = $request->user();
        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => "Terlarang"], 403);

        $dansos = DansosLaporan::report($request)->orderBy('created_at', 'desc');

		$paginator = $dansos->paginate(10);
        $collection = $paginator->getCollection();
        
        if($request->desa == '')
        	return null;

        return fractal()
            ->collection($collection)
            ->transformWith(new DansosLaporanTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function exportDanaDesa(Request $request)
    {
        $reader = IOFactory::createReader('Xlsx');
        $pendapatan = Pendapatan::report($request)->orderBy('created_at', 'desc')->get();
        $belanja = BelanjaBidang::report($request)->orderBy('created_at', 'desc')->get();
        $rincian = RincianBelanja::report($request)->orderBy('created_at', 'desc')->get();

        $excel = $reader->load(storage_path("app/excel/dana-desa.xlsx"));
        $sheet = $excel->getSheet(0);
        $sheet2 = $excel->getSheet(1);
        $sheet3 = $excel->getSheet(2);
        $no = 1; $no2 = 1; $no3 = 1;
        $startRowIndex = 2;
        foreach ($pendapatan as $val) {
            $currentIndex = $startRowIndex + ($no - 1);
            $sheet->setCellValue('A' . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $val->bagihasilpajakDaerah);
            $sheet->setCellValue('C' . $currentIndex, $val->pendapatanaslidaerah);
            $sheet->setCellValue('D' . $currentIndex, $val->alokasidanaDesa);
            $sheet->setCellValue('E' . $currentIndex, $val->silpa);
            $sheet->setCellValue('F' . $currentIndex, substr($val->tahun_anggaran, 0, 4));
            $no++;
        }

        foreach ($belanja as $val) {
            $currentIndex2 = $startRowIndex + ($no2 - 1);
            $sheet2->setCellValue('A' . $currentIndex2, $no2);
            $sheet2->setCellValue('B' . $currentIndex2, $val->penyelenggaraan);
            $sheet2->setCellValue('C' . $currentIndex2, $val->pelaksanaan);
            $sheet2->setCellValue('D' . $currentIndex2, $val->pemberdayaan);
            $sheet2->setCellValue('E' . $currentIndex2, $val->pembinaan);
            $sheet2->setCellValue('F' . $currentIndex2, $val->tak_terduga);
            $sheet2->setCellValue('G' . $currentIndex2, substr($val->tahun_anggaran, 0, 4));
            $no2++;
        }

        foreach ($rincian as $val) {
            $currentIndex3 = $startRowIndex + ($no3 - 1);
            $sheet3->setCellValue('A' . $currentIndex3, $no3);
            $sheet3->setCellValue('B' . $currentIndex3, $val->uraian);
            $sheet3->setCellValue('C' . $currentIndex3, $val->jumlah);
            $sheet3->setCellValue('D' . $currentIndex3, substr($val->tahun_anggaran, 0, 4));
            $no3++;
        }

        $writer = IOFactory::createWriter($excel, 'Xlsx');
        ob_start();
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="Pendapatan-Dana-Desa.xlsx"');
        \header('Cache-Control: max-age=0');
        return $writer->save('php://output');
    }

    public function exportDanaSosial(Request $request)
    {
        $reader = IOFactory::createReader('Xlsx');
        $pendapatan = DansosPagu::report($request)->orderBy('created_at', 'desc')->get();
        $dansos = DansosLaporan::report($request)->orderBy('created_at', 'desc')->get();   

        $excel = $reader->load(storage_path("app/excel/dana-sosial.xlsx"));
        $sheet = $excel->getSheet(0);
        $sheet2 = $excel->getSheet(1);
        $no = 1; $no1 = 1;
        $startRowIndex = 2;
        $width = 100;
        foreach ($pendapatan as $val) {
            $currentIndex = $startRowIndex + ($no - 1);
            $sheet->setCellValue('A' . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $val->pagu);
            $sheet->setCellValue('C' . $currentIndex, $val->asal);
            $sheet->setCellValue('D' . $currentIndex, substr($val->tahun_anggaran, 0, 4));
            $no++;
        }

        foreach ($dansos as $val) {
            $currentIndex1 = $startRowIndex + ($no1 - 1);
            $sheet2->setCellValue('A' . $currentIndex1, $no1);
            $sheet2->setCellValue('B' . $currentIndex1, $val->kepada);
            $sheet2->setCellValue('C' . $currentIndex1, $val->kegunaan);
            $sheet2->setCellValue('D' . $currentIndex1, $val->jumlah);
            $sheet2->setCellValue('E' . $currentIndex1, substr($val->tahun_anggaran, 0, 4));

            if ($val->foto && Storage::exists($val->foto)) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val->id);
                $imageDraw->setCoordinates('F' . $currentIndex1);
                $imageDraw->setPath(storage_path('app/' . $val->foto));
                $imageDraw->setWidthAndHeight(200, 200);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet2);
                $sheet2->getRowDimension($currentIndex1)->setRowHeight($imageDraw->getHeight());
            }
            $no1++;
        }
        $sheet2->getColumnDimension('F')->setWidth(50);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        ob_start();
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="Pendapatan-Dana-Desa.xlsx"');
        \header('Cache-Control: max-age=0');
        return $writer->save('php://output');
    }
}