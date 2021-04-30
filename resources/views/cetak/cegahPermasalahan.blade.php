@php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=".$namefile.".xlsx");
    header("Cache-Control: max-age=0");
@endphp
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body style="border: 0.5px solid #ccc">
        <table style="font-size : 12px;">
            <thead>
                <tr>
                    <th colspan="2" rowspan="2">KEPOLISIAN NEGARA REPUBLIK INDONESIA <br> DAERAH JAWATIMUR <br> RESORT BOJONEGORO </th>
                </tr>
                <tr></tr>
                <tr>
                    <th colspan="6">
                        HASIL GIAT SOSIALISASI TINDAK LANJUT MoU ANTARA KEMENDEKES PDTT, KEMENDAGRI <br>
                        DAN KAPOLRI TERKAIT PENCEGAHAN, PENGAWASAN, DAN PERMASALAHAN <br>
                        DANA DESA
                    </th>
                </tr>
                <tr>
                    <th colspan="6">HARI : {{ $day }} <br> TANGGAL : {{ $calendar }} <br> WAKTU : {{ $time }}</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>WAKTU KEGIATAN</th>
                    <th>BENTUK KEGIATAN</th>
                    <th>KUAT PERSONIL</th>  
                    <th>DOKUMENTASI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($collection as $val)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $val->waktu_kegiatan->format('d-m-Y') }}</td>
                    <td>{{ $val->judul }}</td>
                    <td>{{ $val->kuat_pers }}</td>
                    <td nowrap><img style="width : 100px; height: 100px;" src="{{ url('api/upload/'.$val->dokumentasi) }}"/></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>