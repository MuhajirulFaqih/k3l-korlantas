<template>
    <div>
        <b-modal ref="detail"
            hide-footer centered
            modal-class="e-modal e-modal-sm"
            title-tag="h4"
            @hide="hideModal"
            title="Detail Darurat">
            <div class="d-block" v-if="single !== null">
            <div v-if="!ubahKejadian">
                    <b-row class="mb-2">
                        <b-col cols="4">Nomor Telepon</b-col>
                        <b-col cols="8">{{ single.user.no_telp }}</b-col>
                    </b-row>
                    <b-row class="mb-2">
                        <b-col cols="4">Alamat</b-col>
                        <b-col cols="8">{{ single.user.alamat }}</b-col>
                    </b-row>
                    <b-row class="mb-2">
                        <b-col cols="4">Akurat sampai</b-col>
                        <b-col cols="8">{{ single.acc }} meter</b-col>
                    </b-row>
                    <b-row v-if="single.selesai == 1">
                        <b-col cols="4">Status</b-col>
                        <b-col cols="8"><h4><label class="badge badge-primary">Selesai</label></h4></b-col>
                    </b-row>
                    <b-row class="mb-2">
                        <b-col cols="12" colspan="2">
                            <center v-if="single.user.jenis_pemilik == 'personil' || single.user.jenis_pemilik == 'bhabin'">
                                <button class="btn e-btn e-btn-primary btn-lg mt-3" @click="$parent.$parent.$refs.personil.$refs.detail.videoCallById(single.user.id_personil)">
                                    <ph-video-camera class="phospor"/> Panggilan Video
                                </button>
                            </center>
                            <center>
                                <br/>
                                <b-button variant="success" @click="ubahStatusDarurat(single)" v-if="single.kejadian == null && single.selesai != 1">
                                    <ph-pencil class="phospor"/> Ubah menjadi kejadian
                                </b-button>
                                <b-button variant="primary" v-if="single.kejadian == null && single.selesai != 1" @click="daruratSelesai(single.id)">
                                    <ph-check class="phospor"/> Selesai
                                </b-button>
                            </center>
                        </b-col>
                    </b-row>
                </div>
                <b-row v-else>
                    <b-col cols="12">
                        <b-form @submit.prevent="simpanDaruratKejadian">
                            <b-form-group label="Kejadian">
                                <b-form-input class="e-form" type="text"
                                                required
                                                v-model="singleDarurat.kejadian">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group label="Lokasi">
                                <b-form-input class="e-form" type="text"
                                                required
                                                v-model="singleDarurat.lokasi">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group label="Keterangan">
                                <b-form-textarea class="e-form" placeholder="Ketikkan keterangan"
                                                    :rows="4"
                                                    v-model="singleDarurat.keterangan">
                                </b-form-textarea>
                            </b-form-group>
                        </b-form>
                        <b-alert show>Pilih kesatuan/personil terdekat yang akan di beri notifikasi</b-alert>
                        <center>
                            <b-form-radio-group v-model="jenis"
                                                :options="jenisOptions"
                                                name="radioInline"
                                                @change="pilihJenis">
                            </b-form-radio-group>
                        </center>
                        <br/>
                        <multiselect
                            v-show="jenis == 'kesatuan'"
                            v-model="id_kesatuan"
                            :options="kesatuan"
                            :show-labels="false"
                            value="value"
                            label="text"
                            placeholder="Pilih kesatuan..."
                            track-by="text"
                            :multiple="true"
                            >
                        </multiselect>
                        <b-list-group v-show="jenis == 'terdekat'" class="e-list">
                            <b-list-group-item v-for="(td, index) in single.nearby" :key="'terdekat-darurat-' + index" >
                                <b-row>
                                    <b-col cols="1">
                                        <div class="custom-control custom-checkbox pt-1 custom-control-inline">
                                            <input type="checkbox"
                                                class="custom-control-input"
                                                :id="'nearby-' + index" :value="td.personil.id"
                                                v-model="personilTerdekat">
                                            <label class="custom-control-label" :for="'nearby-' + index"></label>
                                        </div>
                                    </b-col>
                                    <b-col cols="8">
                                        <b-row>
                                            <b-col cols="1">{{ index + 1 }}</b-col>
                                            <b-col cols="11">
                                            {{ td.personil.pangkat }} {{ td.personil.nama }} | <b>{{ td.personil.jabatan }}</b><br/> <i>{{ td.personil.no_telp }}</i>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                    <b-col cols="3">{{ td.distance }} meter</b-col>
                                </b-row>
                            </b-list-group-item>
                            <b-list-group-item v-if="single.nearby.length == 0" class="mt-2 text-center">
                                <span>Tidak ditemukan data personil terdekat</span>
                            </b-list-group-item>
                        </b-list-group>
                        <br/>
                        <center>
                            <b-button variant="warning" @click="ubahStatusDarurat(single)">
                                <ph-caret-left class="phospor"/> Batal
                            </b-button>
                            <b-button :disabled="disableSimpanKejadian" variant="success" @click="simpanDaruratKejadian">
                                <ph-floppy-disk class="phospor"/> {{ teksSimpanKejadian }}
                            </b-button>
                        </center>
                    </b-col>
                </b-row>
            </div>
        </b-modal>
        <VideoCall ref="videoCall"/>
    </div>
</template>

<script>
import VideoCall from '@/views/Monit/Personil/VideoCall'
import { format, formatISO, parseISO } from 'date-fns'
import { debounce, flattenDepth, values } from 'lodash'
import id from 'date-fns/locale/id'
import moment from 'moment'
moment.locale('id')
export default {
    name: 'darurat',
    data () {
        return {
            single: null,
            ubahKejadian: false,
            singleDarurat: {
                kejadian: '',
                lokasi: '',
                keterangan: '',
                lat: '',
                lng: '',
                id_asal: '',
                id_darurat: '',
            },
            jenis: '',
            jenisOptions: [
                { text: 'Semua Personil', value: 'semua' },
                { text: 'Kesatuan', value: 'kesatuan' },
                { text: 'Personil terdekat', value: 'terdekat' }
            ],
            id_kesatuan: null,
            kesatuan: [],
            personilTerdekat: [],
        }
    },
    components: { VideoCall },
    computed: {
        disableSimpanKejadian () {
            	return this.prosesSimpanKejadian;
        },
        teksSimpanKejadian () {
            return this.prosesSimpanKejadian == true ? ' Proses data...' : 'Simpan';
        }
    },
    methods: {
        showModal (item) {
            this.single = item
            this.$refs.detail.show()
        },
        ubahStatusDarurat (data) {
            this.$parent.$parent.$parent.$refs.maps.$mapPromise.then((map) => {
                var a = new google.maps.LatLng(data.lat, data.lng)
                data.nearby.forEach(function(value, index) {
                    var b = new google.maps.LatLng(value.lat, value.lng)
                    value.distance = parseFloat(google.maps.geometry.spherical.computeDistanceBetween(a, b)).toFixed(2)
                })
            })
            this.ubahKejadian = !this.ubahKejadian
            this.jenis = ''
        },
        daruratSelesai (id) {
            axios.post(`darurat/${id}/selesai`)
            .then( response => {
                this.$parent.$parent.$parent.getMarkerKejadian()
                this.$toast.success('Data berhasil diupdate', { layout: 'topRight' })
                this.$parent.refreshTable()
                this.$refs.detail.hide()
            })
            // .catch(({ response: { status, data: { errors }}}) => {
            //     if (status === 422)
            //         this.$toast.error(flattenDepth(values(errors)).join('<br>'))
            // })
        },
        simpanDaruratKejadian () {
            this.singleDarurat.id_asal = this.single.user.id
            this.prosesSimpanKejadian = true
            var payload = this.singleDarurat
            if(this.jenis == '') {
                this.$toast.error('Lengkapi data yang dibutuhkan')
                this.prosesSimpanKejadian = false
                return
            }
            if(this.jenis == 'kesatuan' && this.id_kesatuan == null ) {
                this.$toast.error('Lengkapi data yang dibutuhkan')
                this.prosesSimpanKejadian = false
                return
            }
            var kesatuan = this.id_kesatuan != null ? this.id_kesatuan.map(function(key) { return key.value }) : null;
            payload.personil = this.personilTerdekat
            payload.lat = this.single.lat
            payload.lng = this.single.lng
            payload.id_darurat = this.single.id
            axios.post('kejadian', payload)
            .then( response => {
                this.prosesSimpanKejadian = false
                this.resetFormDaruratKejadian()
                this.$parent.refreshTable()
                this.$parent.$parent.$parent.getMarkerKejadian()
                this.ubahKejadian = false
                this.$parent.$parent.$parent.$refs.topbar.isReload()
                this.$toast.success('Data berhasil disimpan', { layout: 'topRight' })
                this.$refs.detail.hide()
            })
            .catch(({ response: { status, data: { errors }}}) => {
                this.prosesSimpanKejadian = false
                if (status === 422)
                    this.$toast.error(flattenDepth(values(errors)).join('<br>'))
            })
        },
        resetFormDaruratKejadian() {
            this.singleDarurat = {
                kejadian: '',
                lokasi: '',
                keterangan: '',
                lat: '',
                lng: '',
                id_asal: '',
                id_darurat: '',
            }
        },
        pilihJenis (jenis) {
            switch (jenis) {
                case 'kesatuan':
                    this.fetchKesatuan()
                    break
            }
        },
        fetchKesatuan() {
            var promise = axios.get('kesatuan/korlantas')
            .then(({data: {data}}) => {
                this.kesatuan = data.map((val) => {
                    return {value: val.id, text: val.kesatuan}
                })
            })
        },
        hideModal() {
            this.single = null
        },
    }
}
</script>