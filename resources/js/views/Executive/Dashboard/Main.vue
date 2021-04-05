<template>
    <b-container fluid class="p-0">
        <div class="e-topbar">
            <b-row>
                <b-col cols="6 d-flex align-items-center">
                    <b-img :src="`${baseUrl}/assets/logo.png`" class="e-topbar-logo" fluid />
                    <div class="e-topbar-brand ">{{ appName }}</div>
                </b-col>
                <b-col cols="6 pt-2">
                    <b-row>
                        <b-col cols="6 d-flex justify-content-end align-items-center" offset="1">
                            <div><ph-calendar-blank class="phospor"/> 30 Maret 2021</div>
                        </b-col>
                        <b-col cols="5 d-flex justify-content-end align-items-center">
                            <b-dropdown right class="e-profile">
                                <template #button-content>
                                    Polda Kalimantan Timur <ph-caret-down class="phospor ml-3"/>   
                                </template>
                                <b-dropdown-item to="/">Monit</b-dropdown-item>
                                <b-dropdown-item @click="$parent.triggerLogout()">Logout</b-dropdown-item>
                            </b-dropdown>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
        </div>
        <div class="e-body">
            <b-row>
                <b-col cols="8">
                    <b-row>
                        <b-col cols="4">
                            <div class="e-widget">
                                <div class="e-widget-icon e-widget-icon-info">
                                    <ph-squares-four class="phospor"/>
                                </div>
                                <div class="e-widget-body">
                                    <div>32</div>
                                    <span>Kesatuan</span>
                                </div>
                            </div>
                        </b-col>
                        <b-col cols="4">
                            <div class="e-widget">
                                <div class="e-widget-icon e-widget-icon-primary">
                                    <ph-circles-three class="phospor"/>
                                </div>
                                <div class="e-widget-body">
                                    <div>9</div>
                                    <span>Polres Jajaran</span>
                                </div>
                            </div>
                        </b-col>
                        <b-col cols="4">
                            <div class="e-widget">
                                <div class="e-widget-icon e-widget-icon-success">
                                    <ph-pedestrian class="phospor"/>
                                </div>
                                <div class="e-widget-body">
                                    <div>8722</div>
                                    <span>Personil</span>
                                </div>
                            </div>
                        </b-col>
                    </b-row>
                    <b-row class="mt-4">
                        <b-col cols="3 pr-0">
                            <div class="e-tile">
                                <div class="e-tile-body">
                                    <div>18770</div>
                                    <span>Kegiatan</span>
                                </div>
                                <div class="e-tile-icon e-tile-icon-primary">
                                    <div>
                                        <ph-shield-chevron class="phospor"/>
                                    </div>
                                </div>
                            </div>
                        </b-col>
                        <b-col cols="3 pr-0">
                            <div class="e-tile">
                                <div class="e-tile-body">
                                    <div>135</div>
                                    <span>Pengaduan</span>
                                </div>
                                <div class="e-tile-icon e-tile-icon-success">
                                    <div>
                                        <ph-newspaper-clipping class="phospor"/>
                                    </div>
                                </div>
                            </div>
                        </b-col>
                        <b-col cols="3 pr-0">
                            <div class="e-tile">
                                <div class="e-tile-body">
                                    <div>177</div>
                                    <span>Kejadian</span>
                                </div>
                                <div class="e-tile-icon e-tile-icon-danger">
                                    <div>
                                        <ph-activity class="phospor"/>
                                    </div>
                                </div>
                            </div>
                        </b-col>
                        <b-col cols="3 pr-0">
                            <div class="e-tile">
                                <div class="e-tile-body">
                                    <div>895</div>
                                    <span>Darurat</span>
                                </div>
                                <div class="e-tile-icon e-tile-icon-warning">
                                    <div>
                                        <ph-shield-warning class="phospor"/>
                                    </div>
                                </div>
                            </div>
                        </b-col>
                    </b-row>
                    <b-row class="mt-4">
                        <b-col cols="7">
                            <div class="e-box">
                                <h4 class="font-weight-bold d-inline-block mb-4">Statistik</h4> <span>(7 hari terakhir)</span>
                                <div class="position-relative h-100">
                                    <canvas id="statistik-chart"></canvas>
                                </div>
                            </div>
                        </b-col>
                        <b-col cols="5">
                            <div class="e-box">
                                <h4 class="font-weight-bold d-inline-block mb-4">Personil paling aktif</h4>
                                <perfect-scrollbar class="e-box-list">
                                    <b-row class="mb-3">
                                        <b-col cols="4 text-center">
                                            <div class="e-img-circle">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="8 pt-1">
                                            <h6 class="font-weight-bold">BRIGADIR ISFA WIJAYA</h6>
                                            <div>19060213 - ROOPS</div>
                                        </b-col>
                                    </b-row>
                                    <b-row class="mb-3">
                                        <b-col cols="4 text-center">
                                            <div class="e-img-circle">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="8 pt-1">
                                            <h6 class="font-weight-bold">BRIGADIR SUBAGYA</h6>
                                            <div>19060211 - DITPAMOBVIT</div>
                                        </b-col>
                                    </b-row>
                                    <b-row class="mb-3">
                                        <b-col cols="4 text-center">
                                            <div class="e-img-circle">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="8 pt-1">
                                            <h6 class="font-weight-bold">AIPDA FAJAR AGUS</h6>
                                            <div>19060210 - POLRES BONTANG</div>
                                        </b-col>
                                    </b-row>
                                    <b-row class="mb-3">
                                        <b-col cols="4 text-center">
                                            <div class="e-img-circle">
                                                <b-img :src="`${baseUrl}/assets/logo.png`" alt=""/>
                                            </div>
                                        </b-col>
                                        <b-col cols="8 pt-1">
                                            <h6 class="font-weight-bold">BRIGADIR ZIFAL DAHMA</h6>
                                            <div>19060219 - POLRES KUTAI TIMUR</div>
                                        </b-col>
                                    </b-row>
                                </perfect-scrollbar>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="4">
                    <b-row>
                        <b-col cols="12">
                            <div class="e-box">
                                <h4 class="font-weight-bold d-inline-block mb-4">Lokasi bekerja personil</h4>
                                <b-row class="align-items-center">
                                    <b-col cols="6">
                                        <canvas id="lokasi-chart" width="500"></canvas>
                                    </b-col>
                                    <b-col cols="6 p-0 h-100">
                                        <div class="e-box-spacer"></div>
                                        <b-row class="mb-3">
                                            <b-col cols="8">  
                                                <div class="e-circle e-circle-aqua">
                                                    <ph-airplane-in-flight class="phospor"/>
                                                </div>
                                                Dinas luar
                                            </b-col>
                                            <b-col cols="4 text-center pt-2"><h5 class="font-weight-bold">247</h5></b-col>
                                        </b-row>
                                        <b-row class="mb-3">
                                            <b-col cols="8">  
                                                <div class="e-circle e-circle-blue">
                                                    <ph-suitcase class="phospor"/>
                                                </div>
                                                Kantor
                                            </b-col>
                                            <b-col cols="4 text-center pt-2"><h5 class="font-weight-bold">392</h5></b-col>
                                        </b-row>
                                        <b-row class="mb-3">
                                            <b-col cols="8">  
                                                <div class="e-circle e-circle-purple">
                                                    <ph-house-line class="phospor"/>
                                                </div>
                                                Rumah
                                            </b-col>
                                            <b-col cols="4 text-center pt-2"><h5 class="font-weight-bold">283</h5></b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>
                            </div>
                        </b-col>
                    </b-row>
                    <b-row class="mt-4">
                        <b-col cols="12">
                            <div class="e-box">
                                <h4 class="font-weight-bold d-inline-block mb-4">Kondisi personil</h4>
                                <b-row class="align-items-center">
                                    <b-col cols="6">
                                        <canvas id="kondisi-chart" width="500"></canvas>
                                    </b-col>
                                    <b-col cols="6 p-0 h-100">
                                        <div class="e-box-spacer"></div>
                                        <b-row class="mb-3">
                                            <b-col cols="8">  
                                                <div class="e-circle e-circle-green">
                                                    <ph-smiley class="phospor"/>
                                                </div>
                                                Sehat
                                            </b-col>
                                            <b-col cols="4 text-center pt-2"><h5 class="font-weight-bold">247</h5></b-col>
                                        </b-row>
                                        <b-row class="mb-3">
                                            <b-col cols="8">  
                                                <div class="e-circle e-circle-orange">
                                                    <ph-smiley-meh class="phospor"/>
                                                </div>
                                                Kurang Fit
                                            </b-col>
                                            <b-col cols="4 text-center pt-2"><h5 class="font-weight-bold">392</h5></b-col>
                                        </b-row>
                                        <b-row class="mb-3">
                                            <b-col cols="8">  
                                                <div class="e-circle e-circle-red">
                                                    <ph-smiley-nervous class="phospor"/>
                                                </div>
                                                Sakit
                                            </b-col>
                                            <b-col cols="4 text-center pt-2"><h5 class="font-weight-bold">283</h5></b-col>
                                        </b-row>
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
    name: 'dashboard',
    data () {
        return {
            appName: appName,
            baseUrl: baseUrl,
        }
    },
    methods : {
        generateChart () {
            var barChart = new Chart('statistik-chart', {
                    type: 'bar',
                    data: {
                    labels:  ["23-Mar", "24-Mar", "25-Mar", "26-Mar", "27-Mar", "28-Mar", "29-Mar"],
                    datasets: [
                        {
                            label: 'Kegiatan',
                            data: [ 2, 4, 5, 8, 7, 4, 2 ],
                            backgroundColor: '#7F98F3'
                        },
                        {
                            label: 'Pengaduan',
                            data: [ 2, 3, 7, 9, 1, 1, 3 ],
                            backgroundColor: '#69E49A'
                        },
                        {
                            label: 'Kejadian',
                            data: [ 2, 5, 8, 2, 3, 9, 8 ],
                            backgroundColor: '#F8747D'
                        },
                        {
                            label: 'Darurat',
                            data: [ 9, 7, 2, 3, 2, 2, 8 ],
                            backgroundColor: '#FFC165'
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        xAxes: [{
                            stacked: true,
                        }],
                        yAxes: [{
                            stacked: true
                        }]
                    },
                    datasets: { bar: { categoryPercentage: 1.0, barPercentage: 0.5 } }
                },
                plugins: [{
                    beforeInit: function(chart, options) {
                        chart.legend.afterFit = function() {
                            this.height = this.height + 30;
                        };
                    }
                }]
            });
            var polarChart = new Chart('lokasi-chart', {
                    type: 'polarArea',
                    data: {
                    labels:  ["Dinas luar", "Kantor", "Rumah"],
                    datasets: [
                        {
                            label: 'Jumlah',
                            data: [ 247, 392, 283 ],
                            backgroundColor: [ '#95DFFF', '#7EA2FF', '#7783F2' ]
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
            var doughnutChart = new Chart('kondisi-chart', {
                    type: 'doughnut',
                    data: {
                    labels:  ["Sehat", "Kurang Fit", "Sakit"],
                    datasets: [
                        {
                            label: 'Jumlah',
                            data: [ 247, 392, 283 ],
                            backgroundColor: [ '#6BDDA6', '#FBA85C', '#FD7E86' ],
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
        }
    },
    mounted () {
        this.generateChart()
    }
}
</script>