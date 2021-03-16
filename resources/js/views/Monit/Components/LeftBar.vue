<template>
    <div class="leftbar-outline">
        <ul class="leftbar">
            <li class="leftbar-header">Peta</li>
            <li :class="openMenuClass('peta')">
                <div @click="toggleSub('peta')">
                    <ph-map-pin-line class="phospor" /> 
                    <span>Setting</span>
                </div>
                <ul class="leftbar-sub">
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
                </ul>
            </li>
            <li :class="openMenuClass('tracking')">
                <div @click="toggleSub('tracking')">
                    <ph-pedestrian class="phospor" /> 
                    <span>Tracking</span>
                </div>
                <ul class="leftbar-sub">
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
            </li>
            <li :class="openMenuClass('kegiatan')">
                <div @click="toggleSub('kegiatan')">
                    <ph-shield-chevron class="phospor" />
                    <span>Kegiatan</span>
                    <span class="circle circle-primary"></span>
                </div>
                <ul class="leftbar-sub">
                    <li class="text-right p-0 px-2">
                        <div @click="openMenu = ''"><ph-x class="phospor"/></div>
                    </li>
                    <li class="leftbar-sub-header">Kegiatan</li>
                    <li>
                        <button class="e-btn leftbar-sub-icon btn">
                            <ph-eye class="phospor" />
                        </button>
                        <span>Tampil</span>
                    </li>
                    <li>
                        Menampilkan
                        <vue-slider
                            class="e-slider e-slider-primary"
                            v-model="kegiatan"
                            v-bind="optionsSlider"
                        ></vue-slider>
                    </li>
                </ul>
            </li>
            <li :class="openMenuClass('pengaduan')">
                <div @click="toggleSub('pengaduan')">
                    <ph-newspaper-clipping class="phospor" />
                    <span>Pengaduan</span>
                    <span class="circle circle-success"></span>
                </div>
                <ul class="leftbar-sub">
                    <li class="text-right p-0 px-2">
                        <div @click="openMenu = ''"><ph-x class="phospor"/></div>
                    </li>
                    <li class="leftbar-sub-header">Pengaduan</li>
                    <li>
                        <button class="e-btn leftbar-sub-icon btn">
                            <ph-eye class="phospor" />
                        </button>
                        <span>Tampil</span>
                    </li>
                    <li>
                        Menampilkan
                        <vue-slider
                            class="e-slider e-slider-primary"
                            v-model="pengaduan"
                            v-bind="optionsSlider"
                        ></vue-slider>
                    </li>
                </ul>
            </li>
            <li :class="openMenuClass('kejadian')">
                <div @click="toggleSub('kejadian')">
                    <ph-activity class="phospor" />
                    <span>Kejadian</span>
                    <span class="circle circle-danger"></span>
                </div>
                <ul class="leftbar-sub">
                    <li class="text-right p-0 px-2">
                        <div @click="openMenu = ''"><ph-x class="phospor"/></div>
                    </li>
                    <li class="leftbar-sub-header">Kejadian</li>
                    <li>
                        <button class="e-btn leftbar-sub-icon btn">
                            <ph-eye class="phospor" />
                        </button>
                        <span>Tampil</span>
                    </li>
                    <li>
                        Menampilkan
                        <vue-slider
                            class="e-slider e-slider-primary"
                            v-model="kejadian"
                            v-bind="optionsSlider"
                        ></vue-slider>
                    </li>
                </ul>
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
                <ul class="leftbar-sub">
                    <li class="text-right p-0 px-2">
                        <div @click="openMenu = ''"><ph-x class="phospor"/></div>
                    </li>
                    <li class="leftbar-sub-header">Lokasi Vital</li>
                    <perfect-scrollbar class="h-100">
                        
                    </perfect-scrollbar>
                </ul>
            </li>
            <li :class="openMenuClass('area')">
                <div @click="toggleSub('area')">
                    <ph-globe-hemisphere-west class="phospor" />
                    <span>Area</span>
                </div>
            </li>
        </ul>
        <div class="leftbar-bottom">
            <div>
                <button class="e-btn e-btn-warning btn btn-block">Refresh Data</button>
            </div>
            <div>
                <b-row>
                    <b-col cols="6 pr-lg-0">
                        <button class="e-btn e-btn-primary btn btn-block">-</button>
                    </b-col>
                    <b-col cols="6 pl-lg-0">
                        <button class="e-btn e-btn-primary btn btn-block">+</button>
                    </b-col>
                </b-row>
            </div>
        </div>
    </div>
</template>

<script>
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/default.css'
export default {
    name: 'leftbar',
    components: { VueSlider },
    data () {
        return {
            openMenu: '',
            darkMode: true,
            label: false,
            traffic: false,
            jenisPeta: 'default',
            trafficLayer: null,
            kegiatan: 10,
            pengaduan: 10,
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
        addLabelMaps () {
            let mapsOptions = this.$parent.mapsOptions.styles
            let indexMapsOptions = mapsOptions.findIndex(v => v.elementType == 'labels' && v.featureType == 'poi')
            if(indexMapsOptions <= -1) {
                mapsOptions.push({
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                })
            }
        },
        removeLabelMaps () {
            let mapsOptions = this.$parent.mapsOptions.styles
            let indexMapsOptions = mapsOptions.findIndex(v => v.elementType == 'labels' && v.featureType == 'poi')
            mapsOptions.splice(indexMapsOptions, 1)
        },
        addGeometryMaps () {
            let mapsOptions = this.$parent.mapsOptions.styles
            let indexMapsOptions = mapsOptions.findIndex(v => v.elementType == 'road' && v.featureType == 'geometry' && v.stylers[0].visibility == 'off')
            if(indexMapsOptions <= -1) {
                mapsOptions.push({
                    featureType: "road",
                    elementType: "geometry",
                    stylers: [{ visibility: "off" }]
                })
            }
        },
        removeGeometryMaps () {
            let mapsOptions = this.$parent.mapsOptions.styles
            let indexMapsOptions = mapsOptions.findIndex(v => v.elementType == 'geometry' && v.featureType == 'road' && v.stylers[0].visibility == 'off')
            mapsOptions.splice(indexMapsOptions, 1)
        },
        addTrafficMaps () {
            let maps = this.$parent.$refs.maps
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
        settingJenisPeta (val) {
            this.jenisPeta = val
        },
        triggerHotspot () {
            this.hotspot = !this.hotspot
        }
    },
    watch: {
        darkMode (val) {
            this.$parent.mapsOptions.styles = val ? this.$parent.darkStyle : this.$parent.lightStyle
            this.label ? this.removeLabelMaps() : this.addLabelMaps()
            this.jenisPeta == 'satellite' ? this.addGeometryMaps() : this.removeGeometryMaps()
        },
        label (val) {
            val ? this.removeLabelMaps() : this.addLabelMaps()
        },
        traffic (val) {
            val ? this.addTrafficMaps() : this.removeTrafficMaps()
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
        },
        hotspot(val) {
            this.$parent.$refs.rightbar.hotspot = val
        }
    }
}
</script>