<template>
    <div :class="['bottombar-outline', markerSingleClass]">
        <div class="bottombar">
            <a href="javascript:void(0);" @click="showKegiatan" class="bottombar-menu">
                <span class="bottombar-menu-info" v-if="kegiatan != 0">{{ kegiatan }}</span>
                <div class="bottombar-icon">
                    <ph-shield-chevron class="phospor"/>
                </div>
                <span>Kegiatan</span>
            </a>
            <!-- <a href="javascript:void(0);"  @click="showPengaduan" class="bottombar-menu">
                <span class="bottombar-menu-info" v-if="pengaduan != 0">{{ pengaduan }}</span>
                <div class="bottombar-icon">
                    <ph-newspaper-clipping class="phospor"/>
                </div>
                <span>Pengaduan</span>
            </a> -->
            <a href="javascript:void(0);" @click="showKejadian" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-activity class="phospor"/>
                </div>
                <span>Kejadian</span>
            </a>
            <a href="javascript:void(0);" @click="showPersonil" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-pedestrian class="phospor"/>
                </div>
                <span>Personil</span>
            </a>
            <a href="javascript:void(0);" @click="showHistory" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-video-camera class="phospor"/>
                </div>
                <span>History VC</span>
            </a>
            <a href="javascript:void(0);" @click="showDarurat" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-shield-warning class="phospor"/>
                </div>
                <span>Darurat</span>
            </a>
        </div>
        <Kegiatan ref="kegiatan"/>
        <!-- <Pengaduan ref="pengaduan"/> -->
        <Kejadian ref="kejadian"/>
        <History ref="history"/>
        <Personil ref="personil"/>
        <Darurat ref="darurat"/>
    </div>
</template>

<script>
import Kegiatan from "@/views/Monit/Kegiatan/Main"
// import Pengaduan from "@/views/Monit/Pengaduan/Main"
import Kejadian from "@/views/Monit/Kejadian/Main"
import History from "@/views/Monit/HistoryVC/Main"
import Personil from "@/views/Monit/Personil/Main"
import Darurat from "@/views/Monit/Darurat/Main"
export default {
    name: 'bottom-bar',
    components: { 
            Kegiatan, 
            // Pengaduan, 
            Kejadian, History, Personil, Darurat 
    },
    data () {
        return {
            kegiatan: 0,
            // pengaduan: 0
        }
    },
    computed: {
        markerSingleClass () {
            return this.$parent.markerSingleShow || this.$parent.polyPatroli != null || this.$parent.polyPengawalan != null ? 'offset' : ''
        }
    },
    methods: {
        showKegiatan () {
            this.kegiatan = 0
            this.$refs.kegiatan.showModal()
        },
        // showPengaduan () {
        //     this.pengaduan = 0
        //     this.$refs.pengaduan.showModal()
        // },
        showKejadian () {
            this.$refs.kejadian.showModal()
        },
        showHistory () {
            this.$refs.history.showModal()
        },
        showPersonil () {
            this.$refs.personil.showModal()
        },
        showDarurat () {
            this.$refs.darurat.showModal()
        },
    }
}
</script>