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

            <div id="marker-single" v-if="markerSingleShow">
                <GmapMarker v-for="(indexMarkerSingle, keyMarkerSingle) in markerSingle" :key="`single-${keyMarkerSingle}`" 
                    :position="{ lat: parseFloat(indexMarkerSingle.data.lat), lng: parseFloat(indexMarkerSingle.data.lng) }"
                    :icon="loadMarkerSingle(indexMarkerSingle)" @click="detailMarkerSingle(indexMarkerSingle)"/>
            </div>

            <div id="marker-darurat" v-if="!markerSingleShow">
                <GmapMarker v-for="(indexMarkerDarurat, keyMarkerDarurat) in markerDarurat" :key="`darurat-${keyMarkerDarurat}`" 
                    :position="{ lat: parseFloat(indexMarkerDarurat.lat), lng: parseFloat(indexMarkerDarurat.lng) }"
                    :icon="require('@/assets/darurat.png').default" @click="$refs.darurat.detail(indexMarkerDarurat)"/>
            </div>

            <div id="marker-hotspot" v-if="!markerSingleShow">
                <GmapMarker v-for="(indexMarkerHotspot, keyMarkerHotspot) in markerHotspot" :key="`hotspot-${keyMarkerHotspot}`" 
                    :position="{ lat: parseFloat(indexMarkerHotspot.lat), lng: parseFloat(indexMarkerHotspot.lng) }"
                    :icon="loadMarkerHotspot(indexMarkerHotspot.tk)" @click="$refs.hotspot.detail(indexMarkerHotspot)"/>
            </div>

            <div id="marker-kegiatan" v-if="kegiatanStatus && !markerSingleShow">
                <GmapMarker v-for="(indexMarkerKegiatan, keyMarkerKegiatan) in markerKegiatan" :key="`kegiatan-${keyMarkerKegiatan}`" 
                    :position="{ lat: parseFloat(indexMarkerKegiatan.lat), lng: parseFloat(indexMarkerKegiatan.lng) }"
                    :icon="require('@/assets/kegiatan.png').default" @click="$refs.kegiatan.detail(indexMarkerKegiatan)"/>
            </div>
            
            <div id="marker-kejadian" v-if="kejadianStatus && !markerSingleShow">
                <GmapMarker v-for="(indexMarkerKejadian, keyMarkerKejadian) in markerKejadian" :key="`kegiatan-${keyMarkerKejadian}`" 
                    :position="{ lat: parseFloat(indexMarkerKejadian.lat), lng: parseFloat(indexMarkerKejadian.lng) }"
                    :icon="require('@/assets/kejadian.png').default" @click="$refs.kejadian.detail(indexMarkerKejadian)"/>
            </div>

            <div id="marker-pengaduan" v-if="pengaduanStatus && !markerSingleShow">
                <GmapMarker v-for="(indexMarkerPengaduan, keyMarkerPengaduan) in markerPengaduan" :key="`kegiatan-${keyMarkerPengaduan}`" 
                    :position="{ lat: parseFloat(indexMarkerPengaduan.lat), lng: parseFloat(indexMarkerPengaduan.lng) }"
                    :icon="require('@/assets/pengaduan.png').default" @click="$refs.pengaduan.detail(indexMarkerPengaduan)"/>
            </div>
            
            <div id="marker-tps" v-if="!markerSingleShow">
                <GmapMarker v-for="(indexMarkerTps, keyMarkerTps) in markerTps" :key="`hotspot-${keyMarkerTps}`" 
                    :position="{ lat: parseFloat(indexMarkerTps.lat), lng: parseFloat(indexMarkerTps.lng) }"
                    :icon="require('@/assets/tps.png').default" @click="$refs.tps.detail(indexMarkerTps)"/>
            </div>
            
            <div id="marker-personil" v-if="markerPersonilShow && !markerSingleShow">
                <GmapMarker v-for="(indexMarkerPersonil, keyMarkerPersonil) in markerPersonil" :key="`personil-${keyMarkerPersonil}`" 
                    :position="{ lat: parseFloat(indexMarkerPersonil.lat), lng: parseFloat(indexMarkerPersonil.lng) }"
                    :icon="indexMarkerPersonil.icon" @click="$refs.personil.detail(indexMarkerPersonil)"/>
            </div>

            <div id="marker-patroli" v-if="markerPersonilShow && !markerSingleShow">
                <GmapMarker v-for="(indexMarkerPatroli, keyMarkerPatroli) in markerPatroli" :key="`pengawalan-${keyMarkerPatroli}`" 
                    :position="{ lat: parseFloat(indexMarkerPatroli.lat), lng: parseFloat(indexMarkerPatroli.lng) }"
                    :icon="require('@/assets/patroli.png').default" @click="$refs.personil.detail(indexMarkerPatroli)"/>
            </div>
           
            <div id="marker-pengawalan" v-if="markerPersonilShow && !markerSingleShow">
                <GmapMarker v-for="(indexMarkerPengawalan, keyMarkerPengawalan) in markerPengawalan" :key="`pengawalan-${keyMarkerPengawalan}`" 
                    :position="{ lat: parseFloat(indexMarkerPengawalan.lat), lng: parseFloat(indexMarkerPengawalan.lng) }"
                    :icon="require('@/assets/pengawalan.png').default" @click="$refs.personil.detail(indexMarkerPengawalan)"/>
            </div>
            
            <div id="marker-lokasi-vital">
                <GmapMarker v-for="(indexMarkerLokasiVital, keyMarkerLokasiVital) in markerLokasiVital" :key="`lokasivital-${keyMarkerLokasiVital}`" 
                    :position="{ lat: parseFloat(indexMarkerLokasiVital.lat), lng: parseFloat(indexMarkerLokasiVital.lng) }"
                    :icon="indexMarkerLokasiVital.jenis.icon" @click="$refs.lokasiVital.detail(indexMarkerLokasiVital)"/>
            </div>
        </GmapMap>
        <RightBar ref="rightbar"/>
        <BottomBar ref="bottombar"/>

        <Darurat ref="darurat" />
        <Hotspot ref="hotspot" />
        <Kegiatan ref="kegiatan" />
        <Kejadian ref="kejadian" />
        <LokasiVital ref="lokasiVital" />
        <Pengaduan ref="pengaduan" />
        <Personil ref="personil" />
        <Tps ref="tps" />

        <audio :src="audioEmergency" ref="emergency" loop></audio>
	    <audio :src="audioKejadian" ref="audioKejadian" loop></audio>
        <audio :src="ringBackTone" ref="rbt" loop></audio>
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
import Vcall from '@/views/Monit/Vcall/Main'

import LaravelEcho from "laravel-echo"

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
            markerSingle: [],
            markerKegiatan: [],
            markerPengaduan: [],
            markerKejadian: [],
            markerDarurat: [],
            markerHotspot: [],
            markerPersonil: [],
            markerMasyarakat: [],
            logPatroliPengawalan: [],
            markerPatroli: [],
            markerPengawalan: [],
            markerLokasiVital: [],
            markerTps: [],
            markerPersonilShow: true,
            markerSingleShow: false,
            kegiatanStatus: true, //Belongs to leftbar
            pengaduanStatus: true, //Belongs to leftbar
            kejadianStatus: true, //Belongs to leftbar
            audioEmergency: audioEmergency,
	        audioKejadian: audioKejadian,
            ringBackTone: ringBackTone,
            socketDarurat: false,
            socketDaruratRadius: [],
        }
    },
    computed: {
        
    },
    methods : {
        toggleTheme (theme) {
            this.$parent.toggleTheme(theme)
        },
        loadMarkerHotspot (hotspot) {
            if(hotspot == 7) {
                return require('@/assets/hotspot-hijau.png').default
            } else if (hotspot == 8) {
                return require('@/assets/hotspot-orange.png').default
            } else {
                return require('@/assets/hotspot-merah.png').default
            }
        },
        detailMarkerSingle (item) {
            switch (item.type) {
                case 'kegiatan':
                    this.$refs.kegiatan.detail(item.data)
                    break;
                case 'pengaduan':
                    this.$refs.pengaduan.detail(item.data)
                    break;
                case 'kejadian':
                    this.$refs.kejadian.detail(item.data)
                    break;
                case 'personil':
                    this.$refs.personil.detail(item.data)
                    break;
                default:
                    this.$refs.darurat.detail(item.data)
                    break;
            }
        },
        loadMarkerSingle (item) {
            switch (item.type) {
                case 'kegiatan':
                    return require('@/assets/kegiatan.png').default
                    break;
                case 'pengaduan':
                    return require('@/assets/pengaduan.png').default
                    break;
                case 'kejadian':
                    return require('@/assets/kejadian.png').default
                    break;
                case 'personil':
                    return item.data.icon
                    break;
                default:
                    return require('@/assets/darurat.png').default
                    break;
            }
        },
        getDefaultMarker() {
            this.getMarkerKegiatan()
            this.getMarkerKejadian()
            this.getMarkerPengaduan()
        },
        getMarkerSingle(item) {
            this.markerSingle = [item]
            this.center = { lat: parseFloat(item.data.lat), lng: parseFloat(item.data.lng) }
            this.zoom = 16
            this.markerSingleShow = true
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
        resetDefaultMarker () {
            this.zoom = 15
            this.center = { lat: Number(defaultLat), lng: Number(defaultLng) }
            this.markerSingle = []
            this.markerSingleShow = false
        },
        triggerLogout () {
            this.$parent.triggerLogout()
        },
        initLaravelEcho() {
            var self = this

            Echo = new LaravelEcho({
                broadcaster: "socket.io",
                host: socketUrl,
                auth: { headers: { Authorization: localStorage.getItem('token') }}
            });

            Echo.private(socketPrefix + ':Monit')
                .listen('.CallReady', this.$refs.vcall.callReady)
                .listen('.darurat-baru', this.daruratBaru)
                .listen('.kegiatan-baru', this.kegiatanBaru)
                .listen('.pengaduan-baru', this.pengaduanBaru)
                .listen('.kegiatan-komentar', this.kegiatanKomentar)
                .listen('.personil-relokasi', this.relokasiPersonil)
                .listen('.masyarakat-relokasi', this.relokasiMasyarakat)
                .listen('.kejadian-baru', this.kejadianBaru)
                .listen('.personil-logout', this.personilLogout)
        },
        daruratBaru({ data }) {
            var self = this
            this.$toast.open({
                message: data.user.nama + ' mengirim darurat baru!',
                type: 'error',
                duration: 0,
                onDismiss: function() {
                    self.$refs.emergency.pause()
				    self.$refs.emergency.currentTime = 0
                },
            })
            if(this.$refs.vcall.call == false) { this.$refs.emergency.play() }
        },
        detailDaruratBaru() {
            this.markerSingleShow = true
            this.socketDarurat = true
            this.markerDarurat.push(data)
            this.$refs.maps.$mapPromise.then((map) => {
                var radius = new google.maps.Circle({
                    strokeColor: '#f44336',
                    strokeOpacity: 0.35,
                    strokeWeight: 1,
                    fillColor: '#f44336',
                    fillOpacity: 0.35,
                    map: map,
                    center: { lat: parseFloat(data.lat), lng: parseFloat(data.lng) },
                    radius: parseFloat(data.acc)
                })
                this.socketDaruratRadius.push(radius)
            })
            this.zoom = 20
            this.center = { lat: parseFloat(data.lat), lng: parseFloat(data.lng) }
        },
        kegiatanBaru(data){
            this.$toast.info(data.data.user.nama + ' menambah kegiatan baru', { layout: 'topRight' })
        },
        kegiatanKomentar(data){
            this.$toast.info(data.data.user.nama + ' mengomentari kegiatan ' + data.data.induk, { layout: 'topRight' })
        },
        pengaduanBaru(data){
            this.$toast.info(data.data.user.nama + ' menambah pengaduan baru', { layout: 'topRight' })
        },
        relokasiPersonil({data : {data}}){
            var self = this

            if (this.markerSingle.type == 'personil' && this.markerSingle.data.id == data.id) {
                this.markerSingle.data = data
            }

            this.$refs.maps.$mapPromise.then((map) => {
                if (self.$refs.leftbar.tracking === false)
                    return
                if (self.$refs.leftbar.tracking === true && self.$refs.leftbar.trackingType == 'dinas' && data.dinas.kegiatan == 'LEPAS DINAS') {
                    self.removeMarkerPersonil(data)
                    return
                }
                if (self.$refs.leftbar.tracking === true && self.$refs.leftbar.trackingType == 'lepas_dinas' && data.dinas.kegiatan != 'LEPAS DINAS') {
                    self.removeMarkerPersonil(data)
                    return
                }

                var personil = data
                var indexMarkerPersonil = self.markerPersonil.findIndex((o) => o.id === personil.id );

                personil.position = {
                    lat: personil.lat,
                    lng: personil.lng
                }

                if (personil.dinas.kegiatan === 'Patroli' || personil.dinas.kegiatan === 'Pengawalan') {
                    var angle = indexMarkerPersonil >= 0 ? google.maps.geometry.spherical.computeHeading(new google.maps.LatLng(self.markerPersonil[indexMarkerPersonil].lat, self.markerPersonil[indexMarkerPersonil].lng), new google.maps.LatLng(personil.position.lat, personil.position.lng)) : 0;
                    personil.style = {
                        'width' : "20px",
                        'transform' : "rotate("+ angle +"deg)",
                        'transform-origin' : "center"
                    }
                    var indexLogPatroliPengawalan = self.logPatroliPengawalan.findIndex((o) => o.id === personil.id)
                    if (indexLogPatroliPengawalan < 0 ){
                        var color = this.randomColor()
                        var polyline = new google.maps.Polyline({
                            path: [],
                            strokeColor: color,
                            strokeOpactity: 0.8,
                            strokeWidth: 2,
                            map: map
                        })
                        indexLogPatroliPengawalan = self.logPatroliPengawalan.length
                        self.$set(self.logPatroliPengawalan, indexLogPatroliPengawalan, {'id': personil.id, 'polyline': polyline})
                    }
                    var path = self.logPatroliPengawalan[indexLogPatroliPengawalan].polyline.getPath().getArray()
                    path.push({lat: parseFloat(personil.lat), lng: parseFloat(personil.lng)})
                    self.logPatroliPengawalan[indexLogPatroliPengawalan].polyline.setPath(path)
                } else {
                    personil.style = {
                        'width': "30px"
                    }
                    var indexLogPatroliPengawalan = self.logPatroliPengawalan.findIndex((o) => o.id === personil.id)
                    if (indexLogPatroliPengawalan >= 0){
                        self.logPatroliPengawalan[indexLogPatroliPengawalan].polyline.setMap(null)
                        self.logPatroliPengawalan.splice(indexLogPatroliPengawalan, 1)
                    }
                }
                if (indexMarkerPersonil < 0) {
                    self.$set(self.markerPersonil, self.markerPersonil.length, personil)
                } else {
                    self.$set(self.markerPersonil, indexMarkerPersonil, personil);
                }
            })
        },
        removeMarkerPersonil (data) {
            var indexPers  = this.markerPersonil.findIndex((o) => o.id === data.id)
            var indexPatwal  = this.logPatroliPengawalan.findIndex((o) => o.id === data.id)
            this.markerPersonil.splice(indexPers, 1)
            this.logPatroliPengawalan.splice(indexPatwal, 1)
        },
        relokasiMasyarakat ({data: { data } }) {
            var singleKejadian = this.markerSingle.findIndex((o) => o.tipe == 'kejadian')
            var singleDarurat = this.markerSingle.findIndex((o) => o.tipe == 'darurat')
            if (this.markerKejadian.length == 0 && this.markerDarurat.length == 0)
                return

            if(data.jenis_induk == 'kejadian') {
                if(data.induk.selesai != 0 || data.induk.follow_me == 0) { return }
                this.relokasiKejadian(data)
            } else {
                if(data.induk.selesai != 0) { return }
                this.relokasiDarurat(data)
            }
        },
        relokasiKejadian (data) {
            var masyarakat = data
            var indexKejadian = this.markerSingle.findIndex((o) => o.data.id == masyarakat.induk.id )
            var indexMasyarakat = this.markerMasyarakatKejadian.findIndex((o) => o.user.id == masyarakat.user.id )
            var indexKejadianMasyarakat = this.markerMasyarakatKejadian.findIndex((o) => o.induk.id == masyarakat.induk.id )

            masyarakat.position = {
                lat: parseFloat(masyarakat.lat),
                lng: parseFloat(masyarakat.lng)
            }

            if (indexKejadian != -1) {
                if(indexMasyarakat == -1 && indexKejadianMasyarakat == -1) {
                    this.$set(this.markerMasyarakatKejadian, this.markerMasyarakatKejadian.length, masyarakat)
                } else {
                    this.$set(this.markerMasyarakatKejadian, indexMasyarakat, masyarakat)
                }
            }
        },

        relokasiDarurat (data) {
            var masyarakat = data
            var indexDarurat = this.markerSingle.findIndex((o) => o.data.id == masyarakat.induk.id )
            var indexMasyarakat = this.markerMasyarakatDarurat.findIndex((o) => o.user.id == masyarakat.user.id )
            var indexDaruratMasyarakat = this.markerMasyarakatDarurat.findIndex((o) => o.induk.id == masyarakat.induk.id )

            masyarakat.position = {
                lat: parseFloat(masyarakat.lat),
                lng: parseFloat(masyarakat.lng)
            }

            if (indexDarurat != -1) {
                if(indexMasyarakat == -1 && indexDaruratMasyarakat == -1) {
                    this.$set(this.markerMasyarakatDarurat, this.markerMasyarakatDarurat.length, masyarakat)
                } else {
                    this.$set(this.markerMasyarakatDarurat, indexMasyarakat, masyarakat)
                }
            }
        },
        kejadianBaru ({data : {data}}) {
            if(data.id_darurat == null) {
                if(this.call == false) {
                    data.audio == 'kejadian' ? this.$refs.audioKejadian.play() : this.$refs.audioKejadianCustom.playAudio(data.audio)
                }
                this.$toast.open({
                    message: data.user.nama + ' mengirim darurat baru!',
                    type: 'error',
                    duration: 0,
                    onDismiss: function() {
                        self.$refs.emergency.pause()
                        this.$refs.audioKejadian.pause()
						this.$refs.audioKejadianCustom.stopAudio()
                        this.$refs.audioKejadian.currentTime = 0
                    },
                })
            }
            this.$refs.topbar.refreshData()
        },
        personilLogout ({data : {data}}) {
            var self = this
            var indexMarkerPersonil = this.markerPersonil.findIndex((o) => o.id === data.id );
            if(indexMarkerPersonil >= 0)
                this.markerPersonil.splice(indexMarkerPersonil, 1)

            var indexLogPatroliPengawalan = self.logPatroliPengawalan.findIndex((o) => o.id === data.id)
            if (indexLogPatroliPengawalan >= 0) {
                self.logPatroliPengawalan[indexLogPatroliPengawalan].polyline.setMap(null)
                self.logPatroliPengawalan.splice(indexLogPatroliPengawalan, 1)
            }
        },
    },
    mounted () {
        this.mapsOptions.styles = this.darkStyle
        this.getDefaultMarker()
    }
}
</script>