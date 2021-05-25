<?php

namespace Database\Seeders;

use App\Models\Kesatuan;
use App\Models\JenisKegiatan;
use App\Models\JenisKegiatanKesatuan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisKegiatanKesatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('jenis_kegiatan')->truncate();
        DB::table('jenis_kegiatan_kesatuan')->truncate();

        // Isi kode_satuan dengan kode induk dari kesatuan terkait

        $kegiatan = [
            //Direktorat lalu lintas
            [
                'kode_satuan' => ['22009'],
                'data' => [
                    [
                        'jenis' => 'Laporan Pengaturan',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => true,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Lokasi Pengaturan', 
                                'subjenis' => [
                                    ['jenis' => 'Persimpangan'], 
                                    ['jenis' => 'Putaran/Belok Arah'], 
                                    ['jenis' => 'Penyebrangan'], 
                                    ['jenis' => 'Sekolah'], 
                                    ['jenis' => 'Pasar Keramaian'], 
                                    ['jenis' => 'Jalan Tol'], 
                                    ['jenis' => 'Keadaan Khusus'], 
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                            [
                                'jenis' => 'Jenis Pengaturan', 
                                'subjenis' => [
                                    ['jenis' => 'Percepatan Arus Lalu Lintas'], 
                                    ['jenis' => 'Alih Arus Lalu Lintas'], 
                                    ['jenis' => 'Tutup Arus Lalu Lintas'], 
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Laporan Penjagaan',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => true,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Lokasi Penjagaan', 
                                'subjenis' => [
                                    ['jenis' => 'Mako/Kantor'], 
                                    ['jenis' => 'Pos Tetap'], 
                                    ['jenis' => 'Pos Sementara'], 
                                    ['jenis' => 'Induk PJR/Pos Diperkuat'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Giat Pengawalan',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => true,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Giat Pengawalan', 
                                'subjenis' => [
                                    ['jenis' => 'Pimpinan Lembaga RI'], 
                                    ['jenis' => 'Pimpinan & Pejabat Negara Asing'], 
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Laporan Patroli',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => true,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Patroli', 
                                'subjenis' => [
                                    ['jenis' => 'Roda Dua'], 
                                    ['jenis' => 'Roda Empat'],
                                ]
                            ],
                            [
                                'jenis' => 'Lokasi Patroli', 
                                'subjenis' => [
                                    ['jenis' => 'Luar Kota'], 
                                    ['jenis' => 'Jalan Tol'],
                                ]
                            ],
                            [
                                'jenis' => 'Sasaran Patroli', 
                                'subjenis' => [
                                    ['jenis' => 'Datangi/Tangani Lokasi Rawan Macet'], 
                                    ['jenis' => 'Datangi/Tangani Lokasi Rawan Laka'],
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Kegiatan Pelayanan Masyarakat',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Pelayanan Masyarakat', 
                                'subjenis' => [
                                    ['jenis' => 'Satpas Keliling'], 
                                    ['jenis' => 'Samsat Keliling'],
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Giat SIM',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Pembuatan SIM Baru', 
                                'subjenis' => [
                                    ['jenis' => 'A'], 
                                    ['jenis' => 'A Umum'],
                                    ['jenis' => 'B1'],
                                    ['jenis' => 'B1 Umum'],
                                    ['jenis' => 'B2'],
                                    ['jenis' => 'B2 Umum'],
                                    ['jenis' => 'C'],
                                    ['jenis' => 'D'],
                                ]
                            ],
                            [
                                'jenis' => 'Perpanjangan SIM', 
                                'subjenis' => [
                                    ['jenis' => 'A'], 
                                    ['jenis' => 'A Umum'],
                                    ['jenis' => 'B1'],
                                    ['jenis' => 'B1 Umum'],
                                    ['jenis' => 'B2'],
                                    ['jenis' => 'B2 Umum'],
                                    ['jenis' => 'C'],
                                    ['jenis' => 'D'],
                                ]
                            ],
                            [
                                'jenis' => 'SATPAS KELILING', 
                                'subjenis' => [
                                    ['jenis' => 'A'], 
                                    ['jenis' => 'C'],
                                ]
                            ],
                        ]
                    ],
                ]
            ],
            //Direktorat samapta
            [
                'kode_satuan' => ['22038'],
                'data' => [
                    [
                        'jenis' => 'Pengaturan',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Cara Bertindak', 
                                'subjenis' => [
                                    ['jenis' => 'Pengaturan Giat Masyarakat'], 
                                    ['jenis' => 'Pengaturan Lalu Lintas'],
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Penjagaan',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Cara Bertindak', 
                                'subjenis' => [
                                    ['jenis' => 'Penjagaan Mako'], 
                                    ['jenis' => 'Penjagaan Tahanan'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Pengawalan',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Cara Bertindak', 
                                'subjenis' => [
                                    ['jenis' => 'Pengawalan Uang/Barang'], 
                                    ['jenis' => 'Pengawalan Tahanan'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Patroli',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Cara Bertindak', 
                                'subjenis' => [
                                    ['jenis' => 'Patroli Jalan Kaki'], 
                                    ['jenis' => 'Patroli Roda 2'],
                                    ['jenis' => 'Patroli Roda 4'],
                                    ['jenis' => 'Patroli Roda 6'],
                                    ['jenis' => 'Patroli OPS Kepolisian'],
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Dalmas',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Cara Bertindak', 
                                'subjenis' => [
                                    ['jenis' => 'Pengamanan Unjuk Rasa'], 
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Negosiator',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Cara Bertindak', 
                                'subjenis' => [
                                    ['jenis' => 'Negosiator UNRAS'], 
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'TPTKP',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Cara Bertindak', 
                                'subjenis' => [
                                    ['jenis' => 'TPTKP Laka Lantas'], 
                                    ['jenis' => 'TPTKP Kebakaran'], 
                                    ['jenis' => 'TPTKP Pencurian'], 
                                    ['jenis' => 'Lain-lain'], 
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'PAMMAT/SAR Terbatas',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Cara Bertindak', 
                                'subjenis' => [
                                    ['jenis' => 'Banjir'], 
                                    ['jenis' => 'Tanah Longsor'], 
                                    ['jenis' => 'Kebakaran'], 
                                    ['jenis' => 'Lain-lain'], 
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'BANTIS Satwa',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Cara Bertindak', 
                                'subjenis' => [
                                    ['jenis' => 'Sterilisasi'], 
                                    ['jenis' => 'Pengiriman UNRAS'], 
                                    ['jenis' => 'Patroli Satwa'], 
                                    ['jenis' => 'Pelacakan Handak'], 
                                    ['jenis' => 'Pelacakan Umum'], 
                                    ['jenis' => 'Pelacakan Narkoba'], 
                                    ['jenis' => 'Pelacakan Mayat'], 
                                    ['jenis' => 'Lain-lain'], 
                                ]
                            ],
                        ]
                    ],
                ]
            ],
            //Direktorat pembinaan ketertiban masyarakat
            [
                'kode_satuan' => ['22007'],
                'data' => [
                    [
                        'jenis' => 'Pembinaan Ketertiban Sosial',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Pembinaan', 
                                'subjenis' => [
                                    ['jenis' => 'Penyuluhan Kepada Pemuda dan Wanita'], 
                                    ['jenis' => 'Pencegahan, Penanggulangan Faham Radikal & Anti Pancasila (FGD, Seminar, Sambang)'], 
                                    ['jenis' => 'Pembinaan Karakter (Saka Bhayangkara, Komunitas Pemuda Lainnya)'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Pembinaan Satpam/Polsus',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Pelatihan', 
                                'subjenis' => [
                                    ['jenis' => 'Pelatihan Satpan'], 
                                    ['jenis' => 'Pelatihan Polsus'], 
                                    ['jenis' => 'Pengawasan BUJP'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Pembinaan POLMAS',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Pembinaan', 
                                'subjenis' => [
                                    ['jenis' => 'Pemberdayaan POTMAS & Membangun Kemitraan Melalui FKPM'], 
                                    ['jenis' => 'Pembinaan Siskamling'], 
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Pembinaan Bhabinkamtibmas',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'ANEV kegiatan Bhabinkamtibmas', 
                                'subjenis' => [
                                    ['jenis' => 'Deteksi Dini'], 
                                    ['jenis' => 'Sambang'], 
                                    ['jenis' => 'Pembinaan Polmas'], 
                                    ['jenis' => 'Terobosan Kreatif'],
                                ]
                            ],
                        ]
                    ],
                ]
            ],
        ];

        $quickresponse = [
            [
                'kode_satuan' => [],
                'data' => [
                    [
                        'jenis' => 'PATROLI BEAT',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => true,
                        'has_kelurahan' => false,
                        'has_rute' => true,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Beat', 
                                'subjenis' => [
                                    ['jenis' => 'BEAT 1'], 
                                    ['jenis' => 'BEAT 2'], 
                                    ['jenis' => 'BEAT 3'], 
                                    ['jenis' => 'BEAT 4'], 
                                    ['jenis' => 'BEAT 5'], 
                                    ['jenis' => 'BEAT 6'], 
                                    ['jenis' => 'BEAT 7'], 
                                    ['jenis' => 'BEAT 8'],
                                    ['jenis' => 'BEAT 9'],
                                    ['jenis' => 'BEAT 10'],
                                ]
                            ],
                            [
                                'jenis' => 'Kejadian', 
                                'subjenis' => [
                                    ['jenis' => 'Tindak Pidana'], 
                                    ['jenis' => 'TIPIRING'], 
                                    ['jenis' => 'Laka Lantas'], 
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ]
                ],
            ],
            //Satlantas
            [
                'kode_satuan' => [ 
                    "2201917", "2202010", "2202121", 
                    "2202222", "2202316", "2202416", 
                    "2202510", "2202613", "2204513",
                ],
                'data' => [
                    [
                        'jenis' => 'Laporan Pengaturan',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => true,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Lokasi Pengaturan', 
                                'subjenis' => [
                                    ['jenis' => 'Persimpangan'], 
                                    ['jenis' => 'Putaran/Belok Arah'], 
                                    ['jenis' => 'Penyebrangan'], 
                                    ['jenis' => 'Sekolah'], 
                                    ['jenis' => 'Pasar Keramaian'], 
                                    ['jenis' => 'Jalan Tol'], 
                                    ['jenis' => 'Keadaan Khusus'], 
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                            [
                                'jenis' => 'Jenis Pengaturan', 
                                'subjenis' => [
                                    ['jenis' => 'Percepatan Arus Lalu Lintas'], 
                                    ['jenis' => 'Alih Arus Lalu Lintas'], 
                                    ['jenis' => 'Tutup Arus Lalu Lintas'], 
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Laporan Penjagaan',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Lokasi Penjagaan', 
                                'subjenis' => [
                                    ['jenis' => 'Mako/Kantor'], 
                                    ['jenis' => 'Pos Tetap'], 
                                    ['jenis' => 'Pos Sementara'], 
                                    ['jenis' => 'Induk PJR/Pos Diperkuat'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Giat Pengawalan',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => true,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Giat Pengawalan', 
                                'subjenis' => [
                                    ['jenis' => 'Pimpinan Lembaga RI'], 
                                    ['jenis' => 'Pimpinan & Pejabat Negara Asing'], 
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Laporan Patroli',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => true,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Patroli', 
                                'subjenis' => [
                                    ['jenis' => 'Roda Dua'], 
                                    ['jenis' => 'Roda Empat'],
                                ]
                            ],
                            [
                                'jenis' => 'Lokasi Patroli', 
                                'subjenis' => [
                                    ['jenis' => 'Luar Kota'], 
                                    ['jenis' => 'Jalan Tol'],
                                ]
                            ],
                            [
                                'jenis' => 'Sasaran Patroli', 
                                'subjenis' => [
                                    ['jenis' => 'Datangi/Tangani Lokasi Rawan Macet'], 
                                    ['jenis' => 'Datangi/Tangani Lokasi Rawan Laka'],
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Kegiatan Pelayanan Masyarakat',
                        'has_daftar_rekan' => true,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Pelayanan Masyarakat', 
                                'subjenis' => [
                                    ['jenis' => 'Satpas Keliling'], 
                                    ['jenis' => 'Samsat Keliling'],
                                    ['jenis' => 'Lain-lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Giat SIM',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Pembuatan SIM Baru', 
                                'subjenis' => [
                                    ['jenis' => 'A'], 
                                    ['jenis' => 'A Umum'],
                                    ['jenis' => 'B1'],
                                    ['jenis' => 'B1 Umum'],
                                    ['jenis' => 'B2'],
                                    ['jenis' => 'B2 Umum'],
                                    ['jenis' => 'C'],
                                    ['jenis' => 'D'],
                                ]
                            ],
                            [
                                'jenis' => 'Perpanjangan SIM', 
                                'subjenis' => [
                                    ['jenis' => 'A'], 
                                    ['jenis' => 'A Umum'],
                                    ['jenis' => 'B1'],
                                    ['jenis' => 'B1 Umum'],
                                    ['jenis' => 'B2'],
                                    ['jenis' => 'B2 Umum'],
                                    ['jenis' => 'C'],
                                    ['jenis' => 'D'],
                                ]
                            ],
                            [
                                'jenis' => 'SATPAS KELILING', 
                                'subjenis' => [
                                    ['jenis' => 'A'], 
                                    ['jenis' => 'C'],
                                ]
                            ],
                        ]
                    ],
                ]
            ],
            //Sat Reskrim
            [
                'kode_satuan' => [ 
                    "2201920", "2202013", "2202124", 
                    "2202225", "2202319", "2202419", 
                    "2202513", "2202616", "2204516"
                ],
                'data' => [
                    [
                        'jenis' => 'SP2HP',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'E-Penyidikan',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                ]
            ],
            //Sat Intelkam
            [
                'kode_satuan' => [
                    "2201916", "2202009", "2202120", 
                    "2202221", "2202315", "2202415", 
                    "2202509", "2202612", "2204512" 
                ],
                'data' => [
                    [
                        'jenis' => 'SKCK',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'Perizinan',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                ]
            ],
            //Sat Narkoba
            [
                'kode_satuan' => [
                    "2201921", "2202014", "2202125", 
                    "2202226", "2202320", "2202420", 
                    "2202514", "2202617", "2204517"
                ],
                'data' => [
                    [
                        'jenis' => 'SP2HP',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'E-Penyidikan',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                ]
            ],
            //Sat Sabhara
            [
                'kode_satuan' => [
                    "2201922", "2202015", "2202126",
                    "2202227", "2202321", "2202421",
                    "2202515", "2202618", "2204518"
                ],
                'data' => [
                    [
                        'jenis' => 'Pengaturan',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'Penjagaan',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'Pengawalan',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'Patroli',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'Pengamanan',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'DALMAS',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'TPTKP',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'PAMMAT',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                    [
                        'jenis' => 'TIPIRING',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => false,
                        'has_rute' => false,
                    ],
                ]
            ],
            //Sat Binmas
            [
                'kode_satuan' => [
                    "2201915", "2202008", "2202119",
                    "2202220", "2202314", "2202414",
                    "2202508", "2202611", "2204511"
                ],
                'data' => [
                    [
                        'jenis' => 'Deteksi Dini',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => true,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => '3T',
                                'subjenis' => [
                                    ['jenis' => 'Testing'],
                                    ['jenis' => 'Tracing'],
                                    ['jenis' => 'Treatment'],
                                ]
                            ],
                            [
                                'jenis' => 'PPKM',
                                'subjenis' => [
                                    ['jenis' => 'Tempat Kerja'],
                                    ['jenis' => 'Tempat Makan'],
                                    ['jenis' => 'Tempat Belanja'],
                                    ['jenis' => 'Tempat Ibadah'],
                                    ['jenis' => 'Giat Fas Umum & Sosial'],
                                    ['jenis' => 'Transportasi Umum'],
                                    ['jenis' => 'OBVITNAS'],
                                    ['jenis' => 'Lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Sambang',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => true,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => '3T',
                                'subjenis' => [
                                    ['jenis' => 'Testing'],
                                    ['jenis' => 'Tracing'],
                                    ['jenis' => 'Treatment'],
                                ]
                            ],
                            [
                                'jenis' => 'PPKM',
                                'subjenis' => [
                                    ['jenis' => 'Tempat Kerja'],
                                    ['jenis' => 'Tempat Makan'],
                                    ['jenis' => 'Tempat Belanja'],
                                    ['jenis' => 'Tempat Ibadah'],
                                    ['jenis' => 'Giat Fas Umum & Sosial'],
                                    ['jenis' => 'Transportasi Umum'],
                                    ['jenis' => 'OBVITNAS'],
                                    ['jenis' => 'Lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Pembinaan Polmas',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => true,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => 'Jenis Pembinaan',
                                'subjenis' => [
                                    [
                                        'jenis' => 'PPKM Mikro',
                                        'subjenis' => [
                                            ['jenis' => 'Jenis PPKM Mikro',
                                                'subjenis' => [
                                                    ['jenis' => 'Ketahanan Pangan Keluarga'],
                                                    ['jenis' => 'Edukasi Keluarga'],
                                                    ['jenis' => 'Kesehatan Keluarga'],
                                                    ['jenis' => 'Pembeberdayaan Keluarga'],
                                                ]
                                            ]
                                        ]
                                    ],
                                    ['jenis' => 'Kampung Tangguh'],
                                    ['jenis' => 'Cafe PRESISI'],
                                    ['jenis' => 'Peduli Sesama Berbagi Berkah'],
                                ]
                            ],
                            [
                                'jenis' => '3T',
                                'subjenis' => [
                                    ['jenis' => 'Testing'],
                                    ['jenis' => 'Tracing'],
                                    ['jenis' => 'Treatment'],
                                ]
                            ],
                            [
                                'jenis' => 'PPKM',
                                'subjenis' => [
                                    ['jenis' => 'Tempat Kerja'],
                                    ['jenis' => 'Tempat Makan'],
                                    ['jenis' => 'Tempat Belanja'],
                                    ['jenis' => 'Tempat Ibadah'],
                                    ['jenis' => 'Giat Fas Umum & Sosial'],
                                    ['jenis' => 'Transportasi Umum'],
                                    ['jenis' => 'OBVITNAS'],
                                    ['jenis' => 'Lain'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'jenis' => 'Lain-lain',
                        'has_daftar_rekan' => false,
                        'has_nomor_polisi' => false,
                        'has_kelurahan' => true,
                        'has_rute' => false,
                        'subjenis' => [
                            [
                                'jenis' => '3T',
                                'subjenis' => [
                                    ['jenis' => 'Testing'],
                                    ['jenis' => 'Tracing'],
                                    ['jenis' => 'Treatment'],
                                ]
                            ],
                            [
                                'jenis' => 'PPKM',
                                'subjenis' => [
                                    ['jenis' => 'Tempat Kerja'],
                                    ['jenis' => 'Tempat Makan'],
                                    ['jenis' => 'Tempat Belanja'],
                                    ['jenis' => 'Tempat Ibadah'],
                                    ['jenis' => 'Giat Fas Umum & Sosial'],
                                    ['jenis' => 'Transportasi Umum'],
                                    ['jenis' => 'OBVITNAS'],
                                    ['jenis' => 'Lain'],
                                ]
                            ],
                        ]
                    ],
                ]
            ],
        ];

        $this->seed($kegiatan, 'is_kegiatan');
        $this->seed($quickresponse, 'is_quick_response');
    }

    public function seed($data, $type)
    {
        $is_quick_response = $type == 'is_kegiatan' ? false : true;
        foreach ($data as $row) {
            $kode_satuan = $row['kode_satuan'];
            foreach($row['data'] as $row1) {
                
                $parent = JenisKegiatan::create([
                    'jenis' => $row1['jenis'], 
                    'keterangan' => 'jenis_kegiatan',
                    'has_daftar_rekan' => $row1['has_daftar_rekan'],
                    'has_nomor_polisi' => $row1['has_nomor_polisi'],
                    'has_kelurahan' => $row1['has_kelurahan'],
                    'has_rute' => $row1['has_rute'],
                ]);
                foreach ($kode_satuan as $key => $value) {
                    $kesatuan = Kesatuan::whereRaw("kode_satuan like '$value%'")->get();
                    if($is_quick_response) {
                        $kesatuan = Kesatuan::where("kode_satuan", $value)->get();
                    }
                    foreach ($kesatuan as $keyKesatuan) {
                        JenisKegiatanKesatuan::create(['id_kesatuan' => $keyKesatuan->id, 'id_jenis_kegiatan' => $parent->id]);
                    }
                }

                if(isset($row1['subjenis'])) {
                    foreach($row1['subjenis'] as $row2) {
                        $parent2 = $parent->children()->create(['jenis' => $row2['jenis'], 'keterangan' => 'judul_subjenis']);
                        if(isset($row2['subjenis'])) {
                            foreach($row2['subjenis'] as $row3) {
                                $parent3 = $parent2->children()->create(['jenis' => $row3['jenis'], 'keterangan' => 'subjenis']);
                                if(isset($row3['subjenis'])) {
                                    foreach($row3['subjenis'] as $row4) {
                                        $parent4 = $parent3->children()->create(['jenis' => $row4['jenis'], 'keterangan' => 'judul_dropdown_subjenis']);
                                        if(isset($row4['subjenis'])) {
                                            foreach($row4['subjenis'] as $row5) {
                                                $parent4->children()->create(['jenis' => $row5['jenis'], 'keterangan' => 'dropdown_subjenis']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}