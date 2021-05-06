<template>
    <div :class="`rightbar-outline ${rightClass ? 'close' : ''}`">
        <ul :class="`rightbar ${hotspotClass('expand')}`">
            <!-- <button class="btn e-btn e-btn-primary rightbar-close" @click="toggleMenu">
                <ph-caret-right class="phospor"/>
            </button> -->
            <li class="rightbar-header">
                Kegiatan 
                <a href="javascript:void(0);" @click="$parent.triggerLogout()" v-b-tooltip.hover title="Logout"
                class="d-block float-right"><ph-sign-out class="phopsor"/></a>
            </li>
            <perfect-scrollbar class="h-100" @ps-y-reach-end="onScroll" ref="kegiatan">
                <li v-for="(v, i) in kegiatan" :key="`kegiatan-${i}`">
                    <div class="rightbar-widget cursor-pointer" @click="$parent.$refs.bottombar.$refs.kegiatan.$refs.detail.showModal(v)">
                        <b-img v-if="v.dokumentasi == null" src="/assets/sample-kegiatan.png" class="w-100" alt=""/>
                        <b-img v-else :src="v.dokumentasi" class="w-100" alt=""/>
                        <div>{{ v.user.nama }}</div>
                        <span>{{ v.w_kegiatan }}</span>
                        <p>{{ v.judul }}</p>
                    </div>
                </li>
                <li>
                    <center v-show="isBusy" class="mt-1">
                        <b-spinner variant="primary"></b-spinner>
                    </center>
                </li>
            </perfect-scrollbar>
        </ul>
        <div :class="`hotspot ${hotspotClass('active')}`">
            <b-row class="mt-3">
                <b-col cols="4">
                    <div class="e-alert e-alert-success w-100 text-center">{{ rendah }}</div>
                </b-col>
                <b-col cols="8">C(%) ≤ 29%</b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col cols="4">
                    <div class="e-alert e-alert-warning w-100 text-center">{{ sedang }}</div>
                </b-col>
                <b-col cols="8">30% ≤ C(%) ≤ 79%</b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col cols="4">
                    <div class="e-alert e-alert-danger w-100 text-center">{{ tinggi }}</div>
                </b-col>
                <b-col cols="8">C(%) ≥ 80%</b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
export default {
    name: 'right-bar',
    data () {
        return {
            rightClass: false,
            hotspot: false,
            totalRows: 0,
            perPage: 10,
            currentPage: 1,
            scrollStatus: false,
            isBusy: false,
            kegiatan: [],
            tinggi: '-',
            sedang: '-',
            rendah: '-',
        }
    },
    methods: {
        hotspotClass (val) {
            return this.hotspot ? val : ''
        },
        onScroll (event) {
            if(this.kegiatan.length !== 0 && this.isBusy !== true) {
                if(this.scrollStatus == false) {
                    this.scrollStatus = true
                    this.getKegiatan('pagination')
                } else {
                    this.scrollStatus = true
                }
            }
        },
        getKegiatan(from) {
            if(this.totalRows !== 0 && (this.totalRows == this.kegiatan.length)) {
                return
            }
            this.isBusy = true
            let payload = {
                page: from == 'master' ? this.currentPage : (this.currentPage + 1),
                sort: 'created_at:desc',
            }
            axios.get('kegiatan', { params: payload })
            .then(({ data: { data, meta: { pagination }}}) => {
                this.totalRows = pagination.total
                this.perPage = pagination.per_page
                this.currentPage = pagination.current_page
                if(from == 'master') { 
                    this.kegiatan = data 
                } else {
                    var self = this
                    data.forEach(function(v, i) {
                        self.kegiatan.push(v)
                    })
                }
                this.isBusy = false
                this.scrollStatus = false
            }).catch(error => {
                this.totalRows = 0
                console.log("error")
            })
        },
        kegiatanBaru(data) {
            var kegiatan = data
            if(!(this.totalRows !== 0 && (this.totalRows == this.kegiatan.length))) {
                this.kegiatan.pop()
            }
            this.totalRows++
            this.kegiatan.unshift(kegiatan)
        },
        getHotspot () {
            axios.get('titik-api')
            .then(({data}) => {
                this.tinggi = data.total.tinggi
                this.sedang = data.total.sedang
                this.rendah = data.total.rendah
                var self = this
                data.data.forEach(function(index) {
                    self.$parent.markerHotspot.push(index)
                })
                this.$parent.$refs.leftbar.isBusy = false
            })
        },
        resetHotspot () {
            this.rendah = '-'
            this.sedang = '-'
            this.tinggi = '-'
            this.$parent.markerHotspot = []
        },
        isReload(data) {
            var kegiatan = data
            if(!(this.totalRows !== 0 && (this.totalRows == this.kegiatan.length))) {
                this.kegiatan.pop()
            }
            this.totalRows++
            this.kegiatan.unshift(kegiatan)
        }
    },
    mounted() {
        this.getKegiatan('master')
    },
    watch: {
        hotspot (val) {
            val ? this.getHotspot() : this.resetHotspot()
        }
    }
}
</script>