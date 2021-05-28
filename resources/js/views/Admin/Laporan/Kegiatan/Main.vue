<template>
    <div>
        <b-row>
            <b-col cols="6" md="6">
                <b-row>
                    <b-col cols="auto">
                        <h4>Laporan Kegiatan</h4>
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
                        <b-form-select
                            @select="selectData"
                            :v-model="jenisGiat"
                            :options="itemJenisGiat"
                            placeholder="Pilih Jenis Kegiatan"
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
                            <ph-printer class="phospor"/>
                        </button>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>

        <div class="position-relative mt-2">
            <center class="m-2">
                <b-spinner v-if="proses" variant="primary"/>
            </center>
            <b-table
                responsive
                ref="tabelLaporan"
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
                    {{ row.item.user.pangkat }} {{ row.item.user.nama }} <br/> {{ row.item.user.jabatan }}
                </template>
                <template #cell(img)="row">
                    <b-img :src="row.item.dokumentasi" width="100px" height="100px"></b-img>
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
                sortBy: "kegiatan.id",
                sortDesc: true,
                rentangTanggal: null,
                tableColumns: [
                    {
                        key: 'index',
                        label: 'No.'
                    },
                    { key: 'index', label: 'No' },
                    { key: 'detail', label: 'Detail', thStyle: { width: '25%' } },
                    { 
                        key: 'jenis', 
                        label: 'Jenis', 
                        sortable: false, 
                        formatter: v => {
                            var viewJenis = ''
                            v.forEach((w) => {
                                switch (w.jenis.keterangan) {
                                    case 'jenis_kegiatan':
                                        viewJenis += `${w.jenis.jenis}`
                                        break;
                                    default:
                                        break;
                                }
                            })
                            return viewJenis == '' ? '-' : viewJenis
                        } 
                    },
                    { 
                        key: 'w_kegiatan', label: 'Waktu kegiatan', 
                        formatter: v => format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}),
                    },
                    { key: 'user.nrp', label: 'Nrp' },
                    { key: 'nama', label: 'Nama' },
                ],
                proses: false,
                option: 1,
                tipeLaporan: [],
                itemTipeLaporan: {},
                jenisGiat: [],
                lastSelectItem: {},
                itemJenisGiat: [],
                semua: "0",
                checked: [],
                unchecked: [],
                type: 1,
                tableData: ""
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

                let payload = {
                    page: ctx.currentPage,
                    filter: ctx.filter === "" ? null : ctx.filter,
                    option: this.option,
                    type: this.type,
                    id_jenis: this.type == 2 ? this.jenisGiat : null,
                    rentang: this.rentangTanggal,
                    sort: sortBy !== null ? sortBy + ":" + (ctx.sortDesc ? "desc" : "asc") : "waktu_kegiatan:desc"
                };

                var promise = axios
                    .get("export-kegiatan", { params: payload })
                    .then(({data: {data, meta: {pagination}}}) => {
                        this.totalRows = pagination.total;
                        this.perPage = pagination.per_page;
                        this.currentPage = pagination.current_page;
                        this.tableData = data;
                        this.checked = [];
                        var self = this;
                        data.forEach(function (key) {
                            if (self.unchecked.indexOf(key.id) > -1) {
                            } else {
                                self.checked.push(key.id);
                            }
                        });
                        return data;
                    })
                    .catch(({response}) => {
                        // Catch error
                        return response;
                    });

                return promise;
            },
            selectData(jenisGiat, lastSelectItem) {
                this.jenisGiat = jenisGiat
                this.lastSelectItem = lastSelectItem
                this.semua = '0'
                if (this.type == '1') {
                    this.semua = '1'
                }
                this.$refs.tabelLaporan.refresh()
            },
            showData() {
                this.type = 1
                this.rentangTanggal = null
                this.jenisGiat = []
                this.semua = '0'
                this.$refs.tabelLaporan.refresh()
            },
            showDataType() {
                this.checked = [];
                this.unchecked = [];
                this.jenisGiat = this.itemJenisGiat[0].id
                this.semua = "0";

                if (this.option == 1 && this.type == 1) {
                    this.semua = "1";
                }
                this.$refs.tabelLaporan.refresh();
            },

            onInputRentangTgl(val) {
                this.semua = "1";
                this.$refs.tabelLaporan.refresh();
            },
            semuaToggle() {
                this.checked = [];
                if (this.semua == "0") {
                    var self = this;
                    var data = this.tableData;
                    data.forEach(function (key) {
                        self.checked.push(key.id);
                        var index = self.unchecked.indexOf(key.id);
                        self.unchecked.splice(index, 1);
                    });
                } else {
                    var self = this;
                    var table = this.tableData;
                    table.forEach(function (key) {
                        self.unchecked.push(key.id);
                    });
                }
            },
            toggleChecked(value) {
                if (this.checked.length == this.tableData.length) {
                    this.semua = "1";
                } else {
                    this.semua = "0";
                }

                var uncheck = this.unchecked;

                if (this.checked.indexOf(value) > -1) {
                    var index = this.unchecked.indexOf(value);
                    uncheck.splice(index, 1);
                } else {
                    uncheck.push(value);
                }
            },

            printData() {
                if (
                    this.option == "" ||
                    (this.option == 1 && this.type == "") ||
                    (this.option == 1 && this.type == 2 && this.jenisGiat.length == 0) ||
                    (this.option == 2 && this.rentangTanggal == "") ||
                    (this.option == 2 && this.type == "" && this.rentangTanggal == "") ||
                    (this.option == 2 && this.type == 2 && this.jenisGiat.length == 0 && this.rentangTanggal == "")
                ) {
                    console.log('Data tidak lengkap')
                    this.$toast.error("Silahkan lengkapi data jenis laporan", {
                        layout: "topRight"
                    });
                } else {
                    this.proses = true

                    var data = {
                        option: this.option,
                        type: this.type,
                        id_jenis: this.type == 2 ? this.jenisGiat : null,
                        rentang: this.rentangTanggal
                    };
                    axios({
                        method: "POST",
                        url: "export-laporan/cetak",
                        data: data,
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
                            if (status === 422)
                                this.$toast.error(flattenDeep(values(errors)).join("<br>"))
                        });
                }
            },
            fetchJenis() {
                var promise = axios.get('export-kegiatan/jenis')
                    .then(({data}) => {
                        var self = this
                        data.map((val) => {
                            self.itemJenisGiat.push({value: val.id, text: val.jenis})
                        })
                        this.jenisGiat = data[0].id
                    })
                    .catch((error) => {

                    })
            },
        },
        mounted() {
            this.fetchJenis()
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
