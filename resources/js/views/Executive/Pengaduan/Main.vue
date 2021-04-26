<template>
    <b-container fluid class="p-0">
        <div class="e-topbar transparent">
            <b-row>
                <b-col cols="6 d-flex align-items-center">
                    <h4><router-link to="/executive"><ph-caret-left class="phospor" /></router-link> <span class="semibold ml-3">Pengaduan</span></h4>
                </b-col>
                <b-col cols="6 pt-2">
                    <b-row>
                        <b-col cols="6 d-flex justify-content-end align-items-center" offset="6">
                            <div class="e-topbar-date"><ph-calendar-blank class="phospor"/> 30 Maret 2021</div>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
        </div>
        <div class="e-body">
            <b-row>
                <b-col cols="8">
                    <b-row>
                        <b-col cols="6">
                            <b-row>
                                <b-col cols="6 pb-3">
                                    <div class="e-widget-card e-widget-card-success row">
                                        <b-col cols="7">
                                            <h4 class="font-weight-bold">18700</h4>
                                            <span>Total</span>
                                        </b-col>
                                        <b-col cols="5">
                                            <div class="e-widget-card-circle">
                                                <ph-chart-bar class="phospor"/>
                                            </div>
                                        </b-col>
                                    </div>
                                </b-col>
                                <b-col cols="6 pb-3">
                                    <div class="e-widget-card e-widget-card-success row">
                                        <b-col cols="7">
                                            <h4 class="font-weight-bold">124</h4>
                                            <span>Bulan ini</span>
                                        </b-col>
                                        <b-col cols="5">
                                            <div class="e-widget-card-circle">
                                                <ph-chart-bar class="phospor"/>
                                            </div>
                                        </b-col>
                                    </div>
                                </b-col>
                                <b-col cols="6 pb-3">
                                    <div class="e-widget-card e-widget-card-success row">
                                        <b-col cols="7">
                                            <h4 class="font-weight-bold">74</h4>
                                            <span>Minggu ini</span>
                                        </b-col>
                                        <b-col cols="5">
                                            <div class="e-widget-card-circle">
                                                <ph-chart-bar class="phospor"/>
                                            </div>
                                        </b-col>
                                    </div>
                                </b-col>
                                <b-col cols="6 pb-3">
                                    <div class="e-widget-card e-widget-card-success row">
                                        <b-col cols="7">
                                            <h4 class="font-weight-bold">12</h4>
                                            <span>Hari ini</span>
                                        </b-col>
                                        <b-col cols="5">
                                            <div class="e-widget-card-circle">
                                                <ph-chart-bar class="phospor"/>
                                            </div>
                                        </b-col>
                                    </div>
                                </b-col>
                            </b-row>
                        </b-col>
                        <b-col cols="6">
                            <div class="e-box">
                                <h4 class="font-weight-bold d-inline-block mb-4">Pengirim terbanyak</h4>
                                <perfect-scrollbar class="e-box-list e-box-list-sm">
                                    <b-row class="mb-3">
                                        <b-col cols="3 text-center">
                                            <div class="e-img-circle">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="6 pt-1">
                                            <div class="font-weight-bold">BRIGADIR ISFA WIJAYA</div>
                                            <small>19060213 - ROOPS</small>
                                        </b-col>
                                        <b-col cols="3 text-center"><div class="e-bg-success p-2 rounded d-inline-block">45</div></b-col>
                                    </b-row>
                                    <b-row class="mb-3">
                                        <b-col cols="3 text-center">
                                            <div class="e-img-circle">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="6 pt-1">
                                            <div class="font-weight-bold">BRIGADIR SUBAGYA</div>
                                            <small>19060211 - DITPAMOBVIT</small>
                                        </b-col>
                                        <b-col cols="3 text-center"><div class="e-bg-success p-2 rounded d-inline-block">32</div></b-col>
                                    </b-row>
                                    <b-row class="mb-3">
                                        <b-col cols="3 text-center">
                                            <div class="e-img-circle">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="6 pt-1">
                                            <div class="font-weight-bold">AIPDA FAJAR AGUS</div>
                                            <small>19060210 - POLRES BONTANG</small>
                                        </b-col>
                                        <b-col cols="3 text-center"><div class="e-bg-success p-2 rounded d-inline-block">24</div></b-col>
                                    </b-row>
                                </perfect-scrollbar>
                            </div>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col cols="12">
                            <div class="e-box mt-4">
                                <b-row>
                                    <b-col cols="8">
                                        <h4 class="font-weight-bold d-inline-block mb-4">Statistik <small>(20 Maret 2021 s/d 29 Maret 2021)</small></h4>
                                    </b-col>
                                    <b-col cols="4 text-right">
                                        <span><ph-calendar-blank class="phospor"/></span>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col cols="12">
                                        <canvas id="statistik-chart" height="60px"></canvas>
                                    </b-col>
                                </b-row>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="4">
                   <b-row>
                        <b-col cols="12">
                            <div class="e-box">
                                <h4 class="font-weight-bold d-inline-block mb-4">Pengaduan terbaru</h4>
                                <perfect-scrollbar class="e-box-list e-box-list-xl">
                                    <b-row>
                                        <b-col cols="2">
                                            <div class="e-img-square">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="10 pt-1">
                                            <h6 class="font-weight-bold">SYAIFUL</h6>
                                            <div>Desa Karang Doyo</div>
                                        </b-col>
                                        <b-col cols="12 pt-1">
                                            Untuk pembuatan skck online ko ada pili foto saya pencet ko ndak bisa mohon arahannnya	
                                        </b-col>
                                        <b-col cols="12"><hr/></b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col cols="2">
                                            <div class="e-img-square">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="10 pt-1">
                                            <h6 class="font-weight-bold">ANANG</h6>
                                            <div>Jembatan Mahulu</div>
                                        </b-col>
                                        <b-col cols="12 pt-1">
                                            Penemuan mayat
                                        </b-col>
                                        <b-col cols="12"><hr/></b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col cols="2">
                                            <div class="e-img-square">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="10 pt-1">
                                            <h6 class="font-weight-bold">HERMANSYAH</h6>
                                            <div>Di jalan raya depan smp3 tanah grogot</div>
                                        </b-col>
                                        <b-col cols="12 pt-1">
                                            Zero Dmc
                                        </b-col>
                                        <b-col cols="12"><hr/></b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col cols="2">
                                            <div class="e-img-square">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="10 pt-1">
                                            <h6 class="font-weight-bold">YAMA</h6>
                                            <div>Depan toko swalayan</div>
                                        </b-col>
                                        <b-col cols="12 pt-1">
                                            Zero Dmc
                                        </b-col>
                                        <b-col cols="12"><hr/></b-col>
                                    </b-row>
                                </perfect-scrollbar>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
        </div>
    </b-container>
</template>

<script>
var Chart = require('chart.js')
export default {
    name: 'pengaduan',
    data () {
        return {
            appName: appName,
            baseUrl: baseUrl,
        }
    },
    methods : {
        generateChart () {
            var ctx = document.getElementById('statistik-chart').getContext('2d');
            var gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(117, 226, 164, 0.85)');   
            gradient.addColorStop(1, 'rgba(117, 226, 164, 0)');

            var lineChart = new Chart('statistik-chart', {
                    type: 'line',
                    data: {
                    labels:  ["20-Mar", "21-Mar", "22-Mar", "23-Mar", "24-Mar", "25-Mar", "26-Mar", "27-Mar", "28-Mar", "29-Mar"],
                    datasets: [
                        {
                            label: 'Pengaduan',
                            pointBorderWidth: 4,
                            pointBackgroundColor: '#3DBB76',
                            backgroundColor: gradient,
                            lineTension: 0,
                            data: [ 30, 56, 20, 35, 36, 52, 42, 30, 53, 37 ],
                            borderColor: '#3DBB76',
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    legend: { display: false },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                color: "rgba(0, 0, 0, 0)",
                            },
                            ticks: { fontFamily: "Montserrat" }
                        }],
                        yAxes: [{
                            gridLines: {
                                color: "rgba(0, 0, 0, 0)",
                                zeroLineColor: "white"
                            },
                            ticks: { 
                                beginAtZero: true,
                                fontFamily: "Montserrat"
                            },
                        }]
                    },
                }
            });
        }
    },
    mounted () {
        this.generateChart()
    }
}
</script>