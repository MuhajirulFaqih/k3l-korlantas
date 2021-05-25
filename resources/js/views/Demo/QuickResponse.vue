<template>
    <div class="wrapper admin">
        <b-container fluid>
            <b-row>
                <b-col cols="6">
                    <div class="my-5"><h4>SIMULASI QUICK RESPONSE</h4></div>
                        <b-form-group
                            horizontal="horizontal"
                            :label-cols="2"
                            breakpoint="md"
                            label="Bearer Token Akun Personil"
                            class="mt-3">
                            <b-form-textarea class="e-form" v-model="token" placeholder="Masukkan bearer token personil. Ex : eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyIiwianRpIjoiYjNkMTY0ZGY3MjhiZjViOTBlMW" rows="10"/>
                            <b-button variant="primary" class="mt-3" @click="getTipe">Cek Tipe Laporan</b-button>
                        </b-form-group>
                        <div v-if="tipeOptions.length != 0">
                            <b-form-group
                                horizontal="horizontal"
                                :label-cols="2"
                                breakpoint="md"
                                label="Tipe Laporan / Kesatuan">
                                <multiselect
                                    v-model="tipe"
                                    :options="tipeOptions"
                                    :show-labels="false"
                                    value="id"
                                    label="kesatuan"
                                    placeholder="Pilih tipe / kesatuan..."
                                    :searchable="false"
                                    :multiple="false"
                                    @input="getJenis"
                                    >
                                </multiselect>
                            </b-form-group>
                        </div>
                        <b-form v-if="isCheck && token != null" @submit.prevent="kirimKegiatan">
                            <div v-if="jenisKegiatanOptions.length > 0" v-show="(tipe == null ? true : (tipe.id != 0))">
                                <b-form-group
                                    v-show="jenisKegiatanOptions.length != 0"
                                    horizontal="horizontal"
                                    :label-cols="2"
                                    breakpoint="md"
                                    label="Jenis Laporan">
                                    <multiselect
                                        v-model="jenisKegiatan"
                                        :options="jenisKegiatanOptions"
                                        :show-labels="false"
                                        value="id"
                                        label="jenis"
                                        placeholder="Pilih jenis quick response..."
                                        :searchable="false"
                                        :multiple="false"
                                        @input="showSubJenis"
                                        >
                                    </multiselect>
                                </b-form-group>
                            </div>
                            <div v-if="jenisKegiatan != null">
                                <div v-for="(i, k) in jenisKegiatan.children" :key="`sub${k}`">
                                    <b-form-group
                                        horizontal="horizontal"
                                        :label-cols="2"
                                        breakpoint="md"
                                        :label="i.jenis">
                                        <multiselect
                                            v-model="subJenisKegiatan[k]"
                                            :options="i.children"
                                            :show-labels="false"
                                            value="id"
                                            label="jenis"
                                            :placeholder="`Pilih ${i.jenis}...`"
                                            :searchable="false"
                                            :multiple="false"
                                            @input="showChildSubJenis"
                                            >
                                        </multiselect>
                                    </b-form-group>
                                    <div v-if="subJenisKegiatan.length != 0">
                                        <div v-if="typeof subJenisKegiatan[k] != 'undefined'">
                                            <div v-if="subJenisKegiatan[k].children.length != 0">
                                                <b-form-group
                                                v-for="(j, l) in subJenisKegiatan[k].children" :key="`childsub${l}`"
                                                    horizontal="horizontal"
                                                    :label-cols="2"
                                                    breakpoint="md"
                                                    :label="j.jenis">
                                                    <multiselect
                                                        v-model="dropdownSubJenisKegiatan[l]"
                                                        :options="j.children"
                                                        :show-labels="false"
                                                        value="id"
                                                        label="jenis"
                                                        :placeholder="`Pilih ${j.jenis}...`"
                                                        :searchable="false"
                                                        :multiple="false"
                                                        >
                                                    </multiselect>
                                                </b-form-group>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="hasDaftarRekan">
                                <b-form-group
                                horizontal="horizontal"
                                :label-cols="2"
                                breakpoint="md"
                                label="Daftar rekan">
                                    <b-form-textarea class="e-form" rows="3" v-model="single.daftar_rekan"/>
                                </b-form-group>
                            </div>
                            <div v-if="hasNomorPolisi">
                                <b-form-group
                                horizontal="horizontal"
                                :label-cols="2"
                                breakpoint="md"
                                label="Nomor polisi">
                                    <b-form-input class="e-form" v-model="single.nomor_polisi"/>
                                </b-form-group>
                            </div>
                            <div >
                                <b-form-group
                                horizontal="horizontal"
                                :label-cols="2"
                                breakpoint="md"
                                label="Detail laporan">
                                    <b-form-textarea rows="4" class="e-form" v-model="single.detail"/>
                                </b-form-group>
                            </div>
                            <div v-if="hasKelurahan">
                                <b-form-group
                                horizontal="horizontal"
                                :label-cols="2"
                                breakpoint="md"
                                label="Kabupaten">
                                    <multiselect
                                        v-model="kabupaten"
                                        :options="kabupatenOptions"
                                        :show-labels="false"
                                        value="id_kab"
                                        label="nama"
                                        placeholder="Pilih kabupaten..."
                                        :searchable="false"
                                        :multiple="false"
                                        @input="getKecamatan"
                                        >
                                    </multiselect>
                                </b-form-group>
                                <b-form-group
                                horizontal="horizontal"
                                :label-cols="2"
                                breakpoint="md"
                                label="Kecamatan">
                                    <multiselect
                                        v-model="kecamatan"
                                        :options="kecamatanOptions"
                                        :show-labels="false"
                                        value="id_kec"
                                        label="nama"
                                        placeholder="Pilih kecamatan..."
                                        :searchable="false"
                                        :multiple="false"
                                        @input="getKelurahan"
                                        >
                                    </multiselect>
                                </b-form-group>
                                <b-form-group
                                horizontal="horizontal"
                                :label-cols="2"
                                breakpoint="md"
                                label="Desa/Kelurahan">
                                    <multiselect
                                        v-model="kelurahan"
                                        :options="kelurahanOptions"
                                        :show-labels="false"
                                        value="id_kel"
                                        label="nama"
                                        placeholder="Pilih desa/kelurahan..."
                                        :searchable="false"
                                        :multiple="false"
                                        @input="setKelurahan"
                                        >
                                    </multiselect>
                                </b-form-group>
                            </div>
                            <div v-if="hasRute">
                                <b-form-group
                                horizontal="horizontal"
                                :label-cols="2"
                                breakpoint="md"
                                label="Rute">
                                    <b-form-textarea class="e-form" v-model="single.rute_patroli"/>
                                </b-form-group>
                            </div>
                            <div>
                                <b-form-group
                                horizontal="horizontal"
                                :label-cols="2"
                                breakpoint="md"
                                label="Dokumentasi">
                                    <b-form-file id="foto" class="e-form" v-model="dokumentasi" placeholder="Pilih gambar..." accept="image/*"></b-form-file>
                                </b-form-group>
                            </div>
                            <div class="text-center mt-4">
                                <button class="btn e-btn e-btn-success mx-1">
                                    <ph-paper-plane-tilt class="phospor"/> Simpan</button>
                            </div>
                        </b-form>
                </b-col>
                <b-col cols="6">
                    <div class="my-5">
                        <h4 class="d-inline-block">10 QUICK RESPONSE TERBARU</h4>
                        <b-button variant="primary" class="ml-2" size="sm" @click="getKegiatan">Get Quick Response</b-button>
                    </div>
                    <b-row>
                        <b-col cols="6 pb-5" v-for="(k, i) in kegiatan" :key="`${i}`">
                            <img :src="k.dokumentasi" alt="" class="w-100 mb-2">
                            <b-row>
                                <b-col cols="6">
                                    <div v-if="k.w_kegiatan">{{ k.w_kegiatan }}</div>
                                </b-col>
                                <b-col cols="6 text-right">
                                    <div v-if="k.is_quick_response == 1"><b-badge variant="primary">Quick Response</b-badge></div>
                                </b-col>
                            </b-row>
                            <div><b>{{ k.detail }}</b></div>
                            <div v-if="typeof k.jenis != 'undefined'" v-html="formatJenis(k.jenis)"></div>
                            <div v-if="k.daftar_rekan">Daftar rekan : {{ k.daftar_rekan }}</div>
                            <div v-if="k.nomor_polisi">Nomor polisi : {{ k.nomor_polisi }}</div>
                            <div v-if="k.rute_patroli">Rute patroli : {{ k.rute_patroli }}</div>
                            <div v-if="typeof k.kelurahan != 'undefined'">Kelurahan : {{ k.kelurahan.jenis.nama }} {{ k.kelurahan.nama }}</div>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
import { debounce, flattenDepth, values } from 'lodash'
export default {
    name : 'demo-kegiatan',
    data () {
        return {
            token: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyIiwianRpIjoiY2ZjZjVlNTMwMjEzNjdiODg2ZThlM2NjM2VjZDY2YjU3YWZkZGIzNzRmNGVlZGI5NmNjMWZmYjMwZjhiNWQwZTY0ZmM5MTZiZjgyZTEzM2UiLCJpYXQiOjE2MjE4NDkxMzMuODQ5NzU3LCJuYmYiOjE2MjE4NDkxMzMuODQ5NzY2LCJleHAiOjE2NTMzODUxMzMuODIzOTQ0LCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.A6VgEj4lTck__0fDBhpKwMftd8igxcssftNOVu7g6Ol8ZnefHC7PpTP877hPuuX1fVERGDoAuvCpnvuzoVoO2yuwNRHn--tU9KnDD5XdNPVzuHPIZM2xL6Js3PpDVnpwmGUTjnaZv1OxcdY4tNngVcd3Dyu1xpe_wYpSw1MtmaaB4ir-02V_W1ZGD49-l5bONTrGAYi-W2yAcZXCUMh2QXizkRUsEfeZS1pp7-f7dRR2nanVWBROwKaeDY21EoiCT_sLXF4YdkXL7goS_OkFxw_ZofFUoega7EirDc21I90Len_WZQc_yGIAPTcatdYNSVAl63Z5LW0hogjhugKGwk_avFBrya1bgm_DdASfcE3_NZtfLWcSxPUzhXizgE-N8xA1_GM38TFkiimNgW3Ci2-O69vFn02wlLNAuisDCdJhjCLlQjqTQAJYhcTdkqOHr4zaMkxNiv5qnOVXdIYC7U9MRjpgWZMk3-Hldv-1K9zui-49txfKUDpWiCf54-RaQfKOF94wfRhrQTLGA362CaV0VLv01-_t_BjPvG7yIVT-vhAb3g095SWFujwsaiaDGocCKBlG4NJM72vZKZt4o22hXItgeNcxOs9aswfUxak8d0Q0FuZiy9ciyiS47SD9yNxHDL9_XmMHlHrDXzBNo96BAOb7wO3_hcWcgrVBmcA',
            isCheck: false,
            kegiatan: [],
            tipe: null,
            tipeOptions: [],
            jenisKegiatan: null,
            jenisKegiatanOptions: [],
            subJenisKegiatan: [],
            dropdownSubJenisKegiatan: [],
            kabupaten: null,
            kabupatenOptions: [],
            kecamatan: null,
            kecamatanOptions: [],
            kelurahan: null,
            kelurahanOptions: [],
            dokumentasi: null,
            single: {
                daftar_rekan: '',
                nomor_polisi: '',
                detail: '',
                id_kelurahan_binmas: '',
                rute_patroli: '',
                is_quick_response: '1',
            }
        }
    },
    computed: {
        hasDaftarRekan () {
            return this.jenisKegiatan !== null ? (this.jenisKegiatan.has_daftar_rekan == 1) : false
        },
        hasNomorPolisi () {
            return this.jenisKegiatan !== null ? (this.jenisKegiatan.has_nomor_polisi == 1) : false
        },
        hasKelurahan () {
            return this.jenisKegiatan !== null ? (this.jenisKegiatan.has_kelurahan == 1) : false
        },
        hasRute () {
            return this.jenisKegiatan !== null ? (this.jenisKegiatan.has_rute == 1) : false
        },
    },
    methods : {
        getKegiatan() {
            if(this.token == null) { 
                this.$toast.error('Token masih kosong') 
                return 
            }
            axios.get(baseUrl + '/api/kegiatan?is_quick_response=1', 
                { headers: { Authorization: 'Bearer ' + this.token } }
            )
            .then(({ data : { data } }) => {
                this.kegiatan = data
            })
            .catch(({ response }) => {
            })
        },
        formatJenis(jenis) {
            var viewJenis = ''
            jenis.forEach((v) => {
                switch (v.jenis.keterangan) {
                    case 'jenis_kegiatan':
                        viewJenis += `Jenis Quick Response : ${v.jenis.jenis}<br/>`
                        break;
                    case 'subjenis':
                        viewJenis += `${v.jenis.parent.jenis} : ${v.jenis.jenis}<br/>`
                        break;
                    case 'dropdown_subjenis':
                        viewJenis += `${v.jenis.parent.jenis} : ${v.jenis.jenis}<br/>`
                        break;
                
                    default:
                        break;
                }
            })
            return viewJenis
        },
        getKabupaten() {
            if(this.token == null) { return }
            this.kecamatan = null
            axios.get(baseUrl + '/api/kabupaten-provinsi', 
                { headers: { Authorization: 'Bearer ' + this.token } }
            )
            .then(({ data : { kabupaten } }) => {
                this.kabupatenOptions = kabupaten
            })
            .catch(({ response }) => {
            })
        },
        getKecamatan(kabupaten) {
            this.kelurahan = null
            this.single.id_kelurahan_binmas = null
            axios.get(baseUrl + '/api/kecamatan/' + kabupaten.id_kab, 
                { headers: { Authorization: 'Bearer ' + this.token } }
            )
            .then(({ data : { kecamatan } }) => {
                this.kecamatanOptions = kecamatan
            })
            .catch(({ response }) => {
            })
        },
        getKelurahan(kecamatan) {
            this.single.id_kelurahan_binmas = null
            axios.get(baseUrl + '/api/kelurahan/' + kecamatan.id_kec, 
                { headers: { Authorization: 'Bearer ' + this.token } }
            )
            .then(({ data : { kelurahan } }) => {
                this.kelurahanOptions = kelurahan
            })
            .catch(({ response }) => {
            })
        },
        setKelurahan(kelurahan) {
            this.single.id_kelurahan_binmas = kelurahan.id_kel
        },
        getTipe () {
            if(this.token == null) {
                this.$toast.error('Lengkapi token')
                return
            }
                
            axios.get(baseUrl + '/api/kegiatan/kesatuanquickresponse', 
                { headers: { Authorization: 'Bearer ' + this.token } }
            )
            .then(({ data : { data } }) => {
                this.tipe = null
                this.tipeOptions = []
                this.jenisKegiatan = null
                this.jenisKegiatanOptions = []
                this.subJenisKegiatan = []
                this.dropdownSubJenisKegiatan = []
                this.tipeOptions = data
                this.getKabupaten()
            })
            .catch(({ response: { status, data: { errors }}}) => {
            })
        },
        getJenis (val) {
            if(val == null) {
                this.jenisKegiatan = null
                this.jenisKegiatanOptions = []
                this.subJenisKegiatan = []
                this.dropdownSubJenisKegiatan = []
                return
            }
                
            axios.get(baseUrl + `/api/kegiatan/tipejenisbykesatuan/${this.tipe.id}`, 
                { headers: { Authorization: 'Bearer ' + this.token } }
            )
            .then(({ data : { data } }) => {
                this.isCheck = true
                this.jenisKegiatanOptions = []
                this.subJenisKegiatan = []
                this.dropdownSubJenisKegiatan = []
                this.jenisKegiatanOptions = data
                
                if(this.tipe.id != 0) {
                    this.jenisKegiatan = null
                } else {
                    this.jenisKegiatan = data[0]
                }
            })
            .catch(({ response: { status, data: { errors }}}) => {
            })
        },
        showSubJenis () {
            this.subJenisKegiatan = []
            this.dropdownSubJenisKegiatan = []
        },
        showChildSubJenis (jenis) {
            if(typeof jenis.children !== 'undefined') {
                if(jenis.children.length > 0) {
                    this.dropdownSubJenisKegiatan = []
                }
            }
        },
        kirimKegiatan() {
            var data = new FormData()
            var single = this.single
            
            data.append('lat', defaultLat)
            data.append('lng', defaultLng)
            //Collect Jenis
            if(this.jenisKegiatan != null) {
                data.append('id_jenis[]', this.jenisKegiatan.id)
                this.jenisKegiatan.children.forEach((v) => {
                    data.append('id_jenis[]', v.id)
                })
                this.subJenisKegiatan.forEach((v) => {
                    data.append('id_jenis[]', v.id)
                    if(v.children) {
                        v.children.forEach(vv => {
                            data.append('id_jenis[]', vv.id)
                        })
                    }
                })
                this.dropdownSubJenisKegiatan.forEach((v) => {
                    data.append('id_jenis[]', v.id)
                })
            }
            
            data.append('dokumentasi', document.getElementById('foto').files[0])
            Object.keys(single).forEach(function(key) {
                data.append(key, single[key])
            })

            axios.post(baseUrl + '/api/kegiatan', data, {
                headers: { 
                    'Authorization' : 'Bearer ' + this.token,
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(({data}) => {
                this.$toast.success('Data kegiatan berhasil di tambahkan')
                this.resetSingle()
            })
            .catch(({ response: { status, data: { errors }}}) => {
                if (status === 422)
                    this.$toast.error(flattenDepth(values(errors)).join('<br>'))
            })

        },
        resetSingle () {
            this.isCheck = false
            this.tipe = null
            this.jenisKegiatan = null
            this.subJenisKegiatan = []
            this.dropdownSubJenisKegiatan = []
            this.kabupaten = null
            this.kecamatan = null
            this.kecamatanOptions = []
            this.kelurahan = null
            this.kelurahanOptions = []
            this.dokumentasi = null
            this.single = {
                daftar_rekan: '',
                nomor_polisi: '',
                detail: '',
                id_kelurahan_binmas: '',
                rute_patroli: '',
                is_quick_response: '1'
            }
            this.getKegiatan()
        }

    },
}
</script>

<style>

</style>