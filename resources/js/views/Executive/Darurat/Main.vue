<template>
    <b-container fluid class="p-0">
        <div class="e-topbar transparent">
            <b-row>
                <b-col cols="6 d-flex align-items-center">
                    <h4><router-link to="/executive"><ph-caret-left class="phospor" /></router-link> <span class="semibold ml-3">Darurat</span></h4>
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
                <b-col cols="4">
                    <b-row>
                        <b-col cols="6 pb-3">
                            <div class="e-widget-card e-widget-card-warning row">
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
                            <div class="e-widget-card e-widget-card-warning row">
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
                            <div class="e-widget-card e-widget-card-warning row">
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
                            <div class="e-widget-card e-widget-card-warning row">
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
                    <b-row>
                        <b-col cols="12">
                            <div class="e-box">
                                <h4 class="font-weight-bold d-inline-block mb-4">Darurat terbaru</h4>
                                <perfect-scrollbar class="e-box-list e-box-list-lg">
                                    <b-row>
                                        <b-col cols="12 pt-1">
                                            <h6 class="font-weight-bold">BRIGADIR ISFA WIJAYA</h6>
                                            <div>19060213 - ROOPS</div>
                                            <div>30 Maret 2021 09:00</div>
                                        </b-col>
                                        <b-col cols="12"><hr/></b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col cols="12 pt-1">
                                            <h6 class="font-weight-bold">FAIS ANDALUSIA</h6>
                                            <div>Masyarakat</div>
                                            <div>30 Maret 2021 09:00</div>
                                        </b-col>
                                        <b-col cols="12"><hr/></b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col cols="12 pt-1">
                                            <h6 class="font-weight-bold">ANDIKA SAI</h6>
                                            <div>Masyarakat</div>
                                            <div>30 Maret 2021 09:00</div>
                                        </b-col>
                                        <b-col cols="12"><hr/></b-col>
                                    </b-row>
                                </perfect-scrollbar>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="8">
                    <b-row>
                        <b-col cols="7">
                            <b-row>
                                <b-col cols="12">
                                    <div class="e-box">
                                        <b-row>
                                            <b-col cols="8">
                                                <h4 class="font-weight-bold d-inline-block mb-4">Per status <small>(30 Maret 2021)</small></h4>
                                            </b-col>
                                            <b-col cols="4 text-right">
                                                <span><ph-calendar-blank class="phospor"/></span>
                                            </b-col>
                                        </b-row>
                                        <b-row class="align-items-center">
                                            <b-col cols="6 pt-1">
                                                <div class="w-100 pl-0 pr-3">
                                                    <canvas id="status-chart" width="600"></canvas>
                                                </div>
                                            </b-col>
                                            <b-col cols="6 p-0 h-100">
                                                <div class="e-box-spacer"></div>
                                                <b-row class="mb-3">
                                                    <b-col cols="8 d-flex align-items-center">  
                                                        <div class="e-pill e-pill-warning-1"></div>
                                                        Belum ditangani
                                                    </b-col>
                                                    <b-col cols="3 text-center"><div class="e-bg-warning p-1 px-3 rounded d-inline-block">1</div></b-col>
                                                </b-row>
                                                <b-row class="mb-3">
                                                    <b-col cols="8 d-flex align-items-center">  
                                                        <div class="e-pill e-pill-warning-2"></div>
                                                        Proses penanganan
                                                    </b-col>
                                                    <b-col cols="3 text-center"><div class="e-bg-warning p-1 px-3 rounded d-inline-block">2</div></b-col>
                                                </b-row>
                                            </b-col>
                                        </b-row>
                                    </div>
                                </b-col>
                            </b-row>
                        </b-col>
                        <b-col cols="5">
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
                                        <b-col cols="3 text-center"><div class="e-bg-warning p-2 rounded d-inline-block">45</div></b-col>
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
                                        <b-col cols="3 text-center"><div class="e-bg-warning p-2 rounded d-inline-block">32</div></b-col>
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
                                        <b-col cols="3 text-center"><div class="e-bg-warning p-2 rounded d-inline-block">24</div></b-col>
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
            </b-row>
        </div>
    </b-container>
</template>

<script>
var Chart = require('chart.js')
export default {
    name: 'darurat',
    data () {
        return {
            appName: appName,
            baseUrl: baseUrl,
        }
    },
    methods : {
        generateChart () {
            var circleChart = new Chart('status-chart', {
                    type: 'pie',
                    data: {
                    labels:  ["Diubah ke kejadian", "Tidak diubah ke kejadian"],
                    datasets: [
                        {
                            label: 'Jumlah',
                            data: [ 2, 4],
                            backgroundColor: [ '#FAA16F', '#FFDCC9' ],
                            borderWidth: 0,
                            fill: 'start'
                        },
                    ]
                },
                options: {
                    legend: { display: false, maxWidth: 10 },
                    elements: { borderColor: '#D7DDF4' },
                    responsive: true,
                    maintainAspectRatio: false,
                },
            });

            var ctx = document.getElementById('statistik-chart').getContext('2d');
            var gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(250, 165, 118, 0.85)');   
            gradient.addColorStop(1, 'rgba(250, 165, 118, 0)');

            var lineChart = new Chart('statistik-chart', {
                    type: 'line',
                    data: {
                    labels:  ["20-Mar", "21-Mar", "22-Mar", "23-Mar", "24-Mar", "25-Mar", "26-Mar", "27-Mar", "28-Mar", "29-Mar"],
                    datasets: [
                        {
                            label: 'Darurat',
                            pointBorderWidth: 4,
                            pointBackgroundColor: '#F98531',
                            backgroundColor: gradient,
                            lineTension: 0,
                            data: [ 30, 56, 20, 35, 36, 52, 42, 30, 53, 37 ],
                            borderColor: '#F98531',
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