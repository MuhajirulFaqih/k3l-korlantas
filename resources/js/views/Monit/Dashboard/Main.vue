<template>
    <div >
        <LeftBar ref="leftbar"/>
        <TopBar ref="topbar"/>
        <GmapMap
			ref="maps"
		  	:center="center"
  			:zoom="zoom"
  			:options="mapsOptions"
  			class="e-map"
		>
            <div id="marker-darurat">
                <GmapMarker v-for="(indexMarkerDarurat, keyMarkerDarurat) in markerDarurat" :key="`darurat-${keyMarkerDarurat}`" 
                    :position="{ lat: parseFloat(indexMarkerDarurat.lat), lng: parseFloat(indexMarkerDarurat.lng) }"
                    :icon="require('@/assets/darurat.png').default" @click="$refs.darurat.detail(indexMarkerDarurat)"/>
            </div>

            <div id="marker-hotspot">
                <GmapMarker v-for="(indexMarkerHotspot, keyMarkerHotspot) in markerHotspot" :key="`hotspot-${keyMarkerHotspot}`" 
                    :position="{ lat: parseFloat(indexMarkerHotspot.lat), lng: parseFloat(indexMarkerHotspot.lng) }"
                    :icon="loadMarkerHotspot" @click="$refs.hotspot.detail(indexMarkerHotspot)"/>
            </div>

            <div id="marker-kegiatan" v-if="kegiatanStatus">
                <GmapMarker v-for="(indexMarkerKegiatan, keyMarkerKegiatan) in markerKegiatan" :key="`kegiatan-${keyMarkerKegiatan}`" 
                    :position="{ lat: parseFloat(indexMarkerKegiatan.lat), lng: parseFloat(indexMarkerKegiatan.lng) }"
                    :icon="require('@/assets/kegiatan.png').default" @click="$refs.kegiatan.detail(indexMarkerKegiatan, 'marker')"/>
            </div>
            
            <div id="marker-kejadian" v-if="kejadianStatus">
                <GmapMarker v-for="(indexMarkerKejadian, keyMarkerKejadian) in markerKejadian" :key="`kegiatan-${keyMarkerKejadian}`" 
                    :position="{ lat: parseFloat(indexMarkerKejadian.lat), lng: parseFloat(indexMarkerKejadian.lng) }"
                    :icon="require('@/assets/kejadian.png').default" @click="$refs.kejadian.detail(indexMarkerKejadian, 'marker')"/>
            </div>

            <div id="marker-pengaduan" v-if="pengaduanStatus">
                <GmapMarker v-for="(indexMarkerPengaduan, keyMarkerPengaduan) in markerPengaduan" :key="`kegiatan-${keyMarkerPengaduan}`" 
                    :position="{ lat: parseFloat(indexMarkerPengaduan.lat), lng: parseFloat(indexMarkerPengaduan.lng) }"
                    :icon="require('@/assets/pengaduan.png').default" @click="$refs.pengaduan.detail(indexMarkerPengaduan)"/>
            </div>
            
            <div id="marker-tps">
                <GmapMarker v-for="(indexMarkerTps, keyMarkerTps) in markerTps" :key="`hotspot-${keyMarkerTps}`" 
                    :position="{ lat: parseFloat(indexMarkerTps.lat), lng: parseFloat(indexMarkerTps.lng) }"
                    :icon="require('@/assets/tps.png').default" @click="$refs.tps.detail(indexMarkerTps)"/>
            </div>
        </GmapMap>
        <RightBar ref="rightbar"/>
        <BottomBar ref="topbar"/>

        <Darurat ref="darurat" />
        <Hotspot ref="hotspot" />
        <Kegiatan ref="kegiatan" />
        <Kejadian ref="kejadian" />
        <LokasiVital ref="lokasiVital" />
        <Pengaduan ref="pengaduan" />
        <Personil ref="personil" />
        <Tps ref="tps" />
    </div>
</template>

<script>

import BottomBar from '@/views/Monit/Components/BottomBar'
import LeftBar from '@/views/Monit/Components/LeftBar'
import RightBar from '@/views/Monit/Components/RightBar'
import TopBar from '@/views/Monit/Components/TopBar'

import Darurat from '@/views/Monit/Darurat/Main'
import Hotspot from '@/views/Monit/Hotspot/Main'
import Kegiatan from '@/views/Monit/Kegiatan/Main'
import Kejadian from '@/views/Monit/Kejadian/Main'
import LokasiVital from '@/views/Monit/LokasiVital/Main'
import Pengaduan from '@/views/Monit/Pengaduan/Main'
import Personil from '@/views/Monit/Personil/Main'
import Tps from '@/views/Monit/Tps/Main'

export default {
    name: 'dashboard',
    components: { 
        BottomBar, LeftBar, 
        RightBar, TopBar, 
        Darurat, Hotspot, Kegiatan, Kejadian, LokasiVital, Pengaduan, Personil, Tps 
    },
    data () {
        return {
            zoom: 15,
            center: { lat: Number(defaultLat), lng: Number(defaultLng) },
            lightStyle: this.$parent.lightStyle,
            darkStyle: this.$parent.darkStyle,
            mapsOptions: this.$parent.mapsOptions,
            markerKegiatan: [],
            markerPengaduan: [],
            markerKejadian: [],
            markerDarurat: [],
            markerHotspot: [],
            markerPersonil: [],
            markerMasyarakat: [],
            markerPatroli: [],
            markerPengawalan: [],
            markerLokasiVital: [],
            markerTps: [],
            markerSingleShow: false,
            kegiatanStatus: true, //Belongs to leftbar
            pengaduanStatus: true, //Belongs to leftbar
            kejadianStatus: true, //Belongs to leftbar
        }
    },
    computed: {
        
    },
    methods : {
        toggleTheme (theme) {
            this.$parent.toggleTheme(theme)
        },
        loadMarkerHotspot (hotspot) {
            if(hotspot <= 29) {
                return require('@/assets/hotspot-hijau.png').default
            } else if (hotspot <= 79) {
                return require('@/assets/hotspot-orange.png').default
            } else {
                return require('@/assets/hotspot-merah.png').default
            }
        },
        getDefaultMarker() {
            this.getMarkerKegiatan()
            this.getMarkerKejadian()
            this.getMarkerPengaduan()
        },
        getMarkerKegiatan() {
            axios.get("kegiatan/", { params: { limit: this.$refs.leftbar.kegiatan, sort: 'created_at:desc' } })
            .then(({ data: { data }}) => {
                this.markerKegiatan = data
            })
            .catch(error => {
                console.log(error)
            })
        },
        getMarkerKejadian () {
            axios.get("kejadian/", { params: { limit: this.$refs.leftbar.kejadian, sort: 'created_at:desc' } })
            .then(({ data: { data }}) => {
                this.markerKejadian = data
            })
            .catch(error => {
                console.log(error)
            })
        },
        getMarkerPengaduan () {
            axios.get("pengaduan/", { params: { limit: this.$refs.leftbar.pengaduan, sort: 'created_at:desc' } })
            .then(({ data: { data }}) => {
                this.markerPengaduan = data
            })
            .catch(error => {
                console.log(error)
            })
        },
        triggerLogout () {
            this.$parent.triggerLogout()
        }
    },
    mounted () {
        this.mapsOptions.styles = this.darkStyle
        this.getDefaultMarker()
    }
}
</script>