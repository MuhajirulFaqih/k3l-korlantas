<template>
    <div :class="`topbar-outline ${topClass ? 'close' : ''}`">
        <div class="topbar">
            <div class="topbar-widget topbar-widget-purple">
                <div class="topbar-widget-icon">
                    <ph-git-fork class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Total Kejadian</div>
                    <span>189</span>
                </div>
            </div>
            <div class="topbar-widget topbar-widget-blue">
                <div class="topbar-widget-icon">
                    <ph-git-commit class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Belum Ditangani</div>
                    <span>50</span>
                </div>
            </div>
            <div class="topbar-widget topbar-widget-ocean">
                <div class="topbar-widget-icon">
                    <ph-git-diff class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Proses Penanganan</div>
                    <span>50</span>
                </div>
            </div>
            <div class="topbar-widget topbar-widget-aqua">
                <div class="topbar-widget-icon">
                    <ph-git-merge class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Selesai</div>
                    <span>89</span>
                </div>
            </div>
        </div>
        <!-- <div class="w-100 mt-3">
            <b-row>
                <b-col cols="12 d-flex justify-content-center align-items-center">
                    <button class="btn e-btn e-btn-primary topbar-btn" @click="showTopbar">
                        <ph-caret-circle-down class="phospor"/>
                    </button>
                </b-col>
            </b-row>
        </div> -->
    </div>
</template>

<script>
export default {
    name: 'top-bar',
    data () {
        return {
            topClass: false,
        }
    }
}
</script>