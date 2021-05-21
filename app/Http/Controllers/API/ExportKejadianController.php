<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kejadian;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\KejadianTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Spatie\Fractalistic\ArraySerializer;

class ExportKejadianController extends Controller
{
    public function index (Request $request) {
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);
        $kejadian = Kejadian::filteredCetak($request->option, $request->rentang);

        if (count($kejadian->paginate(1)) === 0)
            return response()->json(['message' => 'Tidak ada content'], 204);

        $paginator = $kejadian->paginate(10);
        $collection = $paginator->getCollection();


        return fractal()
                ->collection($collection)
                ->transformWith(new KejadianTransformer(true))
                ->serializeWith(new DataArraySansIncludeSerializer)
                ->paginateWith(new IlluminatePaginatorAdapter($paginator))
                ->respond();
    }

    public function cetak (Request $request) {
        $user = $request->user();
        if(!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        $kejadian = Kejadian::filteredCetak($request->option, $request->rentang);
        $kejadian = $kejadian->whereNotIn('kejadian.id', $request->id ?? [])->get();

        $this->generateExcel($kejadian);
    }

    public function generateExcel($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/kejadian.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 7;
        $colFoto = 'G';

        foreach ($data as $val) {
            $currentIndex = $startRowIndex + ($no - 1);

            $nama = (isset($val->user->pemilik->nik) ? $val->user->pemilik->nik.' - ' : null)
                    .(isset($val->user->pemilik->nrp) ? $val->user->pemilik->nrp.' - ' : null)
                    .(isset($val->user->pemilik->pangkat->pangkat) ? $val->user->pemilik->pangkat->pangkat.' - ' : null)
                    .$val->user->pemilik->nama;
            $t = ''; $nt= 0;
            foreach($val->tindak_lanjut as $r) { $nt++;
                $t .= $nt.'.) ';
                $t .= (isset($r->user->pemilik->nrp) ? $r->user->pemilik->nrp.' - ' : '');
                $t .= $r->user->pemilik->nama."\r";
                $t .= "     ".$r->created_at."";
                $t .= " - ".$r->status == "selesai" ? " (Selesai)" : " (Proses penanganan)";
                $t .= "\r";
                $t .= "     ".$r->keterangan." \r";
            }
            $sheet->setCellValue('A' . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $val->kejadian);
            $sheet->setCellValue('C' . $currentIndex, $val->lokasi);
            $sheet->setCellValue('D' . $currentIndex, $nama);
            $sheet->setCellValue('E' . $currentIndex, $val->w_kejadian);
            $sheet->getStyle('F' . $currentIndex)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('F' . $currentIndex, $val->keterangan);
            $sheet->getStyle('G' . $currentIndex)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('G' . $currentIndex, $t);
            $no++;
        }
        $writer = IOFactory::createWriter($excel, 'Xlsx');

        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
}
