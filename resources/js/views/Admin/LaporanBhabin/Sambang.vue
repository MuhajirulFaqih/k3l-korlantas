<template>
    <div>
        <b-row>
            <b-col cols="6" md="6">
                <b-row>
                    <b-col cols="auto">
                        <h4>DDS/Sambang</h4>
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
				<template v-slot:cell(warga)="row">
		        	<button @click="showWarga(row.item.masyarakat)" class="btn btn-primary btn-sm">Lihat {{ row.item.masyarakat.length }} data warga</button>
		        </template>
            </b-table>

            <div class="loading" v-show="isBusy">
                <b-spinner variant="primary"></b-spinner>
            </div>
        </div>
		<b-modal ref="modalWarga" hide-footer title="Data Masyarakat">
	      	<div class="d-block beauty-scroll">
			    <b-list-group>
				  	<b-list-group-item v-for="(m, i) in masyarakat" :key="i" class="dark-li">
				  		<table border="0" style="font-size: 12px;">
				  			<tr><td>NIK</td><td width="80px"><center>:</center></td><td>{{ m.nik }}</td></tr>
				  			<tr><td>Nama</td><td width="80px"><center>:</center></td><td>{{ m.nama }}</td></tr>
				  			<tr>
				  				<td>TTL</td><td width="80px"><center>:</center></td>
				  				<td>{{ m.tempat_lahir }}, {{ m.tanggal_lahir }}</td>
				  			</tr>
				  			<tr>
				  				<td>Suku</td><td width="80px"><center>:</center></td>
				  				<td>{{ m.suku }}</td>
				  			</tr>
				  			<tr>
				  				<td>Agama</td><td width="80px"><center>:</center></td>
				  				<td>{{ m.agama }}</td>
				  			</tr>
				  			<tr>
				  				<td>Alamat</td><td width="80px"><center>:</center></td>
				  				<td>{{ m.alamat }}</td>
				  			</tr>
				  			<tr>
				  				<td>Pekerjaan</td><td width="80px"><center>:</center></td>
				  				<td>{{ m.pekerjaan }}</td>
				  			</tr>
				  			<tr>
				  				<td>No HP</td><td width="80px"><center>:</center></td>
				  				<td>{{ m.no_hp }}</td>
				  			</tr>
				  			<tr>
				  				<td>Status Keluarga</td><td width="80px"><center>:</center></td>
				  				<td>{{ m.status_keluarga }}</td>
				  			</tr>
				  		</table>
				  	</b-list-group-item>
				</b-list-group>
	      	</div>
	    </b-modal>
    </div>
</template>
<script>
    import moment from 'moment'
    import DatePicker from "vue2-datepicker";
    import {debounce, flattenDeep, values} from "lodash";
    import { format, formatISO, parseISO } from 'date-fns'
    import {ModelSelect, MultiSelect} from "vue-search-select";
	import id from 'date-fns/locale/id'
	import Swal from 'sweetalert2'

    export default {
        name: "sambang",
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
                    { key: 'index', label: 'No' },
                    { key: 'user.nrp', label: 'NRP' },
                    { key: 'user.nama', label: 'Nama' },
                    { key: 'jenis.jenis', label: 'Jenis' },
                    { key: 'lokasi', label: 'Lokasi' },
                    { key: 'uraian', label: 'Uraian' },
                    { key: 'waktu_kegiatan', label: 'Waktu', formatter: v => format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}), sortable: true },
                    { key: 'warga', label: 'Warga' },
                    { key: 'img', label: 'Dokumentasi' }
                ],
                masyarakat: [],
                option: 1,
                indukOptions: [
                    {
                        text: "Semua",
                        value: 1
                    }, {
                        text: "Rentang Waktu",
                        value: 2
                    }
                ],
                proses: false
            };
        },
        computed: {},
        methods: {
            providerLaporan(ctx) {
                let sortBy;

                switch (ctx.sortBy) {
                    case "jenis.jenis":
                        sortBy = "id_jenis";
                        break;
                    default:
                        sortBy = ctx.sortBy;
                }

                let payload = {
                    page: ctx.currentPage,
                    filter: ctx.filter === "" ? null : ctx.filter,
                    indikator: 1, //ID indikator (Sambang Dll)
                    type: this.option,
                    rentang: this.rentangTanggal,
                    sort: sortBy !== null ? sortBy + ":" + (ctx.sortDesc ? "desc" : "asc"): "waktu_kegiatan:desc"
                };

                var promise = axios
                    .get("laporan-kegiatan-bhabin/laporan", { params: payload } )
                    .then(({data: { data, meta: { pagination } }}) => {
                        this.totalRows = pagination.total;
                        this.perPage = pagination.per_page;
                        this.currentPage = pagination.current_page;
                        return data;
                    })
                    .catch(({response}) => {
                        // Catch error
                        return response;
                    });

                return promise;
            },
			
			showWarga (masyarakat) {
				this.masyarakat = masyarakat
				if(masyarakat.length > 0)
					this.$refs.modalWarga.show()
				else
					Swal.fire({
						title: 'Tidak ada data warga!',
						text: "",
						icon: 'error',
						showCancelButton: false,
						confirmButtonColor: '#2196f3',
						confirmButtonText: 'Oke',
					})
			},

            showData(val) {
                if(val == 1 || (val == 2 && this.rentangTanggal != null))
                    this.$refs.tabelLaporan.refresh();
            },

            onInputRentangTgl(val) {
                this.$refs.tabelLaporan.refresh();
            },
            printData() {
                if (this.option == "" || (this.option == 2 && this.rentangTanggal == "")) {
                    this.$toast.error("Silahkan lengkapi data jenis laporan", {layout: "topRight"});
                } else {
                    this.proses = true
                    var data = {
                        type: this.option,
                        rentang: this.rentangTanggal
                    };
                    axios({
                        method: "POST",
                        url: "laporan-kegiatan-bhabin/export/1",
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
                        this.proses = false
                    })
                    .catch(({ response: { status, data: { errors } } }) => {
                        this.proses = false
                        if (status === 422) 
                            this.$toast.error(flattenDeep(values(errors)).join("<br>"), {layout: "topRight"});
                        }
                    );
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
	.dark-li {
		color: #fff;
		background-color: #25293d;
	}
	.beauty-scroll {
		padding: 0;
		overflow-y: auto;
		overflow-x: hidden;
		border-right: solid 1px #e9ecef;
		height: 400px;
		&::-webkit-scrollbar-track {
			-webkit-box-shadow: inset 0 0 4px #757575;
			border-radius: 10px;
			background-color: #F5F5F5;
		}
		&::-webkit-scrollbar {
			width: 5px;
			background-color: #F5F5F5;
		}
		&::-webkit-scrollbar-thumb {
			border-radius: 10px;
			-webkit-box-shadow: inset 0 0 4px #757575;
			background-color: #757575;
		}
	}

</style>