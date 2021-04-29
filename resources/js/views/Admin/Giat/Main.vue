<template>
    <div>
        <b-row>
            <b-col cols="6" md="6">
                <b-row>
                    <b-col cols="auto">
                        <h4>Giat Rutin</h4>
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="6" md="6">
                <b-pagination
                    align="right"
                    :total-rows="totalRows"
                    v-model="currentPage"
                    :per-page="perPage"/>
            </b-col>
        </b-row>
        <b-row>
            <b-col cols="11" md="11">
                <b-row>
                    <b-col cols="3" md="3">
                        <model-select
                            @input="showData"
                            v-model="option"
                            :options="indukOptions"
                            placeholder="Pilih Cetak"/>
                    </b-col>
                    <b-col cols="3" md="3" v-show="option != ''">
                        <model-select
                            @input="showDataType"
                            v-model="type"
                            :options="typeOptions"
                            placeholder="Pilih Kegiatan"/>
                    </b-col>
                    <b-col cols="3" md="3" v-show="type == '2'">
                        <multi-select
                            @select="selectData"
                            :selected-options="jenisGiat"
                            :options="itemJenisGiat"
                            placeholder="Pilih Jenis Kegiatan"/>
                    </b-col>
                    <b-col cols="3" md="3">
                        <b-row>
                            <b-col v-show="option == 2">
                                <date-picker
                                    v-model="rentangTanggal"
                                    @input="onInputRentangTgl"
                                    range="range"
                                    lang="id"
                                    placeholder="Rentang tanggal"
                                    format="DD-MM-YYYY"
                                    style="width: 100%;"></date-picker>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="1" md="1">
                <b-row>
                    <b-col>
                        <button class="btn btn-primary float-right" :disabled="isBusyPrint" @click="printData">
                            <span v-if="isBusyPrint">Proses...</span>
                            <span v-else>Cetak</span>
                        </button>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>

        <div class="position-relative mt-2">
            <b-table
                responsive="responsive"
                ref="tabelLaporan"
                :busy.sync="isBusy"
                :fields="tableColumns"
                :items="providerLaporan"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc">
                    <template #head(checkbox)>
                        <center>
                            <b-form-checkbox
                                @click.native.stop
                                v-model="semua"
                                value="1"
                                unchecked-value="0"
                                @change="semuaToggle"/>
                        </center>
                    </template>
                    <template v-slot:cell(checkbox)="row">
                        <center>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input
                                    type="checkbox"
                                    class="custom-control-input"
                                    :id="'check' + row.item.id"
                                    :value="row.item.id"
                                    v-model="checked"
                                    @change="toggleChecked(row.item.id)">
                                    <label class="custom-control-label" :for="'check' + row.item.id"></label>
                                </div>
                        </center>
                    </template>
                    <template v-slot:cell(index)="data">{{ ((currentPage - 1) * 10) + data.index + 1 }}</template>
                    <template v-slot:cell(nama)="row">
                        {{ row.item.user.pangkat }}
                        {{ row.item.user.nama }}
                        <br/>
                        {{ row.item.user.jabatan }}
                    </template>
                    <template v-slot:cell(img)="row">
                        <b-img :src="row.item.dokumentasi" width="100px" height="100px"></b-img>
                    </template>
                </b-table>

                <div class="loading" v-show="isBusy">
                    <b-spinner variant="primary"></b-spinner>
                </div>
            </div>
        </div>
    </template>
    <script>
        import moment from 'moment'
        import DatePicker from "vue2-datepicker";
        import {debounce, flattenDeep, values} from "lodash";
        import { format, formatISO, parseISO } from 'date-fns'
        import {ModelSelect, MultiSelect} from "vue-search-select";
        import id from 'date-fns/locale/id'

        export default {
            name: "giat-rutin",
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
                    isBusyPrint: false,
                    sortBy: "id",
                    sortDesc: true,
                    valueProgressUpload: 0,
                    rentangTanggal: null,
                    timePickerOptions: {
                        start: "00:00",
                        step: "00:30",
                        end: "23:30"
                    },
                    tableColumns: [
                        { key: 'checkbox', label: 'Checkbox'},
                        { key: 'index', label: 'No' },
                        { key: 'user.nrp', label: 'NRP' },
                        { key: 'nama', label: 'Nama' },
                        { key: 'wKegiatan', label: 'Waktu', formatter: v => format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}), sortable: true },
                        { key: 'judul', label: 'Kegiatan' },
                        { key: 'jenis', label: 'Jenis' },
                        { key: 'kuatPers', label: 'Kuat Pers', formatter: v => { return (v == null ? "0" : v) + " Personil" }, sortable: true },
                        { key: 'img', label: 'Dokumentasi' }
                    ],
                    indukOptions: [
                        {
                            text: "Semua Waktu",
                            value: 1
                        }, {
                            text: "Rentang Waktu",
                            value: 2
                        }
                    ],
                    typeOptions: [
                        {
                            text: "Semua Kegiatan",
                            value: 1
                        }, {
                            text: "Seleksi Kegiatan",
                            value: 2
                        }
                    ],
                    option: "",
                    tipeLaporan: [],
                    itemTipeLaporan: {},
                    jenisGiat: [],
                    lastSelectItem: {},
                    itemJenisGiat: [],
                    semua: "0",
                    checked: [],
                    unchecked: [],
                    type: "",
                    tableData: ""
                };
            },
            computed: {},
            methods: {
                providerLaporan(ctx) {
                    
                    let sortBy;

                    switch (ctx.sortBy) {
                        case "wKegiatan.date":
                            sortBy = "waktu_kegiatan";
                            break;
                        case "kuatPers":
                            sortBy = "kuat_pers";
                            break;
                        case "jenis":
                            sortBy = "jenis_kegiatan";
                            break;
                        default:
                            sortBy = ctx.sortBy;
                    }

                    var jenisGiat = []
                    this.jenisGiat.forEach(function (i) { jenisGiat.push(i.value) })
                    rentang = null
                    if(this.rentangTanggal != null) {
                        var rentang = [ new moment(this.rentangTanggal[0]).format(), new moment(this.rentangTanggal[1]).format() ]
                    }
                    let payload = {
                        page: ctx.currentPage,
                        filter: ctx.filter === "" ? null : ctx.filter,
                        option: this.option,
                        type: this.type,
                        jenis: jenisGiat,
                        rentang: rentang,
                        sort: sortBy !== null ? sortBy + ":" + (ctx.sortDesc ? "desc" : "asc") : "waktu_kegiatan:desc"
                    };

                    var promise = axios.post("export-laporan/show-data/1", payload)
                        .then(({ data: { data, meta: { pagination } } }) => {
                            this.totalRows = pagination.total
                            this.perPage = pagination.per_page
                            this.currentPage = pagination.current_page
                            this.tableData = data
                            this.checked = []
                            var self = this
                            data.forEach(function (key) {
                                if (self.unchecked.indexOf(key.id) > -1) {} else {
                                    self.checked.push(key.id)
                                }
                            })
                            return data
                        })
                        // .catch(({response}) => {
                        //     // Catch error
                        //     return response;
                        // });

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
                    this.type = ''
                    this.semua = '0'
                    this.$refs.tabelLaporan.refresh()
                },
                showDataType() {
                    this.checked = [];
                    this.unchecked = [];
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
                    if (this.option == "" || (this.option == 1 && this.type == "") || (this.option == 1 && this.type == "2" && this.jenisGiat.length == 0) || (this.option == 2 && this.rentangTanggal == "") || (this.option == 2 && this.type == "" && this.rentangTanggal == "") || (this.option == 2 && this.type == "2" && this.jenisGiat.length == 0 && this.rentangTanggal == "")) {
                        this.$toast.error("Silahkan lengkapi data jenis laporan", {layout: "topRight"});
                    } else {
                        this.isBusyPrint = true
                        var jenisGiat = []
                        this.jenisGiat.forEach(function (i) {
                            jenisGiat.push(i.value);
                        })
                        var data = {
                            id: this.unchecked,
                            range: [
                                new moment(this.rentangTanggal[0]).format(),
                                new moment(this.rentangTanggal[1]).format()
                            ],
                            jenis: jenisGiat
                        };
                        axios({
                            method: "POST",
                            url: "export-laporan/download/1",
                            data: data,
                            responseType: "blob"
                        })
                        .then(response => {
                            let url = window.URL.createObjectURL(new Blob([response.data], { type: 'data:application/vnd.ms-excel' }));
                            let filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '')
                            let link = document.createElement('a');
                            link.href = url;
                            link.setAttribute('download', filename);
                            document.body.appendChild(link);
                            link.click();
                            this.isBusyPrint = false
                        })
                        .catch(({ response: { status, data: { errors } } }) => {
                            this.isBusyPrint = false
                            if (status === 422) 
                                this.$toast.error(flattenDeep(values(errors)).join("<br>"), {layout: "topRight"});
                            }
                        );
                    }
                },
                fetchJenis() {
                    var promise = axios.get('form/jenis-giat')
                        .then(({data}) => {
                            var self = this
                            data.map((val) => {
                                self.itemJenisGiat.push({value: val.id, text: val.jenis})
                            })
                        })
                        .catch((error) => {})
                    }
            },
            mounted() {
                this.fetchJenis()
            },
            watch: {}
        };
    </script>

    <style scoped="scoped">
        img.preview-profil {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .ui.selection.dropdown {
            background: #868e96;
            color: #fff;
        }
        .default {
            color: #fff;
        }
    </style>