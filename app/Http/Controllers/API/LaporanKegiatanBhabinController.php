<?php

namespace App\Http\Controllers\API;

use App\Models\BidangKegiatanBhabin;
use App\Http\Controllers\Controller;
use App\Models\IndikatorKegiatanBhabin;
use App\Models\JenisKegiatanBhabin;
use App\Models\KategoriKegiatanBhabin;
use App\Models\KegiatanBhabin;
use App\Models\KegiatanMasyarakat;
use App\Models\MasyarakatKegiatanBhabin;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\KegiatanBhabinTransformer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanKegiatanBhabinController extends Controller
{
    public function laporan(Request $request)
    {
        list($orderBy, $direction) = explode(':', $request->sort);
    	
    	if (!in_array($request->user()->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $kegiatan = $request->filter == '' ?
            KegiatanBhabin::sorting($request)
                            ->where('id_indikator', $request->indikator)
            				->orderBy($orderBy, $direction) :
            KegiatanBhabin::filtered($request)->orderBy($orderBy, $direction);

        if (count($kegiatan->get()) === 0)
            return response()->json(['message' => 'Tidak ada content.'], 204);

        $limit = $request->limit != '' ? $request->limit : 10;
        if($limit == 0)
            return null;
        $paginator = $kegiatan->paginate($limit);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->parseIncludes(['tipe', 'jenis', 'kategori', 'bidang', 'masyarakat', 'kecamatan'])
            ->transformWith(new KegiatanBhabinTransformer(true))
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function export($id, Request $request)
    {
        $data = KegiatanBhabin::sorting($request)
                            ->where('id_indikator', $id)
                            ->get();
        switch ($id) {
            case '1':
                return $this->sambang($data);
                break;
            case '2':
                return $this->mediasi($data);
                break;
            case '3':
                return $this->pelayanan($data);
                break;
            case '4':
                return $this->kegiatanDesa($data);
                break;
            case '5':
                return $this->pembinaan($data);
                break;
            case '6':
                return $this->deteksi($data);
                break;
            
            default:
                return $this->siskamling($data);
                break;
        }
    }

    public function sambang($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/bhabin-sambang.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 2;
        $startColIndex = "A";
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
            $sheet->setCellValue('E' . $currentIndex, $val->jenis->jenis);
            $sheet->setCellValue('F' . $currentIndex, $val->lokasi);
            $sheet->setCellValue('G' . $currentIndex, $val->uraian);
            $sheet->setCellValue('H' . $currentIndex, $val->waktu_kegiatan);
            
            if ($val->dokumentasi && Storage::exists($val->dokumentasi)) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val->id);
                $imageDraw->setCoordinates('I' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
            }
            $no++;
        }

        $sheet->getColumnDimension('I')->setWidth(30);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function mediasi($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/bhabin-mediasi.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 2;
        $startColIndex = "A";
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
            $sheet->setCellValue('E' . $currentIndex, $val->kategori->kategori);
            $sheet->setCellValue('F' . $currentIndex, $val->ringkasan);
            $sheet->setCellValue('G' . $currentIndex, $val->para_pihak);
            $sheet->setCellValue('H' . $currentIndex, $val->kronologi);
            $sheet->setCellValue('I' . $currentIndex, $val->solusi);
            $sheet->setCellValue('J' . $currentIndex, $val->kecamatan->nama);
            $sheet->setCellValue('K' . $currentIndex, $val->waktu_kegiatan);
            
            if ($val->dokumentasi && Storage::exists($val->dokumentasi)) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val->id);
                $imageDraw->setCoordinates('L' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
            }
            $no++;
        }

        $sheet->getColumnDimension('L')->setWidth(30);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function pelayanan($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/bhabin-pertolongan.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 2;
        $startColIndex = "A";
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
            $sheet->setCellValue('E' . $currentIndex, $val->lokasi);
            $sheet->setCellValue('F' . $currentIndex, $val->uraian);
            $sheet->setCellValue('G' . $currentIndex, $val->waktu_kegiatan);
            
            if ($val->dokumentasi && Storage::exists($val->dokumentasi)) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val->id);
                $imageDraw->setCoordinates('H' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
            }
            $no++;
        }

        $sheet->getColumnDimension('H')->setWidth(30);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function kegiatanDesa($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/bhabin-kegiatan-desa.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 2;
        $startColIndex = "A";
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
            $sheet->setCellValue('E' . $currentIndex, $val->lokasi);
            $sheet->setCellValue('F' . $currentIndex, $val->kegiatan);
            $sheet->setCellValue('G' . $currentIndex, $val->pelaksanaan);
            $sheet->setCellValue('H' . $currentIndex, $val->uraian);
            $sheet->setCellValue('I' . $currentIndex, $val->waktu_kegiatan);
            
            if ($val->dokumentasi && Storage::exists($val->dokumentasi)) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val->id);
                $imageDraw->setCoordinates('J' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
            }
            $no++;
        }

        $sheet->getColumnDimension('J')->setWidth(30);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function pembinaan($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/bhabin-pembinaan.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 2;
        $startColIndex = "A";
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
            $sheet->setCellValue('E' . $currentIndex, $val->lokasi);
            $sheet->setCellValue('F' . $currentIndex, $val->kegiatan);
            $sheet->setCellValue('G' . $currentIndex, $val->pelaksanaan);
            $sheet->setCellValue('H' . $currentIndex, $val->keterangan);
            $sheet->setCellValue('I' . $currentIndex, $val->waktu_kegiatan);
            
            if ($val->dokumentasi && Storage::exists($val->dokumentasi)) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val->id);
                $imageDraw->setCoordinates('J' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
            }
            $no++;
        }

        $sheet->getColumnDimension('J')->setWidth(30);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function deteksi($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/bhabin-deteksi.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 2;
        $startColIndex = "A";
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
            $sheet->setCellValue('E' . $currentIndex, $val->bidang->bidang ?? null);
            $sheet->setCellValue('F' . $currentIndex, $val->lokasi);
            $sheet->setCellValue('G' . $currentIndex, $val->uraian);
            $sheet->setCellValue('H' . $currentIndex, $val->nilai_informasi);
            $sheet->setCellValue('I' . $currentIndex, $val->waktu_kegiatan);
            
            if ($val->dokumentasi && Storage::exists($val->dokumentasi)) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val->id);
                $imageDraw->setCoordinates('J' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
            }
            $no++;
        }

        $sheet->getColumnDimension('J')->setWidth(30);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function siskamling($data)
    {
        $reader = IOFactory::createReader('Xlsx');
        $excel = $reader->load(storage_path("app/excel/bhabin-siskamling.xlsx"));
        $sheet = $excel->getActiveSheet();

        $no = 1;
        $startRowIndex = 2;
        $startColIndex = "A";
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
            $sheet->setCellValue('E' . $currentIndex, $val->lokasi);
            $sheet->setCellValue('F' . $currentIndex, $val->uraian);
            $sheet->setCellValue('G' . $currentIndex, $val->waktu_kegiatan);
            
            if ($val->dokumentasi && Storage::exists($val->dokumentasi)) {
                $imageDraw = new Drawing();
                $imageDraw->setName($val->id);
                $imageDraw->setCoordinates('H' . $currentIndex);
                $imageDraw->setPath(storage_path('app/' . $val['dokumentasi']));
                $imageDraw->setWidthAndHeight(250, 250);
                $imageDraw->setOffsetY(10);
                $imageDraw->setOffsetX(10);
                $imageDraw->setWorksheet($sheet);
                $sheet->getRowDimension($currentIndex)->setRowHeight($imageDraw->getHeight());
            }
            $no++;
        }

        $sheet->getColumnDimension('H')->setWidth(30);
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        
        \header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        \header('Content-Disposition: attachment;filename="laporan.xlsx"');
        \header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
}
