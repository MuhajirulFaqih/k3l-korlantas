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
            //Satlantas
            [
                'kode_satuan' => [ 
                    '2201917', '220200710', '2202010', '220211406', '220211510', '2202121', 
                    '220220406', '220220807', '220221006', '220221408', '220221810', '220221910', 
                    '2202222', '220223501', '220230907', '220231212', '2202316', '220240406', 
                    '220240506', '220240606', '2202416', '220250406', '220250606', '220250706', 
                    '2202510', '220260411', '220260511', '220260611', '220260711', '220260811', 
                    '220260911', '220261011', '2202613', '2204513', '220452611', '220452707', 
                    '220452811', '220452911', '220453011', '220453111' 
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
                    '220190409', '220190508', '220190608', '220190708', '220190808', '220190908', 
                    '220191008', '220191108', '220191208', '220191308', '220191408', '2201920', 
                    '220200409', '220200509', '220200608', '220200708', '2202013', '220210408', 
                    '220210508', '220210608', '220210708', '220210808', '220210908', '220211008', 
                    '220211108', '220211208', '220211308', '220211409', '220211508', '220211608', 
                    '220211708', '220211808', '2202124', '220220409', '220220508', '220220608', 
                    '220220708', '220220810', '220220908', '220221009', '220221108', '220221208', 
                    '220221308', '220221411', '220221508', '220221608', '220221708', '220221808', 
                    '220221908', '2202225', '220230408', '220230509', '220230607', '220230708', 
                    '220230809', '220230910', '220231007', '220231110', '220231210', '2202319', 
                    '220240409', '220240509', '220240609', '220240708', '220240808', '220240908', 
                    '220241008', '220241108', '220241208', '220241307', '2202419', '220250409', 
                    '220250508', '220250609', '220250709', '2202513', '220260409', '220260509', 
                    '220260609', '220260709', '220260809', '220260909', '220261009', '2202616', 
                    '2204516', '220452609', '220452710', '220452809', '220452909', '220453009', '220453109',  
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
                    "220190406", "220190505", "220190605", "220190705", "220190805", "220190905", 
                    "220191005", "220191105", "220191205", "220191305", "220191405", "2201916", 
                    "220200406", "220200506", "220200605", "220200705", "2202009", "220210405", 
                    "220210505", "220210605", "220210705", "220210805", "220210905", "220211005", 
                    "220211105", "220211205", "220211305", "220211405", "220211505", "220211605", 
                    "220211705", "220211805", "2202120", "220220405", "220220505", "220220605", "220220705", 
                    "220220806", "220220905", "220221005", "220221105", "220221205", "220221305", "220221407", 
                    "220221505", "220221605", "220221705", "220221805", "220221905", "2202221", "220230405", 
                    "220230506", "220230604", "220230705", "220230806", "220230906", "220231004", "220231107", 
                    "220231207", "2202315", "220240405", "220240505", "220240605", "220240705", "220240805", "220240905", 
                    "220241005", "220241105", "220241205", "220241304", "2202415", "220250405", "220250505", "220250605", 
                    "220250705", "2202509", "220260406", "220260506", "220260606", "220260706", "220260806", "220260906", 
                    "220261006", "2202612", "2204512", "220452606", "220452706", "220452806", "220452906", "220453006", "220453106", 
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
                    "2201921", "2202014", "2202125", "2202226", "2202320", "2202420", "2202514", "2202617", "2204517", 
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
                    "220190410", "220190509", "220190609", "220190709", "220190809", "220190909", 
                    "220191009", "220191109", "220191209", "220191309", "220191409", "2201922", 
                    "220200410", "220200510", "220200609", "220200709", "2202015", "220210409", 
                    "220210509", "220210609", "220210709", "220210809", "220210909", "220211009", 
                    "220211109", "220211209", "220211309", "220211410", "220211509", "220211609", 
                    "220211709", "220211809", "2202126", "220220410", "220220509", "220220609", "220220709", 
                    "220220811", "220220909", "220221010", "220221109", "220221209", "220221309", "220221412", 
                    "220221509", "220221609", "220221709", "220221809", "220221909", "2202227", "220230409", "220230510", 
                    "220230608", "220230709", "220230810", "220230911", "220231008", "220231111", "220231211", "2202321", 
                    "220240410", "220240510", "220240610", "220240709", "220240809", "220240909", "220241009", "220241109", 
                    "220241209", "220241308", "2202421", "220250410", "220250509", "220250610", "220250710", "2202515", 
                    "220260410", "220260510", "220260610", "220260710", "220260810", "220260910", "220261010", "2202618", 
                    "2204518", "220452610", "220452711", "220452810", "220452910", "220453010", "220453110", 
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
                    "220190405", "220190504", "220190604", "220190704", "220190804", "220190904", 
                    "220191004", "220191104", "220191204", "220191304", "220191404", "2201915", 
                    "220200405", "220200505", "220200604", "220200704", "2202008", "220210404", 
                    "220210504", "220210604", "220210704", "220210804", "220210904", "220211004", 
                    "220211104", "220211204", "220211304", "220211404", "220211504", "220211604", 
                    "220211704", "220211804", "2202119", "220220404", "220220504", "220220604", 
                    "220220704", "220220805", "220220904", "220221004", "220221104", "220221204", 
                    "220221304", "220221406", "220221504", "220221604", "220221704", "220221804", 
                    "220221904", "2202220", "220230404", "220230505", "220230603", "220230704", 
                    "220230805", "220230905", "220231003", "220231106", "220231206", "2202314", 
                    "220240404", "220240504", "220240604", "220240704", "220240804", "220240904", 
                    "220241004", "220241104", "220241204", "220241303", "2202414", "220250404", "220250504", 
                    "220250604", "220250704", "2202508", "220260405", "220260505", "220260605", "220260705", 
                    "220260805", "220260905", "220261005", "2202611", "2204511", "220452605", "220452705", 
                    "220452805", "220452905", "220453005", "220453105", 
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
                    'is_quick_response' => $is_quick_response,
                    'has_daftar_rekan' => $row1['has_daftar_rekan'],
                    'has_nomor_polisi' => $row1['has_nomor_polisi'],
                    'has_kelurahan' => $row1['has_kelurahan'],
                    'has_rute' => $row1['has_rute'],
                ]);
                foreach ($kode_satuan as $key => $value) {
                    $kesatuan = Kesatuan::whereRaw("kode_satuan like '$value%'")->get();
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

// Query for get ID Quick Response

/*
SELECT * FROM pers_kesatuan WHERE kesatuan LIKE '%lantas%' AND kesatuan != 'DITLANTAS';
SELECT * FROM pers_kesatuan WHERE kesatuan LIKE '%reskrim%' AND kesatuan != 'DITRESKRIMSUS' AND kesatuan != 'DITRESKRIMUM';
SELECT * FROM pers_kesatuan WHERE kesatuan LIKE '%narkoba%' AND kesatuan != 'DITRESNARKOBA';
SELECT * FROM pers_kesatuan WHERE kesatuan LIKE '%sabhara%' ;
SELECT * FROM pers_kesatuan WHERE kesatuan LIKE '%binmas%' AND kesatuan != 'SUBNITBINMASAIR' AND kesatuan != 'SISARBINMASAIRDANPOTDIRGA' AND kesatuan != 'DITBINMAS';
/*