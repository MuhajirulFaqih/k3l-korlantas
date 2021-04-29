<template>
    <div>
        <b-row>
            <b-col cols="6" md="6">
                <b-row>
                    <b-col cols="auto">
                        <h4>Sitkam</h4>
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
                    <b-col cols="5" md="5">
                        <model-select
                            @input="showData"
                            v-model="option"
                            :options="indukOptions"
                            placeholder="Pilih Cetak"/>
                    </b-col>
                    <b-col cols="5" md="5">
                        <b-row>
                            <b-col v-show="option == 2">
                                <date-picker
                                    v-model="rentangTanggal"
                                    @input="onInputRentangTgl"
                                    range="range"
                                    lang="id"
                                    placeholder="Pilih rentang tanggal"
                                    format="DD-MM-YYYY"></date-picker>
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

        const dateFnsBahasa = {
            locale: require("date-fns/locale/id")
        };

        export default {
            name: "giat-langkah-sitkam",
            components: {
                ModelSelect,
                DatePicker
            },
            data() {
                return {
                    totalRows: 0,
                    perPage: 10,
                    time3: "",
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
                            text: "Semua",
                            value: 1
                        }, {
                            text: "Rentang Waktu",
                            value: 2
                        }
                    ],

                    option: "",

                    tipeLaporan: [],
                    itemTipeLaporan: {},
                    jenisGiat: [],
                    itemJenisGiat: {},
                    semua: "0",
                    checked: [],
                    unchecked: [],
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
                        default:
                            sortBy = ctx.sortBy;
                    }

                    let payload = {
                        page: ctx.currentPage,
                        filter: ctx.filter === "" ? null : ctx.filter,
                        type: this.option,
                        rentang: this.rentangTanggal,
                        sort: sortBy !== null ? sortBy + ":" + (ctx.sortDesc ? "desc" : "asc"): "waktu_kegiatan:desc"
                    };

                    var promise = axios
                        .get("export-laporan/show-data/5", { params: payload } )
                        .then(({data: { data, meta: { pagination } }}) => {
                            this.totalRows = pagination.total;
                            this.perPage = pagination.per_page;
                            this.currentPage = pagination.current_page;
                            this.tableData = data;
                            this.checked = [];
                            var self = this;
                            data.forEach(function (key) {
                                if (self.unchecked.indexOf(key.id) > -1) {} else {
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

                showData() {
                    this.checked = [];
                    this.unchecked = [];
                    this.semua = "0";

                    if (this.option == 1) { this.semua = "1" }
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
                    if (this.option == "" || (this.option == 2 && this.rentangTanggal == "")) {
                        this.$toast.error("Silahkan lengkapi data jenis laporan", {layout: "topRight"});
                    } else {
                        this.isBusyPrint = true
                        var data = {
                            id: this.unchecked,
                            range: this.rentangTanggal
                        };
                        axios({
                            method: "POST",
                            url: "export-laporan/download/5",
                            data: data,
                            responseType: "blob"
                        }).then(response => {
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
                        // axios   .post(baseUrl + "/api/export-laporan/download/2", data, {
                        // headers: { Authorization: sessionStorage.getItem("token") }   })
                        // .then(response => {})   .catch(({ response: { status, data: { errors } } })
                        // => {     if (status === 422)
                        // this.$toast.error(flattenDeep(values(errors)).join("<br>"), {         layout:
                        // "topRight"       });   });
                    }
                }
            },
            mounted() {},
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