<template>
    <div>
        <b-row>
            <b-col cols="6" md="6">
                <b-row>
                    <b-col cols="auto">
                        <h4>Laporan Absensi</h4>
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="6" md="6">
                <b-pagination
                    align="right"
                    :total-rows="totalRows"
                    v-model="currentPage"
                    :per-page="perPage"
                />
            </b-col>
        </b-row>
        <b-row>
            <b-col cols="11" md="11">
                <b-row>
                    <b-col cols="3" md="3">
                        <model-select
                            @input="getLevel3"
                            v-model="kesatuanLevel2"
                            :options="kesatuanLevel2Options"
                            placeholder="Pilih Kesatuan"
                        />
                    </b-col>
                    <b-col cols="3" md="3" v-if="kesatuanLevel3Options.length != 0">
                        <model-select
                            @input="getLevel4"
                            v-model="kesatuanLevel3"
                            :options="kesatuanLevel3Options"
                            placeholder="Pilih Kesatuan"
                        />
                    </b-col>
                    <b-col cols="3" md="3" v-if="kesatuanLevel4Options.length != 0">
                        <model-select
                            @input="setLevel4"
                            v-model="kesatuanLevel4"
                            :options="kesatuanLevel4Options"
                            placeholder="Pilih Kesatuan"
                        />
                    </b-col>
                    <b-col cols="3" md="3">
                        <b-row>
                            <b-col>
                                <date-picker
                                    v-model="rentangTanggal"
                                    @input="onInputRentangTgl"
                                    range
                                    lang="id"
                                    placeholder="Rentang tanggal"
                                    format="DD-MM-YYYY"
                                    style="width: 100%;"
                                ></date-picker>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="1" md="1">
                <b-row>
                    <b-col>
                        <button class="btn btn-primary float-right" :disabled="proses" @click="printData">
                            <b-spinner style="width: 1rem; height: 1rem; top: -3px; position: relative;" variant="light" v-if="proses"/>
                            <ph-printer class="phospor"/>
                        </button>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>

        <div class="position-relative mt-2">
            <b-table
                responsive
                ref="table"
                :busy.sync="isBusy"
                :fields="tableColumns"
                :items="providerLaporan"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
            >
                <template #cell(index)="data">
                    {{ ((currentPage - 1) * 10) + data.index + 1 }}
                </template>
                <template #cell(index)="data">{{ ((currentPage - 1) * 10) + data.index + 1 }}</template>
                <template #cell(nama)="row">
                    {{ row.item.personil.pangkat }} {{ row.item.personil.nama }} <br/> {{ row.item.personil.jabatan }}
                </template>
                <template #cell(img)="row">
                    <b-img :src="row.item.dokumentasi" v-if="row.item.dokumentasi != null" width="100px" height="100px"></b-img>
                    <div v-else>-</div>
                </template>
            </b-table>

            <div class="loading" v-show="isBusy">
                <b-spinner variant="primary"/>
            </div>
        </div>
    </div>
</template>
<script>
    import DatePicker from "vue2-datepicker";
    import {debounce, flattenDeep, values} from "lodash";
    import { format, formatISO, parseISO } from 'date-fns'
    import {ModelSelect, MultiSelect} from "vue-search-select";
    import id from 'date-fns/locale/id'

    export default {
        name: "Laporan",
        components: {
            ModelSelect,
            MultiSelect,
            DatePicker
        },
        data() {
            return {
                totalRows: 0,
                perPage: 10,
                currentPage: 1,
                filter: "",
                filterDebounced: "",
                isBusy: false,
                sortBy: "absensi_personil.id",
                sortDesc: true,
                tableColumns: [
                    {
                        key: 'index',
                        label: 'No.'
                    },
                    { key: 'index', label: 'No' },
                    { key: 'personil.nrp', label: 'Nrp' },
                    { key: 'nama', label: 'Nama' },
                    { key: 'personil.kesatuan_lengkap', label: 'Kesatuan' },
                    { 
                        key: 'waktu_mulai', label: 'Datang', 
                        formatter: v => v != null ? (format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id})) : '-',
                    },
                    { 
                        key: 'waktu_selesai', label: 'Pulang', 
                        formatter: v => v != null ? (format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id})) : '-',
                    },
                ],
                proses: false,
                rentangTanggal: null,
                kesatuanLevel2: null,
                kesatuanLevel3: null,
                kesatuanLevel4: null,
                kesatuanLevel2Options: [],
                kesatuanLevel3Options: [],
                kesatuanLevel4Options: [],
            };
        },
        methods: {
            providerLaporan(ctx) {
                let sortBy;

                switch (ctx.sortBy) {
                    case "w_kegiatan":
                        sortBy = "waktu_kegiatan";
                        break;
                    default:
                        sortBy = ctx.sortBy;
                }
                
                if(this.rentangTanggal != null) {
                    if(this.rentangTanggal[0] != null) {
                        var mulai = format(parseISO(formatISO(this.rentangTanggal[0], { representation: 'complete' })), 'yyyy-MM-dd')
                        var selesai = format(parseISO(formatISO(this.rentangTanggal[1], { representation: 'complete' })), 'yyyy-MM-dd')
                    }
                }
                let kesatuan = this.kesatuanLevel4 != null ? this.kesatuanLevel4 : 
                                (this.kesatuanLevel3 != null ? this.kesatuanLevel3 : 
                                (this.kesatuanLevel2 != null ? this.kesatuanLevel2 : ''))
                let payload = {
                    page: ctx.currentPage,
                    filter: ctx.filter === "" ? null : ctx.filter,
                    kesatuan: kesatuan,
                    rentang: this.rentangTanggal == null ? '' : this.rentangTanggal[0] != null ? ([mulai, selesai]) : '',
                    sort: sortBy !== null ? sortBy + ":" + (ctx.sortDesc ? "desc" : "asc") : "created_at:desc"
                };

                var promise = axios
                    .get("absensi", { params: payload })
                    .then(({data: {data, meta: {pagination}}}) => {
                        this.totalRows = pagination.total;
                        this.perPage = pagination.per_page;
                        this.currentPage = pagination.current_page;
                        return data;
                    })
                    .catch(({response}) => {
                        // Catch error
                        console.log(response)
                        return response;
                    });

                return promise;
            },
            refreshTable () {
                this.totalRows > this.perPage ? 
                (this.currentPage == 1 ? this.$refs.table.refresh() : this.currentPage = 1) 
                : this.$refs.table.refresh()
            },
            getLevel3(kesatuan) {
                this.kesatuanLevel3 = null
                this.kesatuanLevel3Options = []
                this.kesatuanLevel4 = null
                this.kesatuanLevel4Options = []
                this.fetchKesatuanChild(kesatuan, 2)
                this.refreshTable()
            },
            getLevel4(kesatuan) {
                this.kesatuanLevel4 = null
                this.kesatuanLevel4Options = []
                this.fetchKesatuanChild(kesatuan, 3)
                this.refreshTable()
            },
            setLevel4(kesatuan) {
                this.refreshTable()
            },
            onInputRentangTgl(val) {
                this.refreshTable()
            },
            printData() {
                this.proses = true

                if(this.rentangTanggal != null) {
                    if(this.rentangTanggal[0] != null) {
                        var mulai = format(parseISO(formatISO(this.rentangTanggal[0], { representation: 'complete' })), 'yyyy-MM-dd')
                        var selesai = format(parseISO(formatISO(this.rentangTanggal[1], { representation: 'complete' })), 'yyyy-MM-dd')
                    }
                }
                let kesatuan = this.kesatuanLevel4 != null ? this.kesatuanLevel4 : 
                                (this.kesatuanLevel3 != null ? this.kesatuanLevel3 : 
                                (this.kesatuanLevel2 != null ? this.kesatuanLevel2 : ''))
                let payload = {
                    kesatuan: kesatuan,
                    rentang: this.rentangTanggal == null ? '' : this.rentangTanggal[0] != null ? ([mulai, selesai]) : '',
                }
                axios({
                    method: "POST",
                    url: "absensi/export",
                    data: payload,
                    responseType: "blob"
                })
                .then(response => {
                    this.proses = false
                    let url = window.URL.createObjectURL(new Blob([response.data], { type: 'data:application/vnd.ms-excel' }));
                    let filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '')
                    let link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(({response: {status, data: {errors}}}) => {
                    this.proses = false
                    if (status === 422)
                        this.$toast.error(flattenDeep(values(errors)).join("<br>"))
                })
            },
            fetchKesatuan() {
                var promise = axios.get('kesatuan/level/2')
                    .then(({data}) => {
                        var self = this
                        data.map((val) => {
                            self.kesatuanLevel2Options.push({value: val.id, text: val.kesatuan})
                        })
                    })
                    .catch((error) => {

                    })
            },
            fetchKesatuanChild(id, level) {
                var promise = axios.get(`kesatuan/child/${id}`)
                    .then(({data}) => {
                        var self = this
                        data.map((val) => {
                            if(level == 2) {
                                self.kesatuanLevel3Options.push({value: val.id, text: val.kesatuan})
                            } else {
                                self.kesatuanLevel4Options.push({value: val.id, text: val.kesatuan})
                            }
                        })
                    })
                    .catch((error) => {

                    })
            },
        },
        mounted() {
            this.fetchKesatuan()
        },
    };
</script>

<style scoped>
    img.preview-profil {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
</style>
