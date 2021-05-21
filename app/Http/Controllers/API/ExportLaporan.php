<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JenisKegiatan;
use App\Models\Kegiatan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\ExportLaporanTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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

    public function index(Request $request)
    {
        $users = $request->user();
        if ($users->jenis_pemilik !== "admin")
            return response()->json(['error' => 'Terlarang'], 403);



        list($orderBy, $direction) = explode(':', $request->sort);

        $data = Kegiatan::with('user', 'jenis', 'user.pemilik')->laporan($request->filter, $request->rentang, $request->id_jenis)->orderBy($orderBy, $direction);

        $paginator = $data->paginate(10);
        $collection = $paginator->getCollection();
        return fractal()
            ->collection($collection)
            ->transformWith(new ExportLaporanTransformer)
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();
    }

    public function exportExcelbyChecklist(Request $request, $id_tipe)
    {
        $data = Kegiatan::with('user', 'jenis', 'user.pemilik')->laporan($request->filter, $request->range, $request->id_jenis)->get();

        $this->giatRutin($data);

        dump(['request' => $request, 'data' => $data]);
    }

    public function giatRutin($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/kegiatan.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 2;
        $startColIndex = "A";
        $colNrp = "B";
        $colNama = "C";
        $colJabatan = "D";
        $colWaktu = "E";
        $colKegiatan = "F";
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
            $sheet->setCellValue($colNrp . $currentIndex, $val->user->pemilik->nrp);
            $sheet->setCellValue($colNama . $currentIndex, $nama);
            $sheet->setCellValue($colJabatan . $currentIndex, $jabatan);
            $sheet->setCellValue($colWaktu . $currentIndex, $val->waktu_kegiatan);
            $sheet->setCellValue($colKegiatan . $currentIndex, $val->judul);

            Log::info('mbuh', ['y' => $val['dokumentasi'] && Storage::exists($val['dokumentasi']), 'x' => $val['dokumentasi']]);
            if ($val['dokumentasi'] && Storage::exists($val['dokumentasi'])) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val['id']);
                $imageDraw->setCoordinates($colDokumentasi . $currentIndex);
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

        $sheet->getColumnDimension($colFoto)->setWidth(40);
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
