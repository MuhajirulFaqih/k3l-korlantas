<template>
    <div>
        <b-modal ref="detail"
                 hide-footer centered
                 modal-class="e-modal e-modal-lg"
                 :no-close-on-backdrop="isBusy"
                 :no-close-on-esc="isBusy"
                 :hide-header-close="isBusy"
                 title-tag="h4"
                 @hide="hideModal"
                 title="Detail Personil">
            <div class="d-block" v-if="single !== null">
                <b-row>
                    <b-col cols="4">
                        <center v-if="single.foto != ''">
                            <b-img :src="single.foto" fluid class="m-1"/>
                        </center>
                    </b-col>
                    <b-col cols="8" class="pt-4">
                        <perfect-scrollbar class="e-data-body">
                            <b-row class="my-2">
                                <b-col cols="4" class="font-weight-bold">NRP</b-col>
                                <b-col cols="8">{{ single.nrp }}</b-col>
                            </b-row>
                            <b-row class="my-2">
                                <b-col cols="4" class="font-weight-bold">Nama</b-col>
                                <b-col cols="8">{{ single.pangkat }} {{ single.nama }}</b-col>
                            </b-row>
                            <b-row class="my-2">
                                <b-col cols="4" class="font-weight-bold">Jabatan</b-col>
                                <b-col cols="8">{{ single.jabatan }}</b-col>
                            </b-row>
                            <b-row class="my-2">
                                <b-col cols="4" class="font-weight-bold">Kesatuan</b-col>
                                <b-col cols="8">{{ single.kesatuan }}</b-col>
                            </b-row>
                            <b-row class="my-2">
                                <b-col cols="4" class="font-weight-bold">Alamat</b-col>
                                <b-col cols="8">{{ single.alamat }}</b-col>
                            </b-row>
                            <b-row class="my-2">
                                <b-col cols="4" class="font-weight-bold">Status</b-col>
                                <b-col cols="8">{{ single.dinas.kegiatan }}</b-col>
                            </b-row>
                            <b-row class="my-2">
                                <b-col cols="4" class="font-weight-bold">Terakhir di update</b-col>
                                <b-col cols="8">{{ humanizeFormat(single.terakhir_diupdate) }}</b-col>
                            </b-row>
                        </perfect-scrollbar>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col cols="12 text-right">
                        <button class="btn e-btn e-btn-primary btn-lg" @click="videoCall(single)">
                            <ph-video-camera class="phospor"/>
                            Panggilan Video
                        </button>
                    </b-col>
                </b-row>
            </div>
        </b-modal>
        <VideoCall ref="videoCall"/>
    </div>
</template>

<script>
    import VideoCall from '@/views/Monit/Personil/VideoCall'
    import {format, formatISO, parseISO} from 'date-fns'
    import {debounce, flattenDepth, values} from 'lodash'
    import id from 'date-fns/locale/id'
    import moment from 'moment'

    moment.locale('id')
    export default {
        name: 'detail-personil',
        components: {VideoCall},
        data() {
            return {
                isBusy: false,
                single: null,
            }
        },
        methods: {
            showModal(item) {
                this.single = item
                this.$refs.detail.show()
            },
            humanizeFormat(value) {
                return moment(value).fromNow()
            },
            incomingCallById(id_personil) {
                console.log(id_personil)
                this.isBusy = true
                axios.get('personil/' + id_personil)
                    .then(({data: {data}}) => {
                        this.isBusy = false
                        this.incomingCall(data)
                    })
            },
            incomingCall(single) {
                setTimeout(() => {
                    this.showModal(single)
                    this.$refs.videoCall.incomingCall(single)
                })
            },
            videoCallById(id_personil) {
                this.isBusy = true
                axios.get('personil/' + id_personil)
                    .then(({data: {data}}) => {
                        this.isBusy = false
                        this.videoCall(data)
                    })
            },
            videoCall(single) {
                var self = this
                setTimeout(function () {
                    self.$refs.videoCall.showModal(single)
                }, 500)
            },
            hideModal() {
                this.single = null
            },
        },
    }
</script>
