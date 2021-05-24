<?php

return [
    'accepted'             => 'Kolom :attribute harus diterima.',
    'active_url'           => 'Kolom :attribute bukan URL yang valid.',
    'after'                => 'Kolom :attribute harus tanggal setelah :date.',
    'after_or_equal'       => 'Kolom :attribute harus berupa tanggal setelah atau sama dengan tanggal :date.',
    'alpha'                => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash'           => 'Kolom :attribute hanya boleh berisi huruf, angka dan strip.',
    'alpha_num'            => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'array'                => 'Kolom :attribute harus berupa array.',
    'before'               => 'Kolom :attribute harus tanggal sebelum :date.',
    'before_or_equal'      => 'Kolom :attribute harus berupa tanggal sebelum atau sama dengan tanggal :date.',
    'between'              => [
        'numeric' => 'Kolom :attribute harus berada diantara :min dan :max.',
        'file'    => 'Kolom :attribute harus diantara :min dan :max kilobytes.',
        'string'  => 'Kolom :attribute harus diantara :min dan :max karakter.',
        'array'   => 'Kolom :attribute harus diantara :min dan :max item.',
    ],
    'boolean'              => 'Kolom :attribute harus berupa true atau false.',
    'confirmed'            => 'Konfirmasi :attribute tidak sesuai.',
    'date'                 => 'Kolom :attribute bukan tanggal yang valid.',
    'date_format'          => 'Kolom :attribute tidak sesuai denga format :format.',
    'different'            => 'Kolom :attribute dan :other harus berbeda.',
    'digits'               => 'Kolom :attribute harus berupa angka :digits.',
    'digits_between'       => 'Kolom :attribute lebih besar dar :min dan kurang dari :max.',
    'dimensions'           => 'Kolom :attribute tidak memiliki dimensi gambar yang valid.',
    'distinct'             => 'Kolom :attribute memiliki nilai duplikat.',
    'email'                => 'Kolom :attribute harus berupa alamat surel yang sesuai.',
    'exists'               => 'Kolom :attribute yang dipilih tidak sesuai.',
    'file'                 => 'Kolom :attribute harus berupa berkas.',
    'filled'               => 'Kolom :attribute harus memiliki nilai.',
    'image'                => 'Kolom :attribute harus berupa.',
    'in'                   => 'Kolom :attribute yang dipilih tidak valid.',
    'in_array'             => 'Bidang isian :attribute tidak terdapat dalam :other.',
    'integer'              => 'Isian :attribute harus merupakan bilangan bulat.',
    'ip'                   => 'Isian :attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => 'Isian :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => 'Isian :attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => 'Isian :attribute harus berupa JSON string yang valid.',
    'max'                  => [
        'numeric' => 'Kolom :attribute seharusnya tidak lebih dari :max.',
        'file'    => 'Kolom :attribute seharusnya tidak lebih dari :max kb.',
        'string'  => 'Kolom :attribute seharusnya tidak lebih dari :max karakter.',
        'array'   => 'Kolom :attribute seharusnya tidak lebih dari :max item.',
    ],
    'mimes'                => 'Isian :attribute harus dokumen berjenis: :values.',
    'mimetypes'            => 'Isian :attribute harus dokumen berjenis: :values.',
    'min'                  => [
        'numeric' => 'Kolom :attribute minimal :min.',
        'file'    => 'Kolom :attribute minimal :min kilobyte.',
        'string'  => 'Kolom :attribute minimal :min karakter.',
        'array'   => 'Kolom :attribute minimal :min item.',
    ],
    'not_in' => 'Isian :attribute yang dipilih tidak valid.',
    'numeric' => 'Isian :attribute harus berupa angka.',
    'present' => 'Bidang isian :attribute wajib ada.',
    'regex' => 'Format isian :attribute tidak valid.',
    'required' => 'Kolom :attribute wajib diisi.',
    'required_if' => 'Kolom :attribute wajib diisi bila :other adalah :value.',
    'required_unless' => 'Kolom :attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with' => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_with_all' => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_without' => 'Kolom :attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => 'Kolom :attribute wajib diisi bila tidak terdapat ada :values.',
    'same' => 'Isian :attribute dan :other harus sama.',
    'size' => [
        'numeric' => 'Isian :attribute harus berukuran :size.',
        'file' => 'Isian :attribute harus berukuran :size kilobyte.',
        'string' => 'Isian :attribute harus berukuran :size karakter.',
        'array' => 'Isian :attribute harus mengandung :size item.',
    ],
    'string' => 'Isian :attribute harus berupa string.',
    'timezone' => 'Isian :attribute harus berupa zona waktu yang valid.',
    'unique' => ':attribute sudah terdaftar.',
    'uploaded' => ':attribute gagal diunggah.',
    'url' => 'Format isian :attribute tidak valid.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
     */

    'custom' => [
        'nama' => [
            'min' => 'Nama harus diisi minimal :min karakter.'
        ],
        'old_password' => [
            'current_password' => 'Password Anda yang sekarang belum sesuai.',
        ],
        'new_password' => [
            'new_password' => 'Password baru Anda harus diisi minimal 7 karakter dan sesuai dengan konfirmasi password baru.',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
     */

    'attributes' => [
        'no_telp' => 'Nomor Handphone',
        'id_kesatuan' => 'Kesatuan',
        'id_barang' => 'Barang',
        'id_pangkat' => 'Pangkat',
        'id_jabatan' => 'Jabatan',
        'id_callsign' => 'Callsign',
        'foto' => 'Foto',
        'alamat' => 'Alamat',
        'lat' => 'Latitude',
        'lng' => 'Longitude',
        'acc' => 'Akurasi',
        'info' => 'Informasi',
        'status' => 'Status',
        'keterangan' => 'Keterangan',
        'lokasi' => 'Lokasi',
        'komentar' => 'Komentar',
        'jenis' => 'Jenis',
        'tk' => 'Tanpa Keterangan',
    ],

];
