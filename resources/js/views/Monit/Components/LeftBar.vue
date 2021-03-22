<template>
    <div class="leftbar-outline">
        <ul class="leftbar">
            <perfect-scrollbar class="h-100">
                <li class="leftbar-header">Peta</li>
                <li :class="openMenuClass('peta')">
                    <div @click="toggleSub('peta')">
                        <ph-map-pin-line class="phospor" /> 
                        <span>Setting</span>
                    </div>
                </li>
                <li :class="openMenuClass('tracking')">
                    <div @click="toggleSub('tracking')">
                        <ph-pedestrian class="phospor" /> 
                        <span>Tracking</span>
                    </div>
                </li>
                <li :class="openMenuClass('kegiatan')">
                    <div @click="toggleSub('kegiatan')">
                        <ph-shield-chevron class="phospor" />
                        <span>Kegiatan</span>
                        <span class="circle circle-primary"></span>
                    </div>
                </li>
                <li :class="openMenuClass('pengaduan')">
                    <div @click="toggleSub('pengaduan')">
                        <ph-newspaper-clipping class="phospor" />
                        <span>Pengaduan</span>
                        <span class="circle circle-success"></span>
                    </div>
                </li>
                <li :class="openMenuClass('kejadian')">
                    <div @click="toggleSub('kejadian')">
                        <ph-activity class="phospor" />
                        <span>Kejadian</span>
                        <span class="circle circle-danger"></span>
                    </div>
                </li>
                <li :class="hotspot ? 'active' : ''">
                    <div @click="triggerHotspot();">
                        <ph-fire class="phospor" />
                        <span>Titik Api</span>
                    </div>
                </li>
                <li :class="openMenuClass('lokasi-vital')">
                    <div @click="toggleSub('lokasi-vital')">
                        <ph-activity class="phospor" />
                        <span>Lokasi Vital</span>
                    </div>
                </li>
                <li :class="openMenuClass('area')">
                    <div @click="toggleSub('area')">
                        <ph-globe-hemisphere-west class="phospor" />
                        <span>Area</span>
                    </div>
                </li>
            </perfect-scrollbar>
        </ul>
        <!-- Sub leftbar -->
        <ul :class="`leftbar-sub ${openMenuClass('peta')}`">
            <li class="text-right p-0 px-2">
                <div @click="openMenu = ''"><ph-x class="phospor"/></div>
            </li>
            <li @click="settingDarkMode">
                <button :class="`e-btn ${darkMode ? `e-btn-primary` : `e-btn-secondary`} leftbar-sub-icon btn`">
                    <ph-moon class="phospor" />
                </button>
                <span>Dark Mode</span>
            </li>
            <li @click="settingLabel">
                <button :class="`e-btn ${label ? `e-btn-primary` : `e-btn-secondary`} leftbar-sub-icon btn`">
                    <ph-list-dashes class="phospor" />
                </button>
                <span>Label</span>
            </li>
            <li @click="settingTraffic">
                <button :class="`e-btn ${traffic ? `e-btn-primary` : `e-btn-secondary`} leftbar-sub-icon btn`">
                    <ph-car class="phospor" />
                </button>
                <span>Traffic</span>
            </li>
            <li class="leftbar-sub-header">
                Jenis Peta
            </li>
            <li @click="settingJenisPeta('default')">
                <button :class="`e-btn ${jenisPeta == 'default' ? `e-btn-primary` : `e-btn-secondary`} btn btn-block text-left`">
                    <ph-navigation-arrow class="phospor" /> 
                    Default
                </button>
            </li>
            <li @click="settingJenisPeta('satellite')">
                <button :class="`e-btn ${jenisPeta == 'satellite' ? `e-btn-primary` : `e-btn-secondary`} btn btn-block text-left`">
                    <ph-globe-hemisphere-east class="phospor" /> 
                    Satellite
                </button>
            </li>
            <li  @click="settingJenisPeta('terrain')">
                <button :class="`e-btn ${jenisPeta == 'terrain' ? `e-btn-primary` : `e-btn-secondary`} btn btn-block text-left`" >
                    <ph-map-trifold class="phospor" /> 
                    Terrain
                </button>
            </li>
            <li class="leftbar-sub-header">
                Menu
            </li>
            <li @click="settingTopBar">
                <button :class="`e-btn ${topbar ? `e-btn-primary` : `e-btn-secondary`} leftbar-sub-icon btn`">
                    <ph-eye class="phospor" />
                </button>
                <span>Menu atas</span>
            </li>
            <li @click="settingRightBar">
                <button :class="`e-btn ${rightbar ? `e-btn-primary` : `e-btn-secondary`} leftbar-sub-icon btn`">
                    <ph-eye class="phospor" />
                </button>
                <span>Menu kanan</span>
            </li>
        </ul>
        <ul :class="`leftbar-sub ${openMenuClass('tracking')}`">
            <li class="text-right p-0 px-2">
                <div @click="openMenu = ''"><ph-x class="phospor"/></div>
            </li>
            <li class="leftbar-sub-header">Tracking Personil</li>
            <li>
                <button class="e-btn leftbar-sub-icon btn">
                    <ph-eye class="phospor" />
                </button>
                <span>Tampil</span>
            </li>
            <li class="leftbar-sub-header">
                Status
            </li>
            <li>
                <button class="e-btn e-btn-primary btn btn-block text-left">
                    <ph-users class="phospor" /> 
                    Semua
                </button>
            </li>
            <li>
                <button class="e-btn e-btn-secondary btn btn-block text-left">
                    <ph-identification-card class="phospor" /> 
                    Dinas
                </button>
            </li>
            <li>
                <button class="e-btn e-btn-secondary btn btn-block text-left">
                    <ph-user-rectangle class="phospor" /> 
                    Lepas Dinas
                </button>
            </li>
        </ul>
        <ul :class="`leftbar-sub ${openMenuClass('kegiatan')}`">
            <li class="text-right p-0 px-2">
                <div @click="openMenu = ''"><ph-x class="phospor"/></div>
            </li>
            <li class="leftbar-sub-header">Kegiatan</li>
            <li @click="toggleKegiatan">
                <button :class="`e-btn ${kegiatanStatus ? `e-btn-primary` : `e-btn-secondary`} leftbar-sub-icon btn`">
                    <ph-eye class="phospor" />
                </button>
                <span>Tampil</span>
            </li>
            <li v-if="kegiatanStatus">
                Menampilkan
                <vue-slider
                    class="e-slider e-slider-primary"
                    v-model="kegiatan"
                    v-bind="optionsSlider"
                ></vue-slider>
            </li>
        </ul>
        <ul :class="`leftbar-sub ${openMenuClass('pengaduan')}`">
            <li class="text-right p-0 px-2">
                <div @click="openMenu = ''"><ph-x class="phospor"/></div>
            </li>
            <li class="leftbar-sub-header">Pengaduan</li>
            <li @click="togglePengaduan">
                <button :class="`e-btn ${pengaduanStatus ? `e-btn-primary` : `e-btn-secondary`} leftbar-sub-icon btn`">
                    <ph-eye class="phospor" />
                </button>
                <span>Tampil</span>
            </li>
            <li v-if="pengaduanStatus">
                Menampilkan
                <vue-slider
                    class="e-slider e-slider-primary"
                    v-model="pengaduan"
                    v-bind="optionsSlider"
                ></vue-slider>
            </li>
        </ul>
        <ul :class="`leftbar-sub ${openMenuClass('kejadian')}`">
            <li class="text-right p-0 px-2">
                <div @click="openMenu = ''"><ph-x class="phospor"/></div>
            </li>
            <li class="leftbar-sub-header">Kejadian</li>
            <li @click="toggleKejadian">
                <button :class="`e-btn ${kejadianStatus ? `e-btn-primary` : `e-btn-secondary`} leftbar-sub-icon btn`">
                    <ph-eye class="phospor" />
                </button>
                <span>Tampil</span>
            </li>
            <li v-if="kejadianStatus">
                Menampilkan
                <vue-slider
                    class="e-slider e-slider-primary"
                    v-model="kejadian"
                    v-bind="optionsSlider"
                ></vue-slider>
            </li>
        </ul>
        <ul :class="`leftbar-sub ${openMenuClass('lokasi-vital')}`">
            <li class="text-right p-0 px-2">
                <div @click="openMenu = ''"><ph-x class="phospor"/></div>
            </li>
            <li class="leftbar-sub-header">Lokasi Vital</li>
            <perfect-scrollbar class="h-100">
                
            </perfect-scrollbar>
        </ul>
        <div class="leftbar-bottom">
            <div v-if="$parent.markerSingleShow">
                <button class="e-btn e-btn-warning btn btn-block">Refresh Data</button>
            </div>
            <div>
                <b-row>
                    <b-col cols="6 pr-lg-0">
                        <button @click="zoomOut" class="e-btn e-btn-primary btn btn-block"><h4 class="mb-0">-</h4></button>
                    </b-col>
                    <b-col cols="6 pl-lg-0">
                        <button @click="zoomIn" class="e-btn e-btn-primary btn btn-block"><h4 class="mb-0">+</h4></button>
                    </b-col>
                </b-row>
            </div>
        </div>
    </div>
</template>

<script>
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/default.css'
import { debounce } from 'lodash'
export default {
    name: 'leftbar',
    components: { VueSlider },
    data () {
        return {
            isBusy: false,
            openMenu: '',
            darkMode: true,
            label: false,
            traffic: false,
            jenisPeta: 'default',
            trafficLayer: null,
            topbar: true,
            rightbar: true,
            kegiatanStatus: true,
            kegiatan: 10,
            pengaduanStatus: true,
            pengaduan: 10,
            kejadianStatus: true,
            kejadian: 10,
            hotspot: false,
            optionsSlider: {
                eventType: 'auto',
                width: '100%',
                height: 6,
                dotSize: 10,
                dotHeight: null,
                dotWidth: null,
                min: 0,
                max: 100,
                interval: 1,
                show: true,
                speed: 0.5,
                tooltip: 'always',
                tooltipPlacement: 'bottom',
            },
        }
    },
    computed:{
        
    },
    methods: {
        openMenuClass (menu) {
            return this.openMenu == menu ? 'open' : ''
        },
        toggleSub (menu) {
            this.openMenu = this.openMenu == menu ? '' : menu
        },
        zoomIn () {
            var maps = this.$parent.$refs.maps
            maps.$mapPromise.then((map) => {
                map.setZoom(map.getZoom() + 1)
            });
        },
        zoomOut () {
            var maps = this.$parent.$refs.maps
            maps.$mapPromise.then((map) => {
                map.setZoom(map.getZoom() - 1)
            });
        },
        addLabelMaps () {
            var presetLabel = {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
            this.$parent.darkStyle.push(presetLabel)
            this.$parent.lightStyle.push(presetLabel)
        },
        removeLabelMaps () {
            var darkStyle = this.$parent.darkStyle
            var indexDarkStyle = darkStyle.findIndex(v => v.elementType == 'labels' && v.featureType == 'poi')
            darkStyle.splice(indexDarkStyle, 1)
            
            var lightStyle = this.$parent.lightStyle
            var indexLightStyle = lightStyle.findIndex(v => v.elementType == 'labels' && v.featureType == 'poi')
            lightStyle.splice(indexLightStyle, 1)
        },
        addGeometryMaps () {
            var presetLabel = {
                featureType: "road",
                elementType: "geometry",
                stylers: [{ visibility: "off" }]
            }
            this.$parent.darkStyle.push(presetLabel)
            this.$parent.lightStyle.push(presetLabel)
        },
        removeGeometryMaps () {
            var darkStyle = this.$parent.darkStyle
            var indexDarkStyle = darkStyle.findIndex(v => v.elementType == 'geometry' && v.featureType == 'road' && v.stylers[0].visibility == 'off')
            darkStyle.splice(indexDarkStyle, 1)
            
            var lightStyle = this.$parent.lightStyle
            var indexLightStyle = lightStyle.findIndex(v => v.elementType == 'geometry' && v.featureType == 'road' && v.stylers[0].visibility == 'off')
            lightStyle.splice(indexLightStyle, 1)
        },
        addTrafficMaps () {
            var maps = this.$parent.$refs.maps
            maps.$mapPromise.then((map) => {
                this.trafficLayer = new google.maps.TrafficLayer();
                this.trafficLayer.setMap(map);
            });
        },
        removeTrafficMaps () {
            this.trafficLayer.setMap(null);
        },
        settingDarkMode () {
            this.darkMode = !this.darkMode
        },
        settingLabel () {
            this.label = !this.label
        },
        settingTraffic () {
            this.traffic = !this.traffic
        },
        settingTopBar () {
            this.topbar = !this.topbar
        },
        settingRightBar () {
            this.rightbar = !this.rightbar
        },
        settingJenisPeta (val) {
            this.jenisPeta = val
        },
        triggerHotspot () {
            if(!this.isBusy) { this.hotspot = !this.hotspot }
        },
        toggleKegiatan (val) {
            this.kegiatanStatus = this.$parent.kegiatanStatus = !this.kegiatanStatus
        },
        togglePengaduan (val) {
            this.pengaduanStatus = this.$parent.pengaduanStatus = !this.pengaduanStatus
        },
        toggleKejadian (val) {
            this.kejadianStatus = this.$parent.kejadianStatus = !this.kejadianStatus
        },
    },
    watch: {
        darkMode (val) {
            this.$parent.mapsOptions.styles = val ? this.$parent.darkStyle : this.$parent.lightStyle
            this.$parent.toggleTheme(val)
        },
        label (val) {
            val ? this.removeLabelMaps() : this.addLabelMaps()
            this.$parent.mapsOptions.styles = this.darkMode ? this.$parent.darkStyle : this.$parent.lightStyle
        },
        traffic (val) {
            val ? this.addTrafficMaps() : this.removeTrafficMaps()
        },
        topbar (val) {
            this.$parent.$refs.topbar.topClass = !val
        },
        rightbar (val) {
            this.$parent.$refs.rightbar.rightClass = !val
        },
        jenisPeta (val) {
            switch (val) {
                case 'default':
                    this.removeGeometryMaps ()
                    this.$parent.mapsOptions.mapTypeId = 'roadmap'
                    break;
                case 'terrain':
                    this.removeGeometryMaps ()
                    this.$parent.mapsOptions.mapTypeId = 'terrain'
                    break;
            
                default:
                    this.addGeometryMaps()
                    this.$parent.mapsOptions.mapTypeId = 'hybrid'
                    break;
            }
            
            this.$parent.mapsOptions.styles = this.darkMode ? this.$parent.darkStyle : this.$parent.lightStyle
        },
        hotspot(val) {
            this.isBusy = val
            this.$parent.$refs.rightbar.hotspot = val
        },
        kegiatanStatus (val) {
            var kegiatan = this.$parent.markerKegiatan;
            if(val) {
                kegiatan.length == 0 ? this.$parent.getMarkerKegiatan() : ''
            }   
        },
        pengaduanStatus (val) {
            var pengaduan = this.$parent.markerPengaduan;
            if(val) {
                pengaduan.length == 0 ? this.$parent.getMarkerPengaduan() : ''
            }
        },
        kejadianStatus (val) {
            var kejadian = this.$parent.markerKejadian;
            if(val) {
                kejadian.length == 0 ? this.$parent.getMarkerKejadian() : ''
            }
        },
        kegiatan :debounce(function (val, old) {
            this.$parent.getMarkerKegiatan()
        }, 700),
        pengaduan :debounce(function (val, old) {
            this.$parent.getMarkerPengaduan()
        }, 700),
        kejadian :debounce(function (val, old) {
            this.$parent.getMarkerKejadian()
        }, 700),
    }
}
</script>