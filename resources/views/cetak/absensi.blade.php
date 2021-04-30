<table>
	<tr>
		<th>No.</th>
		<th>NRP</th>
		<th>Nama</th>
		<th>Jabatan</th>
		<th>Kesatuan</th>
		<th>Induk</th>
		<th>Tanggal</th>
		<th>Datang</th>
		<th>Lokasi Datang</th>
		<th>Pulang</th>
		<th>Lokasi Pulang</th>
	</tr>
	@foreach($data as $row)
	<tr>
		<td>{{ $loop->iteration }}</td>
		<td>{{ $row->personil->nrp ?? '-' }}</td>
		<td>{{ $row->personil->pangkat->pangkat . ' ' .$row->personil->nama }}</td>
		<td>{{ $row->personil->jabatan->jabatan }}</td>
		<td>{{ $row->personil->kesatuan->kesatuan }}</td>
		<td>{{ $row->personil->kesatuan->induk }}</td>
		<td>{{ date('Y-m-d', strtotime($row->created_at)) ?? '-' }}</td>
		<td>{{ $row->waktu_mulai ?? '-' }}</td>
		<td><a href="https://www.google.com/maps/{{ '@'.$row->lat_datang }},{{ $row->lng_datang }},15z"></a>Lihat Lokasi</td>
		<td>{{ $row->waktu_selesai ?? '-' }}</td>
		<td><a href="https://www.google.com/maps/{{ '@'.$row->lat_pulang }},{{ $row->lng_pulang }},15z"></a>Lihat Lokasi</td>
	</tr>
	@endforeach
</table>