@php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=".$namefile.".xlsx");
    header("Cache-Control: max-age=0");
@endphp
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <style>
            .tinggi{
                height: 25px;
            }
        </style>
    </head>
    
    <body style="border: 0.5px solid #ccc"> 
        <table style="font-size : 12px;">
            <thead>
                <tr>
                    <th colspan="2" rowspan="2">KEPOLISIAN NEGARA REPUBLIK <br>INDONESIA <br> DAERAH JAWATIMUR <br> RESORT BOJONEGORO </th>
                    <th></th>
                </tr>
                <tr></tr>
                <tr>
                    <th colspan="9">LAPORAN HASIL PELAKSANAAN KEGIATAN KEPOLISIAN YANG DI TINGKATKAN (K2Y) <br> HARI {{ strtoupper($day) }} TANGGAL {{ $calendar }} </th>
                </tr>
                <tr>
                    <th colspan="9">Dasar : SURAT TELEGRAM KAPOLDA JATIM NOMOR : STR/891/VII/2017/ROOPS TANGGAL 31 JULI TENTANG PELAKSANAAN DAN LAPORAN KEGIATAN KEPOLISIAN YANG DITINGKATAKAN (K2YD).</th>
                </tr>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Waktu</th>
                    <th rowspan="2">Sasaran</th>
                    <th rowspan="2">Lokasi</th>
                    <th rowspan="2">Kuat Pers</th>
                    <th colspan="3">Hasil</th>
                    <th rowspan="2">Dokumentasi</th>
                </tr>
                <tr>
                    <th>Jml Giat</th>
                    <th>Jml Tsk</th>
                    <th>BB</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($collection as $val)
                <tr>
                    <td height="25px">{{ $loop->iteration }}</td>
                    <td height="25px">{{ $val->waktu_kegiatan->format('d-m-Y') }}</td>
                    <td height="25px">{{ $val->sasaran }}</td>
                    <td height="25px">{{ $val->lokasi }}</td>
                    <td height="25px">{{ $val->kuat_pers }}</td>
                    <td height="25px">{{ $val->jml_giat }}</td>
                    <td height="25px">{{ $val->jml_tsk }}</td>
                    <td height="25px">{{ $val->bb }}</td>
                    <td height="25px" nowrap><img width="320px" src="{{ url('api/upload/'.$val->dokumentasi) }}"/></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>