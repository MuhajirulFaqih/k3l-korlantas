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
            <div id="marker-kegiatan">
                <GmapMarker v-for="(indexMarkerKegiatan, keyMarkerKegiatan) in markerKegiatan" :key="`kegiatan-${keyMarkerKegiatan}`" 
                    :position="{ lat: parseFloat(indexMarkerKegiatan.lat), lng: parseFloat(indexMarkerKegiatan.lng) }"
                    :icon="require('@/assets/kegiatan.png').default" @click="detailKegiatan(indexMarkerKegiatan)"/>
            </div>
            
            <div id="marker-pengaduan">
                <GmapMarker v-for="(indexMarkerPengaduan, keyMarkerPengaduan) in markerPengaduan" :key="`kegiatan-${keyMarkerPengaduan}`" 
                    :position="{ lat: parseFloat(indexMarkerPengaduan.lat), lng: parseFloat(indexMarkerPengaduan.lng) }"
                    :icon="require('@/assets/pengaduan.png').default" @click="detailPengaduan(indexMarkerPengaduan)"/>
            </div>

            <div id="marker-kejadian">
                <GmapMarker v-for="(indexMarkerKejadian, keyMarkerKejadian) in markerKejadian" :key="`kegiatan-${keyMarkerKejadian}`" 
                    :position="{ lat: parseFloat(indexMarkerKejadian.lat), lng: parseFloat(indexMarkerKejadian.lng) }"
                    :icon="require('@/assets/kejadian.png').default" @click="detailKejadian(indexMarkerKejadian)"/>
            </div>
            
            <div id="marker-darurat">
                <GmapMarker v-for="(indexMarkerDarurat, keyMarkerDarurat) in markerDarurat" :key="`darurat-${keyMarkerDarurat}`" 
                    :position="{ lat: parseFloat(indexMarkerDarurat.lat), lng: parseFloat(indexMarkerDarurat.lng) }"
                    :icon="require('@/assets/darurat.png').default" @click="detailKegiatan(indexMarkerDarurat)"/>
            </div>

            <div id="marker-hotspot">
                <GmapMarker v-for="(indexMarkerHotspot, keyMarkerHotspot) in markerHotspot" :key="`hotspot-${keyMarkerHotspot}`" 
                    :position="{ lat: parseFloat(indexMarkerHotspot.lat), lng: parseFloat(indexMarkerHotspot.lng) }"
                    :icon="loadMarkerHotspot" @click="detailDataHotspot(indexMarkerHotspot)"/>
            </div>
            
            <div id="marker-tps">
                <GmapMarker v-for="(indexMarkerTps, keyMarkerTps) in markerTps" :key="`hotspot-${keyMarkerTps}`" 
                    :position="{ lat: parseFloat(indexMarkerTps.lat), lng: parseFloat(indexMarkerTps.lng) }"
                    :icon="require('@/assets/tps.png').default" @click="detailDataHotspot(indexMarkerTps)"/>
            </div>
        </GmapMap>
        <RightBar ref="rightbar"/>
        <BottomBar ref="topbar"/>
    </div>
</template>

<script>

import LeftBar from '@/views/Monit/Components/LeftBar'
import RightBar from '@/views/Monit/Components/RightBar'
import TopBar from '@/views/Monit/Components/TopBar'
import BottomBar from '@/views/Monit/Components/BottomBar'
export default {
    name: 'dashboard',
    components: { LeftBar, RightBar, TopBar, BottomBar },
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
        detailKegiatan (kegiatan) {

        },
        detailPengaduan (pengaduan) {

        },
        detailKejadian (kejadian) {

        },
        detailDarurat (darurat) {

        },
        detailHotspot (hotspot) {

        },
        detailPersonil (hotspot) {

        },
        detailLokasiVital (lokasi) {

        },
        detailTps (lokasi) {

        },
    },
    mounted () {
        this.mapsOptions.styles = this.darkStyle
    }
}
</script>