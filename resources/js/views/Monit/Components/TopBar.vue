<template>
    <div :class="`topbar-outline ${topClass ? 'close' : ''}`">
        <div class="topbar-reload" v-if="dataChange">
            <div class="e-reload-button" @click="refreshData">Refresh data top bar</div>
            <span class="e-reload-icon" @click="dataChange = false"><ph-x class="phospor"/></span>
        </div>
        <div class="topbar">
            <div class="topbar-widget topbar-widget-purple">
                <div class="topbar-widget-icon">
                    <ph-git-fork class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Total Kejadian</div>
                    <span>{{ semua }}</span>
                </div>
            </div>
            <div class="topbar-widget topbar-widget-blue">
                <div class="topbar-widget-icon">
                    <ph-git-commit class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Belum Ditangani</div>
                    <span>{{ belum }}</span>
                </div>
            </div>
            <div class="topbar-widget topbar-widget-ocean">
                <div class="topbar-widget-icon">
                    <ph-git-diff class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Penanganan</div>
                    <span>{{ prosesPenanganan }}</span>
                </div>
            </div>
            <div class="topbar-widget topbar-widget-aqua">
                <div class="topbar-widget-icon">
                    <ph-git-merge class="phospor"/>
                </div>
                <div class="topbar-body">
                    <div>Selesai</div>
                    <span>{{ selesai }}</span>
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
            semua: '-',
            belum: '-',
            selesai: '-',
            prosesPenanganan: '-',
            dataChange: false,
        }
    },
    methods: {
        refreshData() {
            axios.get('/kejadian/total')
            .then(({data}) => {
                this.semua = data[0].total
                this.prosesPenanganan = data[0].proses
                this.selesai = data[0].selesai
                this.belum = data[0].belum
                this.dataChange = false
            })
        },
        isReload () {
            this.dataChange = true
        }
    },
    mounted () {
        this.refreshData()
    }
}
</script>