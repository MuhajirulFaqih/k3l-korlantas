<?php

namespace App\Http\Controllers\API;

use App\Exports\LaporanExcel;
use App\Http\Controllers\Controller;
use App\Models\JenisKegiatan;
use App\Models\Kegiatan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Models\TipeLaporan;
use App\Transformers\ExportLaporanTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Spatie\Fractalistic\ArraySerializer;
use function Symfony\Component\Debug\header;

class ExportLaporan extends Controller
{
    public function selectTipe(Request $request)
    {
        $tipeLaporan = TipeLaporan::all();
        return response()->json($tipeLaporan);
    }

    public function jenisGiat()
    {
        $jenisGiat = JenisKegiatan::all();
        return response()->json($jenisGiat);
    }

    public function index(Request $request, $id)
    {
        $users = $request->user();
        if ($users->jenis_pemilik !== "admin")
            return response()->json(['error' => 'Terlarang'], 403);

        if($id == '1')
            return $this->dataGiatRutin($request);

        list($orderBy, $direction) = explode(':', $request->sort);

        if ($request->type == '' || ($request->type == '2' && $request->rentang == '')) {
            $data = Kegiatan::whereNull('id');
        } else {
            if ($request->type == '1') {
                $data = Kegiatan::where('tipe_laporan', $id)
                    ->orderBy($orderBy, $direction);
            } else {
                list($mulai, $selesai) = $request->rentang;
                $data = Kegiatan::whereBetween('waktu_kegiatan', [substr($mulai, 0, 10), substr($selesai, 0, 10)])
                    ->where("tipe_laporan", $id)
                    ->orderBy($orderBy, $direction);;
            }
        }

        $paginator = $data->paginate(10);
        $collection = $paginator->getCollection();
        return fractal()
            ->collection($collection)
            ->transformWith(new ExportLaporanTransformer)
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();
    }

    public function dataGiatRutin($request)
    {
        //Cek jika type null
        if($request->type == '') {
            $data = Kegiatan::whereNull('id');
        }
        else {
            //Cek waktu cetak
            $data = $request->option == '1' ? 
                    $this->giatRutinSemua($request) : 
                    $this-> giatRutinRange($request);
        }

        $paginator = $data->paginate(10);
        $collection = $paginator->getCollection();
        return fractal()
            ->collection($collection)
            ->transformWith(new ExportLaporanTransformer)
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();
    }

    public function giatRutinSemua($request)
    {
        list($orderBy, $direction) = explode(':', $request->sort);

        if($request->type == 1) {
            return Kegiatan::where('tipe_laporan', '1')
                    ->orderBy($orderBy, $direction);
        }
        else {
            if(count($request->jenis) == 0) {
                return Kegiatan::whereNull('id');
            } 
            else {
                return Kegiatan::where('tipe_laporan', '1')
                                ->whereIn('jenis_kegiatan', $request->jenis ?? [])
                                ->orderBy($orderBy, $direction);
            }
        }
    }

    public function giatRutinRange($request)
    {
        list($orderBy, $direction) = explode(':', $request->sort);
        list($mulai, $selesai) = $request->rentang;

        if($request->type == 1) {
            return Kegiatan::where('tipe_laporan', '1')
                    ->whereBetween('waktu_kegiatan', [substr($mulai, 0, 10), substr($selesai, 0, 10)])
                    ->orderBy($orderBy, $direction);
        }
        else {
            return Kegiatan::where('tipe_laporan', '1')
                            ->whereIn('jenis_kegiatan', $request->jenis ?? [])
                            ->whereBetween('waktu_kegiatan', [substr($mulai, 0, 10), substr($selesai, 0, 10)])
                            ->orderBy($orderBy, $direction);
        }
    }

    public function exportExcelbyChecklist(Request $request, $id_tipe)
    {
        if(is_null($request->range)) {
            if(count(($request->jenis == '' ? [] :  $request->jenis)) == 0) {
                $data = Kegiatan::where('tipe_laporan', $id_tipe)
                ->whereNotIn('id', $request->id ?? [])
                ->orderBy('created_at', 'asc')
                ->get();
            }
            else {
                $data = Kegiatan::where('tipe_laporan', $id_tipe)
                ->whereIn('jenis_kegiatan', $request->jenis == '' ? null : $request->jenis)   
                ->whereNotIn('id', $request->id ?? [])
                ->orderBy('created_at', 'asc')
                ->get();
            }
        }
        else {
            if(count(($request->jenis == '' ? [] : $request->jenis)) == 0) {
                list($mulai, $selesai) = $request->range;
                $data = Kegiatan::where('tipe_laporan', $id_tipe)
                    ->whereNotIn('id', $request->id ?? [])
                    ->whereBetween('waktu_kegiatan', [substr($mulai, 0, 10), substr($selesai, 0, 10)])
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
            else {
                list($mulai, $selesai) = $request->range;
                $data = Kegiatan::where('tipe_laporan', $id_tipe)
                ->whereNotIn('id', $request->id ?? [])
                ->whereIn('jenis_kegiatan', $request->jenis == '' ? null : $request->jenis)
                ->whereBetween('waktu_kegiatan', [substr($mulai, 0, 10), substr($selesai, 0, 10)])
                ->orderBy('created_at', 'asc')
                ->get();
            }
        }

        if ($id_tipe == 1) {
            $this->giatRutin($data);
        } elseif ($id_tipe == 2) {
            $this->k2yd($data);
        } elseif ($id_tipe == 3) {
            $this->kunjunganT3($data);
        } elseif ($id_tipe == 4) {
            $this->cegahPermasalahan($data);
        } elseif ($id_tipe == 5) {
            $this->giatLangkah($data);
        } elseif ($id_tipe == 6) {
            $this->satgasMerkuri($data);
        }

        dump(['request' => $request, 'data' => $data]);

    }

    public function giatRutin($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/giat-rutin.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 2;
        $startColIndex = "A";
        $colWaktu = "B";
        $colKegiatan = "C";
        $colLokasi = "D";
        $colKuatPers = "E";
        $colHasil = "F";
        $colDokumentasi = "G";
        $colFoto = $colDokumentasi;
        $widhtColumns = 0;

        foreach ($data as $val) {
            $currentIndex = $startRowIndex + ($no - 1);
            

            $nama = (isset($val->user->pemilik->pangkat->pangkat) ? 
                    $val->user->pemilik->pangkat->pangkat : 
                    $val->user->pemilik->personil->pangkat->pangkat) .' '.($val->user->pemilik->nama == '' ? $val->user->pemilik->personil->nama : $val->user->pemilik->nama);
            $jabatan = (isset($val->user->pemilik->jabatan->jabatan) ? 
                    $val->user->pemilik->jabatan->jabatan : 
                    $val->user->pemilik->personil->jabatan->jabatan);

            $sheet->setCellValue($startColIndex . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $val->user->pemilik->nrp);
            $sheet->setCellValue('C' . $currentIndex, $nama);
            $sheet->setCellValue('D' . $currentIndex, $jabatan);
            $sheet->setCellValue('E' . $currentIndex, $val->waktu_kegiatan);
            $sheet->setCellValue('F' . $currentIndex, $val->judul);
            $sheet->setCellValue('G' . $currentIndex, $val->lokasi);
            $sheet->setCellValue('H' . $currentIndex, $val->kuat_pers);
            $sheet->setCellValue('I' . $currentIndex, $val->hasil);

            Log::info('mbuh', ['y' => $val['dokumentasi'] && Storage::exists($val['dokumentasi']), 'x' => $val['dokumentasi']]);
            if ($val['dokumentasi'] && Storage::exists($val['dokumentasi'])) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val['id']);
                $imageDraw->setCoordinates('J' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
                // if ($widhtColumns < $imageDraw->getWidth())
                //     $widhtColumns = $imageDraw->getWidth();
            }
            $no++;
        }

        $sheet->getColumnDimension($colFoto)->setWidth(30);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function k2yd($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/k2yd.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 15;
        $startColIndex = "A";
        $colWaktu = "B";
        $colSasaran = "C";
        $colKegiatan = "D";
        $colLokasi = "E";
        $colKuatPers = "F";
        $colJmlGiat = "G";
        $colJmlTSK = "H";
        $colJmlBB = "I";
        $colDokumentasi = "J";
        $colFoto = $colDokumentasi;
        $widhtColumns = 0;

        foreach ($data as $val) {
            $currentIndex = $startRowIndex + ($no - 1);

            $nama = (isset($val->user->pemilik->pangkat->pangkat) ? 
                    $val->user->pemilik->pangkat->pangkat : 
                    $val->user->pemilik->personil->pangkat->pangkat) .' '.($val->user->pemilik->nama == '' ? $val->user->pemilik->personil->nama : $val->user->pemilik->nama);
            $jabatan = (isset($val->user->pemilik->jabatan->jabatan) ? 
                    $val->user->pemilik->jabatan->jabatan : 
                    $val->user->pemilik->personil->jabatan->jabatan);

            $sheet->setCellValue($startColIndex . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $val->user->pemilik->nrp);
            $sheet->setCellValue('C' . $currentIndex, $nama);
            $sheet->setCellValue('D' . $currentIndex, $jabatan);
            $sheet->setCellValue('E' . $currentIndex, $val['waktu_kegiatan']);
            $sheet->setCellValue('F' . $currentIndex, $val['sasaran']);
            $sheet->setCellValue('G' . $currentIndex, $val['judul']);
            $sheet->setCellValue('H' . $currentIndex, $val['lokasi']);
            $sheet->setCellValue('I' . $currentIndex, $val['kuat_pers']);
            $sheet->setCellValue('J' . $currentIndex, $val['jml_giat']);
            $sheet->setCellValue('K' . $currentIndex, $val['jml_tsk']);
            $sheet->setCellValue('L' . $currentIndex, $val['bb']);

            //Log::info('mbuh', ['y' => $val['dokumentasi'] && Storage::exists($val['dokumentasi']), 'x' => $val['dokumentasi']]);
            if ($val['dokumentasi'] && Storage::exists($val['dokumentasi'])) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val['id']);
                $imageDraw->setCoordinates('M' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
                if ($widhtColumns < $imageDraw->getWidth())
                    $widhtColumns = $imageDraw->getWidth();
            }
            $no++;
        }

        $sheet->getColumnDimension($colFoto)->setWidth($widhtColumns);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        // ob_start();

        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function kunjunganT3($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/kunjunganT3.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 14;
        $startColIndex = "A";
        $colWaktu = "B";
        $colKegiatan = "C";
        $colKuatPers = "D";
        $colHasil = "E";
        $colDokumentasi = "F";
        $colFoto = $colDokumentasi;
        $widhtColumns = 0;

        foreach ($data as $val) {
            $currentIndex = $startRowIndex + ($no - 1);

            $nama = (isset($val->user->pemilik->pangkat->pangkat) ? 
                    $val->user->pemilik->pangkat->pangkat : 
                    $val->user->pemilik->personil->pangkat->pangkat) .' '.($val->user->pemilik->nama == '' ? $val->user->pemilik->personil->nama : $val->user->pemilik->nama);
            $jabatan = (isset($val->user->pemilik->jabatan->jabatan) ? 
                    $val->user->pemilik->jabatan->jabatan : 
                    $val->user->pemilik->personil->jabatan->jabatan);

            $sheet->setCellValue($startColIndex . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $val->user->pemilik->nrp);
            $sheet->setCellValue('C' . $currentIndex, $nama);
            $sheet->setCellValue('D' . $currentIndex, $jabatan);
            $sheet->setCellValue('E' . $currentIndex, $val['waktu_kegiatan']);
            $sheet->setCellValue('F' . $currentIndex, $val['judul']);
            $sheet->setCellValue('G' . $currentIndex, $val['kuat_pers']);
            $sheet->setCellValue('H' . $currentIndex, $val['hasil']);

            if ($val['dokumentasi'] && Storage::exists($val['dokumentasi'])) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val['id']);
                $imageDraw->setCoordinates('I' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
                if ($widhtColumns < $imageDraw->getWidth())
                    $widhtColumns = $imageDraw->getWidth();
            }
            $no++;
        }

        $sheet->getColumnDimension($colFoto)->setWidth($widhtColumns);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        // ob_start();

        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function cegahPermasalahan($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/cegah-permasalahan.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 15;
        $startColIndex = "A";
        $colWaktu = "B";
        $colKegiatan = "C";
        $colKuatPers = "D";
        $colDokumentasi = "E";
        $colFoto = $colDokumentasi;
        $widhtColumns = 0;

        foreach ($data as $val) {
            $currentIndex = $startRowIndex + ($no - 1);

            $nama = (isset($val->user->pemilik->pangkat->pangkat) ? 
                    $val->user->pemilik->pangkat->pangkat : 
                    $val->user->pemilik->personil->pangkat->pangkat) .' '.($val->user->pemilik->nama == '' ? $val->user->pemilik->personil->nama : $val->user->pemilik->nama);
            $jabatan = (isset($val->user->pemilik->jabatan->jabatan) ? 
                    $val->user->pemilik->jabatan->jabatan : 
                    $val->user->pemilik->personil->jabatan->jabatan);

            $sheet->setCellValue($startColIndex . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $val->user->pemilik->nrp);
            $sheet->setCellValue('C' . $currentIndex, $nama);
            $sheet->setCellValue('D' . $currentIndex, $jabatan);
            $sheet->setCellValue('E' . $currentIndex, $val['waktu_kegiatan']);
            $sheet->setCellValue('F' . $currentIndex, $val['judul']);
            $sheet->setCellValue('G' . $currentIndex, $val['kuat_pers']);

            if ($val['dokumentasi'] && Storage::exists($val['dokumentasi'])) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val['id']);
                $imageDraw->setCoordinates('H' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
                if ($widhtColumns < $imageDraw->getWidth())
                    $widhtColumns = $imageDraw->getWidth();
            }
            $no++;
        }

        $sheet->getColumnDimension($colFoto)->setWidth($widhtColumns);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        // ob_start();

        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function giatLangkah($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/giat-langkah.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 12;
        $startColIndex = "A";
        $colWaktu = "B";
        $colKegiatan = "C";
        $colKuatPers = "D";
        $colHasil = "E";
        $colDokumentasi = "F";
        $colFoto = $colDokumentasi;
        $widhtColumns = 0;

        foreach ($data as $val) {
            $currentIndex = $startRowIndex + ($no - 1);

            $nama = (isset($val->user->pemilik->pangkat->pangkat) ? 
                    $val->user->pemilik->pangkat->pangkat : 
                    $val->user->pemilik->personil->pangkat->pangkat) .' '.($val->user->pemilik->nama == '' ? $val->user->pemilik->personil->nama : $val->user->pemilik->nama);
            $jabatan = (isset($val->user->pemilik->jabatan->jabatan) ? 
                    $val->user->pemilik->jabatan->jabatan : 
                    $val->user->pemilik->personil->jabatan->jabatan);

            $sheet->setCellValue($startColIndex . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $val->user->pemilik->nrp);
            $sheet->setCellValue('C' . $currentIndex, $nama);
            $sheet->setCellValue('D' . $currentIndex, $jabatan);
            $sheet->setCellValue('E' . $currentIndex, $val['waktu_kegiatan']);
            $sheet->setCellValue('F' . $currentIndex, $val['judul']);
            $sheet->setCellValue('G' . $currentIndex, $val['kuat_pers']);
            $sheet->setCellValue('H' . $currentIndex, $val['hasil']);

            if ($val['dokumentasi'] && Storage::exists($val['dokumentasi'])) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val['id']);
                $imageDraw->setCoordinates('I' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
                if ($widhtColumns < $imageDraw->getWidth())
                    $widhtColumns = $imageDraw->getWidth();
            }
            $no++;
        }

        $sheet->getColumnDimension($colFoto)->setWidth($widhtColumns);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        // ob_start();

        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function satgasMerkuri($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/satgas-merkuri.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 10;
        $startColIndex = "A";
        $colModus = "B";
        $colTersangka = "C";
        $colBb = "D";
        $colPerkembangan = "E";
        $colKeterangan = "F";
        $widhtColumns = 0;

        foreach ($data as $val) {
            $currentIndex = $startRowIndex + ($no - 1);

            $nama = (isset($val->user->pemilik->pangkat->pangkat) ? 
                    $val->user->pemilik->pangkat->pangkat : 
                    $val->user->pemilik->personil->pangkat->pangkat) .' '.($val->user->pemilik->nama == '' ? $val->user->pemilik->personil->nama : $val->user->pemilik->nama);
            $jabatan = (isset($val->user->pemilik->jabatan->jabatan) ? 
                    $val->user->pemilik->jabatan->jabatan : 
                    $val->user->pemilik->personil->jabatan->jabatan);

            $sheet->setCellValue($startColIndex . $currentIndex, $no);
            $sheet->setCellValue('B' . $currentIndex, $val->user->pemilik->nrp);
            $sheet->setCellValue('C' . $currentIndex, $nama);
            $sheet->setCellValue('D' . $currentIndex, $jabatan);
            $sheet->setCellValue('E' . $currentIndex, $val['modus']);
            $sheet->setCellValue('F' . $currentIndex, $val['jml_tsk']);
            $sheet->setCellValue('G' . $currentIndex, $val['bb']);
            $sheet->setCellValue('H' . $currentIndex, $val['perkembangan']);
            $sheet->setCellValue('I' . $currentIndex, $val['keterangan']);
            $no++;
        }

        $writer = IOFactory::createWriter($excel, 'Xlsx');
        // ob_start();

        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
}
