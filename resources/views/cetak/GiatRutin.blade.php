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
                    <th>No</th>
                    <th>Jenis Giat</th>
                    <th>Judul</th>
                    <th>Hasil yang didapat</th>  
                    <th>Lokasi</th>
                    <th>Dokumentasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($collection as $val)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $val->waktu_kegiatan->format('d-m-Y') }}</td>
                    <td>{{ $val->jenis->jenis }}</td>
                    <td>{{ $val->judul }}</td>
                    <td>{{ $val->hasil }}</td>
                    <td nowrap><img style="width : 100px; height: 100px;" src="{{ url('api/upload/'.$val->dokumentasi) }}"/></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>