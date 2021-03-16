<template>
    <div class="bottombar-outline">
        <div class="bottombar">
            <a href="javascript:void(0);" @click="showKegiatan" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-shield-chevron class="phospor"/>
                </div>
                <span>Kegiatan</span>
            </a>
            <a href="javascript:void(0);" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-newspaper-clipping class="phospor"/>
                </div>
                <span>Pengaduan</span>
            </a>
            <a href="javascript:void(0);" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-activity class="phospor"/>
                </div>
                <span>Kejadian</span>
            </a>
            <a href="javascript:void(0);" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-pedestrian class="phospor"/>
                </div>
                <span>Personil</span>
            </a>
            <a href="javascript:void(0);" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-video-camera class="phospor"/>
                </div>
                <span>History VC</span>
            </a>
            <a href="javascript:void(0);" class="bottombar-menu">
                <div class="bottombar-icon">
                    <ph-shield-warning class="phospor"/>
                </div>
                <span>Darurat</span>
            </a>
        </div>
        <Kegiatan ref="kegiatan"/>
    </div>
</template>

<script>
import Kegiatan from "@/views/Monit/Kegiatan/Main"
export default {
    name: 'bottom-bar',
    components: { Kegiatan },
    data () {
        return {

        }
    },
    methods: {
        showKegiatan () {
            this.$refs.kegiatan.showModal()
        }
    }
}
</script>