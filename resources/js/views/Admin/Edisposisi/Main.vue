<template>
    <div>
        <b-row>
            <b-col cols="2" md="2">
                <b-row>
                    <b-col cols="8"><h4>e-Disposisi</h4></b-col>
                    <b-col cols="4">
                        <b-button @click="$refs.modalTambahSurat.show()" variant="primary" size="sm">
                            <icon name="plus"/>
                        </b-button>
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="6" md="6">
                <b-pagination
                        align="right"
                        :total-rows="totalRows"
                        v-model="currentPage"
                        :per-page="perPage" />
            </b-col>
            <b-col cols="4" md="4">
                <b-form-input
                        align="right"
                        v-model="filterDebounced"
                        placeholder="Cari No Agenda, Nomor Surat, Perihal, Pengirim..."/>
            </b-col>
        </b-row>

        <div class="position-relative mt-2">
            <b-table responsive
                     ref="tabelSurat"
                     :busy.sync="isBusy"
                     :fields="tableColumns"
                     :items="providerSurat"
                     :current-page="currentPage"
                     :per-page="perPage"
                     :filter="filter"
                     :sort-by.sync="sortBy"
                     :sort-desc.sync="sortDesc">

                <template slot="index" slot-scope="data">
                    {{ ((currentPage - 1) * 10) + data.index + 1 }}
                </template>
                <template slot="aksi" slot-scope="row">
                    <b-button size="md" :title="'Edit data ' + row.item.nama" variant="info" @click="prepareUbah(row.item)">
                        <icon name="edit"/>
                    </b-button>
                    <b-button size="md" variant="success" @click="prepareDisposisi(row.item)">
                        <icon name="paper-plane"/>
                    </b-button>
                    <b-button size="md" variant="danger" @click="prepareDelete(row.item)">
                        <icon name="trash"/>
                    </b-button>
                </template>
            </b-table>

            <div class="loading" v-show="isBusy">
                <b-spinner variant="primary"></b-spinner>
            </div>

            <b-modal
                    hide-header-close
                    ok-only
                    ok-title="Oke"
                    @hide="resetFormTambahDisposisi"
                    ref="modalTambahDisposisi"
                    size="xl">
                <template slot="modal-header">
                    <h3 v-html="judulDisposisi"></h3>
                </template>
                <template>
                    <b-row>
                        <b-col cols="6">
                            <h4>Detail Surat</h4>
                            <table class="table table-stripped">
                                <tr>
                                    <td width="30%">No Surat</td>
                                    <td width="10px">:</td>
                                    <td>{{ payloadDisposisi.nomor }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Surat</td>
                                    <td>:</td>
                                    <td>{{ payloadDisposisi.tanggal }}</td>
                                </tr>
                                <tr>
                                    <td>Jenis Asal Surat</td>
                                    <td>:</td>
                                    <td>{{ payloadDisposisi.asal }}</td>
                                </tr>
                                <tr>
                                    <td>Waktu Diterima</td>
                                    <td>:</td>
                                    <td>{{ payloadDisposisi.waktu_diterima }}</td>
                                </tr>
                                <tr>
                                    <td>Pengirim</td>
                                    <td>:</td>
                                    <td>{{ payloadDisposisi.pengirim }}</td>
                                </tr>
                                <tr>
                                    <td>Perihal</td>
                                    <td>:</td>
                                    <td>{{ payloadDisposisi.perihal }}</td>
                                </tr>
                                <tr>
                                    <td>Derajat</td>
                                    <td>:</td>
                                    <td>{{ payloadDisposisi.derajat }}</td>
                                </tr>
                                <tr>
                                    <td>Klasifikasi</td>
                                    <td>:</td>
                                    <td>{{ payloadDisposisi.klasifikasi }}</td>
                                </tr>
                                <tr>
                                    <td>File</td>
                                    <td>:</td>
                                    <td><a :href="payloadDisposisi.fileSurat" target="_blank">Lihat</a></td>
                                </tr>
                            </table>
                        </b-col>
                        <b-col cols="6">
                            <h4>Detail Disposisi</h4>
                            <ul>
                                <li v-for="disposisi in payloadDisposisi.disposisi">
                                    <span><b>Tujuan</b> : {{ disposisi.jabatan.map((jabatan) => { return jabatan.jabatan}).join(',') }}</span><br>
                                    <span><b>Waktu</b> : {{ disposisi.w_disposisi }}</span><br/>
                                    <span><b>Isi</b> : {{ disposisi.isi }}</span><br/>
                                    <span><b>File</b> <a :href="disposisi.file" target="_blank">lihat</a></span><br/>
                                    <b-button @click="prepareHapusDisposisi(disposisi)">Hapus</b-button>
                                </li>
                                <li v-if="payloadDisposisi.disposisi.length == 0">Belum ada disposisi</li>
                            </ul>
                            <div v-if="payloadDisposisi.disposisi.length == 0">
                                <b-form-group label="Pilih Jabatan">
                                    <multi-select :options="jabatan"
                                                  :selected-options="selectedJabatan"
                                                  placeholder="Pilih Jabatan"
                                                  @select="onJabatanSelect"/>
                                </b-form-group>
                                <b-form-group label="Isi Disposisi">
                                    <b-form-textarea v-model="payloadDisposisi.isi" placeholder="Isi dispisisi"/>
                                </b-form-group>
                                <b-form-group label="File">
                                    <b-form-file @change="onFotoDisposisiChange" accept="image/*,application/pdf"/>
                                    <b-progress class="mt-1" :max="100" show-value v-if="valueProgressUpload > 0">
                                        <b-progress-bar :value="valueProgressUpload" variant="success"/>
                                    </b-progress>
                                    <iframe :src="fileDisposisi" v-if="jenisFileDisposisi == 'pdf'" class="mt-1" width="100%"></iframe>
                                    <img class="mt-1 preview-profil" :src="fileDisposisi"  @error="imgProfilError" v-if="jenisFileDisposisi == 'image'" width="100%" style="margin: 0 auto"/>
                                </b-form-group>
                                <b-button @click="kirimDisposisi">Kirim disposisi</b-button>
                            </div>
                        </b-col>
                    </b-row>
                </template>
            </b-modal>

            <b-modal
                    hide-header-close
                    no-close-on-backdrop
                    no-close-on-esc
                    ok-title="Simpan"
                    cancel-title="Batal"
                    @ok="submitSurat"
                    @hide="resetFormTambahSurat"
                    ref="modalTambahSurat"
                    size="xl">
                <template slot="modal-header">
                    <h3 class="modal-title">{{ this.payload.id == null ? 'Tambah Surat' : 'Ubah Surat' }}</h3>
                </template>
                <b-row>
                    <b-form-group label-class="h3" class="col-md-6 col-xl-12">
                        <b-form-group label="Nomor Surat" label-class="h6 mt2" label-cols="2">
                            <b-form-input v-model="payload.nomor" type="text" placeholder="Nomor Surat"/>
                        </b-form-group>
                        <b-form-group label="Jenis Surat" label-class="h6 mt2" label-cols="2">
                            <model-select v-model="payload.id_asal" :options="itemJenis" placeholder="Pilih jenis surat"/>
                        </b-form-group>
                        <b-form-group label="Tanggal Surat" label-class="h6 mt2" label-cols="2">
                            <date-picker
                                    v-model="payload.tanggal"
                                    lang="en"
                                    placeholder="Tanggal Surat"
                                    format="DD-MM-YYYY"
                                    style="width: 100%;"
                            ></date-picker>
                        </b-form-group>
                        <b-form-group label="Waktu Surat Diterima" label-class="h6 mt2" label-cols="2">
                            <date-time
                                    v-model="payload.waktu_diterima"
                                    lang="en"
                                    placeholder="Waktu Surat Diterima"
                                    format="DD-MM-YYYY H:i:s"
                                    style="width: 100%;"
                            ></date-time>
                        </b-form-group>
                        <b-form-group label="Nomor Agenda" label-class="h6 mt2" label-cols="2">
                            <b-form-input v-model="payload.no_agenda" type="text" placeholder="Nomor Agenda"/>
                        </b-form-group>
                        <b-form-group label="Pengirim" label-class="h6 mt2" label-cols="2">
                            <b-form-input v-model="payload.pengirim" type="text" placeholder="Pengirim"/>
                        </b-form-group>
                        <b-form-group label="Perihal" label-class="h6 mt2" label-cols="2">
                            <b-form-textarea
                                    id="perihal"
                                    v-model="payload.perihal"
                                    placeholder="Perihal Surat..."
                            ></b-form-textarea>
                        </b-form-group>
                        <b-form-group label="Klasifikasi" label-class="h6 mt2" label-cols="2">
                            <model-select v-model="payload.klasifikasi" :options="itemKlasifikasi" placeholder="Pilih jenis surat"/>
                        </b-form-group>
                        <b-form-group label="Derajat" label-class="h6 mt2" label-cols="2">
                            <model-select v-model="payload.derajat" :options="itemDerajat" placeholder="Pilih jenis surat"/>
                        </b-form-group>
                        <b-form-group label="File" label-class="h6 mt2" label-cols="2">
                            <b-form-file @change="onFotoChange" accept="image/*,application/pdf"/>
                            <b-progress class="mt-1" :max="100" show-value v-if="valueProgressUpload > 0">
                                <b-progress-bar :value="valueProgressUpload" variant="success"/>
                            </b-progress>
                            <iframe :src="urlFile" v-if="jenisFile == 'pdf'" class="mt-1" width="100%"></iframe>
                            <img class="mt-1 preview-profil" :src="urlFile"  @error="imgProfilError" v-if="jenisFile == 'image'" width="50%" style="margin: 0 auto"/>
                        </b-form-group>
                    </b-form-group>
                </b-row>

                <template slot="modal-footer">
                    <b-btn variant="primary" @click="submitSurat">Simpan</b-btn>
                    <b-btn variant="secondary" @click="$refs.modalTambahSurat.hide('cancel')">Batal</b-btn>
                </template>
            </b-modal>
        </div>
    </div>
</template>
<script>
    import format from 'date-fns/format'
    import { ModelSelect, MultiSelect} from 'vue-search-select'
    import { debounce, flattenDeep, values } from 'lodash'
    import Swal from 'sweetalert2'
    import DatePicker from "vue2-datepicker"
    import DateTime from 'vuejs-datetimepicker'

    const dateFnsBahasa = {
        locale: require('date-fns/locale/id')
    }

    export default {
        name: 'edisposisi',
        components: {
            DatePicker,
            DateTime,
            ModelSelect,
            MultiSelect
        },
        data () {
            return {
                totalRows: 0,
                perPage: 10,
                currentPage: 1,
                filter: '',
                filterDebounced: '',
                isBusy: false,
                sortBy: 'id_jabatan',
                sortDesc: false,
                valueProgressUpload: 0,
                tableColumns: {
                    index: {
                        label: 'No.'
                    },
                    no_agenda: {
                        label: 'No. Agenda',
                        sortable: true
                    },
                    klasifikasi: {
                        label: 'Klasifikasi',
                        sortable: true
                    },
                    derajat: {
                        label: "Derajat",
                        sortable: true
                    },
                    waktu_diterima: {
                        label: "Waktu Diterima"
                    },
                    asal: {
                        label: 'Asal Surat',
                        sortable: true
                    },
                    pengirim: {
                        label: 'Pengirim Surat',
                        sortable: true
                    },
                    aksi: {
                        label: 'Aksi',
                    },
                },
                payload: {
                    id: null,
                    asal: null,
                    no_agenda: null,
                    nomor: null,
                    tanggal: null,
                    waktu_diterima: null,
                    id_asal: null,
                    pengirim: null,
                    perihal: null,
                    derajat: 'biasa',
                    klasifikasi: 'biasa',
                    file: null,
                },
                payloadDisposisi: {
                    id: null,
                    asal: null,
                    no_agenda: null,
                    nomor: null,
                    tanggal: null,
                    waktu_diterima: null,
                    id_asal: null,
                    pengirim: null,
                    perihal: null,
                    derajat: 'biasa',
                    klasifikasi: 'biasa',
                    fileSurat: null,
                    file: null,
                    isi: null,
                    disposisi: [],
                    id_jabatan: []
                },
                fileDisposisi: null,
                jenisFileDisposisi: null,
                id_kesatuan: null,
                selectedJabatan: [],
                urlFile: null,
                jenisFile: null,
                itemKlasifikasi: [
                    {value: 'biasa', text: 'Biasa'},
                    {value: 'rahasia', text: 'Rahasia'},
                    {value: 'telegram_rahasia', text: 'SURAT TELEGRAM RAHASIA'},
                    {value: 'telegram', text: 'SURAT TELEGRAM'},
                    {value: 'undangan', text: 'UNDANGAN'},
                    {value: 'nota_dinas', text: 'NOTA DINAS'},
                    {value: 'sprin', text: 'NOTA DINAS'},
                    {value: 'kep', text: 'KEP'},
                ],
                itemDerajat: [
                    {value: 'biasa', text: 'Biasa'},
                    {value: 'segera', text: 'Segera'},
                    {value: 'kilat', text: 'Kilat'},
                ],
                itemJenis: [],
                pangkat: [],
                jabatan: [],
                lastSelectedJabatan: {},
                kesatuan: []
            }
        },
        methods: {
            providerSurat (ctx) {
                let sortBy

                switch(ctx.sortBy) {
                    case 'nomor':
                        sortBy = 'nomor'
                        break
                    case 'no_agenda':
                        sortBy = 'no_agenda'
                        break
                    case 'waktu_disposisi':
                        sortBy = 'id'
                        break
                    case 'tanggal':
                        sortBy = 'tanggal'
                        break
                    case '':
                        sortBy = 'status_dinas'
                        break
                    default:
                        sortBy = 'id'
                }

                let payload = {
                    page: ctx.currentPage,
                    filter: ctx.filter === '' ? null : ctx.filter,
                    sort: sortBy !== null ? (sortBy + ':' + (ctx.sortDesc ? 'desc' : 'asc')) : 'wAgenda:desc'
                }

                var promise = axios.get(baseUrl+'/api/disposisi', {
                    params: payload,
                    headers: {
                        Authorization: sessionStorage.getItem('token')
                    }
                })
                    .then(({data : {data, meta: { pagination }}}) => {
                        this.totalRows = pagination.total
                        this.perPage = pagination.per_page
                        this.currentPage = pagination.current_page
                        return data
                    })
                    .catch(({ response }) => {
                        // Catch error
                        return response
                    })

                return promise
            },
            prepareHapusDisposisi(item){
                Swal({
                    title: 'Hapus disposisi',
                    text: "Apakah anda yakin menghapus data disposisi?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '##dc3545',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        axios.delete(baseUrl + '/api/disposisi/disposisi/'+item.id, {
                            headers: { Authorization: sessionStorage.getItem('token') }
                        })
                            .then((response) => {
                                this.$noty.success('Data disposisi berhasil di hapus', { layout: 'topRight' })
                                this.prepareDisposisi({id: item.id_surat})
                                this.$refs.tabelSurat.refresh()
                            })
                            .catch(({ response: { status, data: { errors }}}) => {
                                if (status === 422)
                                    this.$noty.danger('Terjadi kesalahan saat menghapus data', { layout: 'topRight' })
                            })
                    }
                })
            },
            prepareShow(item){

            },
            onFotoDisposisiChange(e){
                var foto = e.target.files[0]
                //srcProfil = URL.createObjectURL(foto)

                var formData = new FormData()
                formData.append("file", foto, foto.name)
                formData.append("jenis", "disposisi")

                axios.post(baseUrl+"/api/disposisi/upload", formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Authorization': sessionStorage.getItem('token'),
                        'Accept': 'application/json'
                    },
                    onUploadProgress: function (progressEvent) {
                        this.valueProgressUpload = (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
                    }.bind(this)
                })
                    .then(({data}) => {
                        this.valueProgressUpload = 0
                        if ('error' in data){
                            this.$noty.error(data.error)
                        }
                        else if('success' in data){
                            this.fileDisposisi = baseUrl+"/api/upload/"+data.file
                            this.jenisFileDisposisi = data.file.endsWith('.pdf') ? 'pdf' : 'image'
                            this.payloadDisposisi.file = data.file
                        }
                    })
                    .catch(({response}) => {
                        this.valueProgressUpload = 0
                    })
            },
            onFotoChange(e){
                var foto = e.target.files[0]
                //srcProfil = URL.createObjectURL(foto)

                var formData = new FormData()
                formData.append("file", foto, foto.name)
                formData.append("jenis", "surat")

                axios.post(baseUrl+"/api/disposisi/upload", formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Authorization': sessionStorage.getItem('token'),
                        'Accept': 'application/json'
                    },
                    onUploadProgress: function (progressEvent) {
                        this.valueProgressUpload = (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
                    }.bind(this)
                })
                    .then(({data}) => {
                        this.valueProgressUpload = 0
                        if ('error' in data){
                            this.$noty.error(data.error)
                        }
                        else if('success' in data){
                            this.urlFile = baseUrl+"/api/upload/"+data.file
                            this.jenisFile = data.file.endsWith('.pdf') ? 'pdf' : 'image'
                            this.payload.file = data.file
                        }
                    })
                    .catch(({response}) => {
                        this.valueProgressUpload = 0
                    })
            },
            submitSurat(e){
                e.preventDefault()

                if (this.payload.id === null) {
                    axios.post(baseUrl+'/api/disposisi', this.payload, {
                        headers: {
                            Authorization: sessionStorage.getItem('token')
                        }
                    })
                        .then(({data}) => {
                            this.$noty.success('Surat berhasil ditambah')
                            this.$refs.modalTambahSurat.hide()
                            this.$refs.tabelSurat.refresh()
                        })
                        .catch((error) => {
                            console.log(error)
                            if (error.response) {
                                if (error.response.status === 422) {
                                    this.$noty.error(flattenDeep(values(error.response.data.errors)).join('<br>'))
                                }
                            }
                        })
                } else {
                    axios.post(baseUrl+'/api/disposisi/'+this.payload.id+'/edit', this.payload, {
                        headers: {
                            Authorization: sessionStorage.getItem('token')
                        }
                    })
                        .then(({data}) => {
                            if ('error' in data)
                                this.$noty.error(data.error)
                            else if('success' in data){
                                this.$noty.success('Surat berhasil diubah')
                                this.$refs.modalTambahSurat.hide()
                                this.$refs.tabelSurat.refresh()
                            }
                        })
                        .catch(({ response: { status, data: { errors }}}) => {
                            if (response.status === 422) {
                                this.$noty.error(flattenDeep(values(error.response.data.errors)).join('<br>'))
                            }
                        })
                }

            },
            kirimDisposisi(e){
                e.preventDefault()

                let payload = {
                    isi: this.payloadDisposisi.isi,
                    file: this.payloadDisposisi.file,
                    id_jabatan: this.payloadDisposisi.id_jabatan
                }
                axios.post(baseUrl+'/api/disposisi/'+this.payloadDisposisi.id+'/disposisi', payload, {
                    headers: {
                        Authorization: sessionStorage.getItem('token')
                    }
                })
                .then(({data}) => {
                    if ('error' in data){
                        this.$noty.error(data.error)
                    } else if('success' in data){
                        this.$noty.success('Disposisi berhasil di kirim')
                        this.prepareDisposisi({id: this.payloadDisposisi.id})
                        this.resetFormTambahDisposisi()
                        this.$refs.tabelSurat.refresh()
                    }
                })
            },
            imgProfilError (e){
                this.srcProfil = baseUrl+"/api/upload/personil/pocil.jpg"
            },
            onJabatanSelect(items, lastSelected) {
                this.selectedJabatan = items
                this.lastSelectJabatan = lastSelected
                this.payloadDisposisi.id_jabatan = items.map((item) => item.value)
            },
            resetFormTambahDisposisi(){
                this.payloadDisposisi = {
                    id: null,
                    asal: null,
                    no_agenda: null,
                    nomor: null,
                    tanggal: null,
                    waktu_diterima: null,
                    id_asal: null,
                    pengirim: null,
                    perihal: null,
                    derajat: 'biasa',
                    klasifikasi: 'biasa',
                    fileSurat: null,
                    file: null,
                    isi: null,
                    disposisi: [],
                    id_jabatan: []
                }
                this.fileDisposisi = null
                this.jenisFileDisposisi = null
                this.selectedJabatan = []
                this.lastSelectJabatan = {}
            },
            resetFormTambahSurat(){
                this.payload = {
                    id: null,
                    asal: null,
                    no_agenda: null,
                    nomor: null,
                    tanggal: null,
                    waktu_diterima: null,
                    id_asal: null,
                    pengirim: null,
                    perihal: null,
                    derajat: 'biasa',
                    klasifikasi: 'biasa',
                    file: null,
                    disposisi: [],
                    id_jabatan: []
                }
                this.urlFle = null
                this.jenisFile = null
            },
            prepareDisposisi({ id }){
                var promise = axios.get(baseUrl+'/api/disposisi/'+id, {
                    headers: {
                        Authorization: sessionStorage.getItem('token')
                    }
                })
                .then(({data: {data}}) => {
                    this.payloadDisposisi.id = data.id
                    this.payloadDisposisi.nama = data.nama
                    this.payloadDisposisi.asal = data.asal
                    this.payloadDisposisi.id_asal = parseInt(data.id_asal)
                    this.payloadDisposisi.no_agenda = data.no_agenda
                    this.payloadDisposisi.nomor = data.nomor
                    this.payloadDisposisi.tanggal = data.tanggal
                    this.payloadDisposisi.waktu_diterima = data.waktu_diterima_format
                    this.payloadDisposisi.pengirim = data.pengirim
                    this.payloadDisposisi.perihal = data.perihal
                    this.payloadDisposisi.derajat = data.derajat
                    this.payloadDisposisi.klasifikasi = data.klasifikasi
                    this.payloadDisposisi.fileSurat = data.file
                    this.payloadDisposisi.file = null
                    this.payloadDisposisi.disposisi = data.disposisi
                    this.urlFile = data.file
                    this.jenisFile = data.file ? (data.file.endsWith('.pdf') ? 'pdf' : 'image'): null

                    this.$refs.modalTambahDisposisi.show()
                })
            },
            prepareUbah({ id }) {
                var promise = axios.get(baseUrl+'/api/disposisi/'+id, {
                    headers: {
                        Authorization: sessionStorage.getItem('token')
                    }
                })
                    .then(({data : { data }}) => {
                        console.log(data)
                        this.payload.id = data.id
                        this.payload.nama = data.nama
                        this.payload.asal = data.asal
                        this.payload.id_asal = parseInt(data.id_asal)
                        this.payload.no_agenda = data.no_agenda
                        this.payload.nomor = data.nomor
                        this.payload.tanggal = data.tanggal
                        this.payload.waktu_diterima = data.waktu_diterima_format
                        this.payload.pengirim = data.pengirim
                        this.payload.perihal = data.perihal
                        this.payload.derajat = data.derajat
                        this.payload.klasifikasi = data.klasifikasi
                        this.payload.file = null
                        this.urlFile = data.file
                        this.jenisFile = data.file ? (data.file.endsWith('.pdf') ? 'pdf' : 'image'): null

                        this.$refs.modalTambahSurat.show()
                    })
            },
            prepareDelete (item) {
                Swal({
                    title: 'Hapus Surat',
                    text: "Apakah anda yakin menghapus Surat ?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '##dc3545',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        axios.delete(baseUrl + '/api/disposisi/'+item.id, {
                            headers: { Authorization: sessionStorage.getItem('token') }
                        })
                            .then((response) => {
                                this.$noty.success('Data surat berhasil di hapus', { layout: 'topRight' })
                                this.$refs.tabelSurat.refresh()
                            })
                            .catch(({ response: { status, data: { errors }}}) => {
                                if (status === 422)
                                    this.$noty.danger('Terjadi kesalahan saat menghapus data', { layout: 'topRight' })
                            })
                    }
                })
            },
            onSelectKelurahan(items, lastSelectItem){
                this.itemsKelurahan = items
                this.payload.id_kelurahan = items.map((val) => val.value)
            },
            fetchJenis(){
                var promise = axios.get(baseUrl+'/api/disposisi/jenis', {
                    headers: {
                        Authorization: sessionStorage.getItem('token')
                    }
                })
                .then(({data}) => {
                    this.itemJenis = data.map((val) => {
                        return {value: val.id, text: val.jenis}
                    })

                    this.payload.id_asal = this.itemJenis.length > 0 ? parseInt(this.itemJenis[0].value) : null
                })
            },
            fetchJabatan(){
                var promise = axios.get(baseUrl+'/api/jabatan', {
                    headers: {
                        Authorization: sessionStorage.getItem('token')
                    }
                })
                    .then(({data: {data}}) => {
                        this.jabatan = data.map((val) => {
                            return {value: val.id, text: val.jabatan}
                        })
                    })
                    .catch((error) => {

                    })
            },
            fetchPangkat(){
                var promise = axios.get(baseUrl+'/api/pangkat', {
                    headers: {
                        Authorization: sessionStorage.getItem('token')
                    }
                })
                    .then(({data: {data}}) => {
                        this.pangkat = data.map((val) => {
                            return {value: val.id, text: val.pangkat}
                        })
                    })
                    .catch((error) => {

                    })
            },
            fetchKesatuan(){
                var promise = axios.get(baseUrl+'/api/kesatuan', {
                    headers: {
                        Authorization: sessionStorage.getItem('token')
                    }
                })
                    .then(({data: {data}}) => {
                        this.kesatuan = data.map((val) => {
                            return {value: val.id, text: val.kesatuan}
                        })
                    })
                    .catch((error) => {

                    })
            },
            fetchWilKel(){
                var promise = axios.get(baseUrl+'/api/wilayah')
                    .then(({data : { data }}) => {
                        var kels = []
                        data.forEach((kec) => {
                            kec.kelurahan.forEach((kel) => {
                                kels.push({value: kel.id_kel, text: kel.jenis.nama+' '+ kel.nama+' Kec. '+kec.nama})
                            })
                        })
                        this.kelurahan = kels
                    })
                    .catch((error) => {

                    })

            },
            debounceFilter: debounce(function () {
                this.filter = this.filterDebounced
                this.currentPage = 1
            }, 500),
        },
        computed: {
            judulDisposisi:  function() {
                return 'Disposisi Surat <b>'+this.payloadDisposisi.nomor+'</b> No Agenda : <b>' + this.payloadDisposisi.no_agenda+'</b>'
            }
        },
        mounted(){
            this.fetchJabatan()
            this.fetchPangkat()
            this.fetchKesatuan()
            this.fetchJenis()
            this.fetchWilKel()
        },
        watch: {
            filterDebounced (newFilter) {
                this.debounceFilter()
            },
        }
    }
</script>

<style scoped>
    img.preview-profil {
        display:block;
        margin-left:auto;
        margin-right:auto;
    }
    .ui.selection.dropdown {
        background: #868e96;
        color: #fff;
    }
    .default {
        color: #fff;
    }
</style>