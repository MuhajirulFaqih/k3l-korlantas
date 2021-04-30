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
                    <th colspan="6"><b>DATA PENGHAPUSAN MERKURI (SATGAS PENGHAPUSAN MERKURI)</b> <br> <b>POLRES BOJONEGORO</b> </th>
                </tr>
                <tr>
                    <th colspan="6">HARI : {{ $day }} {{ $calendar }}</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>DASAR LP/LI</th>
                    <th>Modus Operasi/Pasal</th>
                    <th>Tersangka dan Barang Bukti</th>  
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($collection as $val)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $val->dasar  }}</td>
                    <td>{{ $val->modus }}</td>
                    <td>Tersangka : {{ $val->jml_tsk }} <br> Barang Bukti : {{ $val->bb }} </td>
                    <td>{{ $val->keterangan }}</td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>