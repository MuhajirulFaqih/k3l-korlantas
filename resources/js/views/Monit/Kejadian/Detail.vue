<template>
    <b-modal ref="detail"
            hide-footer centered
            modal-class="e-modal e-modal-xl"
            title-tag="h4"
            :no-close-on-backdrop="isBusy"
            :no-close-on-esc="isBusy"
            :hide-header-close="isBusy"
            @hide="hideModal"
            title="Detail Kejadian">
	  	<div class="d-block" v-if="single !== null">
            <b-row>
                <b-col cols="12 py-2">
                    <h4>{{ single.kejadian }}</h4>
                    <hr/>
                </b-col>
                <b-col cols="12" v-if="formTindakLanjut == true && personilKejadian == false">
                    <b-row>
                        <b-col cols="6">
                            <perfect-scrollbar class="e-data-body">
                                <b-row v-if="single.gambar !== null">
                                    <b-col cols="12">
                                        <b-img :src="single.gambar" alt="" class="w-100"/>
                                    </b-col>
                                </b-row>
                                <b-row v-if="single.keterangan != null">
                                    <b-col cols="12">
                                        <p v-if="single.keterangan !== ''">{{ single.keterangan }}</p>
                                    </b-col>
                                </b-row>
                            </perfect-scrollbar>
                        </b-col>
                        <b-col cols="6">
                            <div class="e-comment" v-if="single.tindak_lanjut.length !== 0">
                                <perfect-scrollbar class="e-comment-body e-comment-body-full">
                                    <ul class="timeline">
                                        <li v-for="(t, i) in single.tindak_lanjut" :key="`tindaklanjut-${i}`">
                                            <span><b>{{ t.status_readable }}</b></span>
                                            <small class="float-right">{{ dateFormat(t.created_at) }}</small>
                                            <p>
                                                <center v-if="t.foto != ''">
                                                    <b-img :src="t.foto" class="mb-1 mt-1 w-100"/>
                                                </center>
                                                {{ t.keterangan }} <br/>
                                                <small><b>Penindak lanjut : </b>{{ t.user.nama }}</small> <br/>
                                                <small><b>Jabatan : </b>{{ t.user.jabatan }}</small> <br/>
                                                <small><b>Telepon : </b>{{ t.user.no_telp }}</small> <br/>
                                            </p>
                                        </li>
                                    </ul>
                                </perfect-scrollbar>
                            </div>
                            <div class="e-comment p-3" v-else>
                                <center>Belum ada tindak lanjut</center>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="12" v-else-if="formTindakLanjut == false">
                    <b-form @submit.prevent="kirimTindakLanjut">
                        <b-form-group
                            horizontal
                            :label-cols="2"
                            breakpoint="md"
                            label="Status">
                                <b-form-radio-group v-model="singleTindakLanjut.status" :options="statusOptions" class="mt-2">
                                </b-form-radio-group>
                        </b-form-group>
                        <b-form-group
                            horizontal
                            :label-cols="2"
                            breakpoint="md"
                            label="Keterangan">
                                <b-form-textarea v-model="singleTindakLanjut.keterangan" :rows="3" :max-rows="6" class="e-form">
                                </b-form-textarea>
                        </b-form-group>
                        <b-form-group
                            horizontal
                            :label-cols="2"
                            breakpoint="md"
                            label="Foto">
                                <b-form-file id="foto" class="e-form" placeholder="Pilih gambar..." accept="image/*"></b-form-file>
                        </b-form-group>
                    </b-form>
				</b-col>
                <b-col cols="12" v-else>
                    <b-alert variant="info mb-3" show>Pilih kesatuan/personil terdekat yang akan di beri notifikasi</b-alert>
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
				    	<b-list-group-item v-for="(t, i) in single.nearby" :key="`terdekat-${i}`" >
				    		<b-row>
								<b-col cols="1">
									<div class="custom-control custom-checkbox custom-control-inline pt-1">
										<input type="checkbox"
							                class="custom-control-input"
							               	:id="`nearby-${i}`" :value="t.personil.id"
							                v-model="personilTerdekat">
						            	<label class="custom-control-label" :for="`nearby-${i}`"></label>
						            </div>
								</b-col>
								<b-col cols="8">
									<b-row>
										<b-col cols="1">{{ i + 1 + '.' }}</b-col>
										<b-col cols="11">
                                            {{ t.personil.pangkat }} {{ t.personil.nama }} | <b>{{ t.personil.jabatan }}</b><br/> 
                                            <i>{{ t.personil.no_telp }}</i>
										</b-col>
									</b-row>
								</b-col>
								<b-col cols="3">{{ t.distance }} meter</b-col>
							</b-row>
				    	</b-list-group-item>
				    </b-list-group>
                </b-col>
            </b-row>
	    </div>
        <div v-if="single !== null" class="w-100 mt-4">
            <hr/>
            <b-row>
                <b-col cols="7">
                    <b-row>
                        <b-col cols="1"> <ph-user class="phospor"/> </b-col>
                        <b-col cols="11">
                            <span v-if="single.user.jenis_pemilik !== 'masyarakat'" v-b-tooltip class="cursor-default"
                                :title="'[ ' + single.user.nrp + ' ]\n' + single.user.nama + '\n' + single.user.jabatan">
                                {{ single.user.nama  }}
                            </span>
                            <span v-else >{{ single.user.nama  }}</span>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="5">
                    <b-row>
                        <b-col cols="1"> <ph-phone class="phospor"/> </b-col>
                        <b-col cols="10">
                            <span>{{ single.user.no_telp  }}</span>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
            <b-row>
                <b-col cols="7">
                    <b-row>
                        <b-col cols="1"> <ph-clock class="phospor"/> </b-col>
                        <b-col cols="11">
                            <span>{{ humanizeFormat(single.w_kejadian) }}</span>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="5">
                    <b-row>
                            <b-col cols="1"> <ph-map-pin class="phospor"/> </b-col>
                            <b-col cols="10">
                                <span>{{ single.lokasi }}</span>
                            </b-col>
                    </b-row>
                </b-col>
            </b-row>
            <div v-if="single.user.jenis_pemilik == 'personil' || single.user.jenis_pemilik == 'bhabin'">
                <hr/>
                <center>
                    <button circle class="btn e-btn e-btn-primary">
                        <ph-video-camera class="phospor"/>
                    </button>
                </center>
            </div>
            <hr/>
            <b-row class="float-right pr-3">
                <button v-if="formTindakLanjut && single.verifikasi != 1 && personilKejadian == false"
                    type="button" class="btn e-btn e-btn-primary mx-1"
                    @click="pilihPersonil(single)"><ph-caret-circle-right class="phospor"/> Verifikasi
                </button>
                <button v-if="formTindakLanjut && personilKejadian == false && single.selesai == 0 && single.verifikasi == 1"
                    type="button"
                    class="btn e-btn e-btn-success mx-1"
                    @click="tambahTindakLanjut(single.id)"><ph-arrows-left-right class="phospor"/> Tambah tindak lanjut</button>
                <button v-if="!formTindakLanjut"
                        type="button"
                        class="btn e-btn e-btn-warning"
                        @click="tambahTindakLanjut"><ph-caret-circle-left class="phospor"/> Batal</button>
                <button v-if="!formTindakLanjut"
                        class="btn e-btn e-btn-success mx-1"
                        @click="kirimTindakLanjut">
                        <ph-paper-plane-tilt class="phospor"/> Simpan</button>
                <button v-if="personilKejadian"
                        type="button"
                        class="btn e-btn e-btn-warning mx-1"
                        :disabled="disabledVerifikasi"
                        @click="hidePilihPersonil"><ph-caret-circle-left class="phospor"/> Batal</button>
                <button v-if="personilKejadian"
                        class="btn e-btn e-btn-primary mx-1"
                        :disabled="disabledVerifikasi"
                        @click="verifikasi(single.id)"><ph-check class="phospor"/> {{ verifikasiTeks }}</button>
            </b-row>
        </div>
    </b-modal>
</template>

<script>
import { format, formatISO, parseISO } from 'date-fns'
import { debounce, flattenDepth, values } from 'lodash'
import id from 'date-fns/locale/id'
import moment from 'moment'
moment.locale('id')
export default {
    name: 'detail-kejadian',
    data () {
        return {
            formTindakLanjut: true,
            personilKejadian: false,
            prosesVerifikasi: false,
            isBusy: false,
            isBusyCreate: false,
            single: null,
            jenis: '',
            jenisOptions: [
                { text: 'Semua Personil', value: 'semua' },
                { text: 'Kesatuan', value: 'kesatuan' },
                { text: 'Personil terdekat', value: 'terdekat' }
            ],
            personilTerdekat: [],
            singleTindakLanjut: {
                id_kejadian: '',
                status: '',
                keterangan: '',
                foto: ''
            },
            statusOptions: [
                { text: 'Proses penanganan', value: 'proses_penanganan' },
                { text: 'Selesai', value: 'selesai' }
            ],
            id_kesatuan: null,
			kesatuan: [],
        }
    },
    computed: {
        disabledVerifikasi () {
            return this.prosesVerifikasi;
        },
        verifikasiTeks () {
            return this.prosesVerifikasi == true ? ' Proses data...' : 'Verifikasi';
        }
    },
    methods : {
        showModal (item) {
            this.formTindakLanjut = true
            this.personilKejadian = false
            this.fetchKejadian(item.id)
            this.$refs.detail.show()
        },
        dateFormat(item) {
            let value = new Date(item)
            return format(parseISO(formatISO(value, { representation: 'complete' })), 'd MMMM yyyy HH:mm:ss', {locale: id}) 
        },
        humanizeFormat (value) {
            return moment(value).fromNow()
        },
        fetchKejadian (id) {
            this.isBusy = true
            axios.get('kejadian/' + id)
            .then(({ data }) => {
                this.single = data.data
                this.singleTindakLanjut.id_kejadian = data.data.id
                this.personilTerdekat = data.data.nearby.map(function(key){
                    return key.id
                })
                this.isBusy = false
            })
        },
        tambahTindakLanjut (id) {
            this.formTindakLanjut = !this.formTindakLanjut
            this.singleTindakLanjut = {
                id_kejadian: id,
                status: '',
                keterangan: '',
                foto: '',
            }
        },
        kirimTindakLanjut () {
            let data = new FormData()
            data.append('foto', document.getElementById('foto').files[0])

            let obj = this.singleTindakLanjut

            Object.keys(obj).forEach(function(key) {
                data.append(key, obj[key]);
            })

            axios.post(`kejadian/${this.single.id}/tindaklanjut`, data, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            .then(({data}) => {
                if(this.singleTindakLanjut.status == 'selesai') {
                    this.single.selesai = 1
                }
                this.single.tindak_lanjut.push(data.original.data)
                this.$toast.success('Data tindak lanjut berhasil di tambahkan')
                this.formTindakLanjut = true
                this.singleTindakLanjut.status = ''
                this.singleTindakLanjut.keterangan = ''
                this.singleTindakLanjut.foto = ''
            })
            .catch(({ response: { status, data: { errors }}}) => {
                if (status === 422)
                    this.$toast.error(flattenDepth(values(errors)).join('<br>'))
            })

        },
        verifikasi (id) {
            if(this.jenis == '') {
                this.$toast.error('Lengkapi data yang dibutuhkan')
                return
            }
            if(this.jenis == 'kesatuan' && this.id_kesatuan == null ) {
                this.$toast.error('Lengkapi data yang dibutuhkan')
                return
            }
            this.prosesVerifikasi = true
            var kesatuan = this.id_kesatuan != null ? this.id_kesatuan.map(function(key) { return key.value }) : null;
            var payload = { id : id, jenis: this.jenis, kesatuan: kesatuan, personil: this.personilTerdekat }
            axios.post('kejadian/broadcast', payload)
            .then((response) => {
                this.single.verifikasi = 1
                this.prosesVerifikasi = false
                this.personilKejadian = false
                this.$toast.success('Data berhasil di verifikasi')
            })
        },
        pilihPersonil (data) {
            var maps = this.$parent.$parent.$refs.maps.$mapPromise
            maps.then((map) => {
                var a = new google.maps.LatLng(data.lat, data.lng)
                data.nearby.forEach(function(value, index) {
                    var b = new google.maps.LatLng(value.lat, value.lng)
                    value.distance = parseFloat(google.maps.geometry.spherical.computeDistanceBetween(a, b)).toFixed(2)
                })
            })
            this.personilKejadian = true
            this.jenis = ''
        },
        pilihJenis (jenis) {
            if(jenis == 'kesatuan') {
                this.id_kesatuan = null
                this.fetchKesatuan()
            }
        },
        fetchKesatuan() {
            var promise = axios.get('kesatuan')
            .then(({data: {data}}) => {
                this.kesatuan = data.map((val) => {
                    return {value: val.id, text: val.kesatuan}
                })
            })
        },
        hidePilihPersonil () { this.personilKejadian = false },
        hideModal() {
            this.single = null
        },
    },
}
</script>