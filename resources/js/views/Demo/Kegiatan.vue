<template>
    <b-container>
        <div class="text-center my-5"><h3>SIMULASI TAMBAH KEGIATAN DAN QUICK RESPONSE</h3></div>
        <b-form>
            <b-form-group
                horizontal="horizontal"
                :label-cols="2"
                breakpoint="md"
                label="Bearer Token"
                class="mt-3">
                <b-form-textarea v-model="token" placeholder="Masukkan bearer token personil" rows="10"/>
                <b-button variant="primary" class="mt-3" @click="getJenis">Cek Tipe Laporan</b-button>
            </b-form-group>
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
                    placeholder="Pilih jenis kegiatan..."
                    :searchable="false"
                    :multiple="false"
                    @input="showSubJenis"
                    >
                </multiselect>
            </b-form-group>
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
        </b-form>
    </b-container>
</template>

<script>
export default {
    name : 'demo-kegiatan',
    data () {
        return {
            token: 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyIiwianRpIjoiMmIzMzZhZjZjMzNlN2FhMDdiMDI5NDg5ZWQyMTA2OGZkMDYxZTliZTkyYzFiOTBlYjliNGIyOTY3MGIyMDhiOWJlYjMxZmJhMGU2ZTJhOTEiLCJpYXQiOjE2MjE0ODM2OTMuOTk0NzM4LCJuYmYiOjE2MjE0ODM2OTMuOTk0NzQ0LCJleHAiOjE2NTMwMTk2OTMuOTg0MzQ3LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.ECERzNX5D3t3nHahXn_WXN1XlmW0Chi-NE34mwn8UElyp973eiiTCuyz4gb1yJIpb6FTHWdErFervCj-EmQrZ2tpftI1ppvCCoUChuS0OzHmx7LRPepZfqU_eJieqGilcUgQl5R3wJv1k6PCEMHi6Ne-_7gwv3oGibQjDlpA6GNZI8nus3hE-i7HsYh8EzDTRM7ogMFz1xS99-XeFLWBebYoH3RftAhXqOMH17szIon65Upgc9uMWeD7jTKSB0MNzQcTO_v_ax2vizggpIPE8J7SQ4IRLYNb3jKjGc-j0IOQ2aKUWnWNhPWNr0KAHlUNEDTxkQzHSXpawRGI6M5du8zVQFniuLj5IrZ3Wlhn5ETwLK_2Tdvfb3d0EtGiNOFAX5Th8qv5kTQ8Kf2em7bh3Q9R8qvKIXDzgTRvgxdom4QY-5MLUKWS4X9nWPbquUPFy97xkhxTfGkxLUna4TOYc3HBzRoljp2B5TPlpf438Hu4pSbouFIuAR5KIl-5sAa6xGgxAVXzYXZesdjVeNLl6cmuzOuhVHZewnKbE59f1UYLZbSaHTyfVxYcAqrwNl7eDOQ9wlQIxPJ05v-I_2JmAUof9a0Gt8g7TbvJ38odOFhCVbVgWU8--vS0CTc-9VLEcdntiTJYsElaNJkBpIWzt6nZI3LVjiGUl0Vxz6vQJVs',
            jenisKegiatan: null,
            jenisKegiatanOptions: [],
            subJenisKegiatan: [],
            dropdownSubJenisKegiatan: [],
        }
    },
    methods : {
        getJenis () {
            if(this.token == null) {
                this.$toast.error('Lengkapi token')
                return
            }
                
            axios.get(baseUrl + '/api/kegiatan/tipejenisbypersonil', 
                { headers: { Authorization: this.token } }
            )
            .then(({ data : { data } }) => {
                this.jenisKegiatan = null
                this.jenisKegiatanOptions = []
                this.subJenisKegiatan = []
                this.dropdownSubJenisKegiatan = []
                this.jenisKegiatanOptions = data
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
        }
    },
}
</script>

<style>

</style>