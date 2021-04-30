<template>
	<div>
		<b-row>
	    	<b-col cols="3" md="3">
				<b-row>
	    			<b-col cols="12"><h4>Absensi Personil</h4></b-col>
	    		</b-row>
			</b-col>
	    	<b-col cols="9" md="9">
		        <b-pagination
		          align="right"
		          :total-rows="totalRows"
		          v-model="currentPage"
		          :per-page="perPage"/>
		    </b-col>
		</b-row>
		<b-row>
			<b-col cols="3">
				<date-picker
                  	v-model="payload.tanggal"
                  	range
                  	lang="id"
                  	placeholder="Rentang tanggal"
                  	format="DD-MM-YYYY"
                  	style="width: 100%;"
                  	@input="filterData"
                ></date-picker>
			</b-col>
			<b-col cols="3">
				<model-select v-model="payload.id_kesatuan" :options="kesatuan" placeholder="Pilih kesatuan" @input="filterData"/>
			</b-col>
			<b-col cols="2">
				<b-form-input
		        	align="right"
		          	v-model="payload.nrp"
		          	@input="filterData"
		          	placeholder="Masukkan NRP..."/>
			</b-col>
			<b-col cols="4">
				<button class="btn btn-danger" @click="resetData">
	              	<ph-arrows-clockwise class="phospor"/> Reset
	            </button>
				<button class="btn btn-primary" v-if="isSearch" @click="prepareCetak" :disabled="isBusyPrint">
					<span v-if="isBusyPrint">Proses...</span>
                    <span v-else><ph-printer class="phospor"/> Cetak</span>
	            </button>
			</b-col>
		</b-row>
		<br/>
		<div class="position-relative">
			<b-table responsive
	               ref="tabelAbsensi"
	               :busy.sync="isBusy"
	               :fields="tableColumns"
				   :items="providerAbsensi"
	               :current-page="currentPage"
	               :per-page="perPage"
	               :filter="filter"
	               :sort-by.sync="sortBy"
	               :sort-desc.sync="sortDesc">
		        <template v-slot:cell(index)="data">
		          	{{ ((currentPage - 1) * 10) + data.index + 1 }}
		        </template>
		        <template v-slot:cell(nrp)="data">
		          	{{ data.item.personil.nrp }}
		        </template>
		        <template v-slot:cell(personil)="data">
		          	{{ data.item.personil.pangkat }}
		          	{{ data.item.personil.nama }} <br/>
		          	{{ data.item.personil.jabatan }}
		        </template>
		        <template v-slot:cell(kesatuan)="data">
		          	{{ data.item.personil.kesatuan }}
		        </template>
		    </b-table>

		    <div class="loading" v-show="isBusy">
		        <b-spinner variant="primary"></b-spinner>
		    </div>
	  	</div>
	</div>
</template>
<script>
	import { format, formatISO, parseISO } from 'date-fns'
	import { debounce, endsWith, flattenDepth, omit, flatMap, values, merge, chain } from 'lodash'
	import { ModelSelect, MultiSelect} from 'vue-search-select'
	import Swal from 'sweetalert2'
	import DatePicker from "vue2-datepicker"
	import id from 'date-fns/locale/id'

	export default {
		name: 'absensi-personil',
		components: {
			ModelSelect,
			MultiSelect,
			DatePicker
		},
		data () {
			return {
				totalRows: 0,
				perPage: 10,
        		currentPage: 1,
        		filter: '',
        		isBusy: false,
        		isBusyPrint: false,
        		sortBy: 'id',
        		sortDesc: true,
				rentangTanggal: null,
				tableColumns: [
                    { key: 'index', label: 'No' },
                    { key: 'nrp', label: 'NRP', sortable: true },
                    { key: 'personil', label: 'Nama personil', sortable: true },
                    { key: 'kesatuan', label: 'Kesatuan', sortable: true },
                    { key: 'waktu_mulai', label: 'Datang', formatter: v => v != null ? format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}) : "-", sortable: true },
                    { key: 'waktu_selesai', label: 'Pulang', formatter: v => v != null ? format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}) : "-", sortable: true },
                ],
        		shortcuts: [
			        {
			         	text: "Today",
			        },
		      	],
		        kesatuan: [],
		        payload: {
		        	id_kesatuan: null,
		        	tanggal: [ null, null ],
		        	nrp: null,
		        },
		        isSearch: false,
			}
		},
		computed: {
			
		},
		methods: {
			providerAbsensi (ctx) {
				let sortBy

				switch(ctx.sortBy) {
					case 'nrp':
						sortBy = 'id_personil'
						break
					case 'personil':
						sortBy = 'id_personil'
						break
					case 'kesatuan':
						sortBy = 'id_personil'
						break
					default:
						sortBy = ctx.sortBy
				}
				let payload = {
					page: ctx.currentPage,
					id_kesatuan: this.payload.id_kesatuan,
					rentangTanggal: this.payload.tanggal,
					nrp: this.payload.nrp,
					sort: (sortBy != null ? sortBy : 'id') + ':' + (ctx.sortDesc ? 'desc' : 'asc')
				}

				var promise = axios.post('absensi', payload)
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
		    fetchKesatuan(){
				var promise = axios.get('kesatuan')
				.then(({data: {data}}) => {
					this.kesatuan = data.map((val) => {
						return {value: val.id, text: val.kesatuan}
					})
				})
				.catch((error) => {
					
				})
			},
		    filterData () {
		    	this.isSearch = true
		    	this.$refs.tabelAbsensi.refresh()
		    },
		    resetData () {
		    	this.payload = {
	    			id_kesatuan: null,
	    			tanggal: [null, null],
	    			nrp: null
	    		}
	    		this.isSearch = false
	    		this.$refs.tabelAbsensi.refresh()
		    },
		    prepareCetak () {
		    	if(this.payload.id_kesatuan == null && this.payload.tanggal[0] == null && this.payload.nrp == null) {
		    		this.$toast.error('Mohon untuk memilih indikator pencarian terlebih dahulu')
		    	} 
		    	else {
					this.isBusyPrint = true
		    		axios({
			          	method: "POST",
			          	url: "absensi/export",
			          	data: this.payload,
			          	responseType: "blob"
			        })
			        .then(response => {
						this.isBusyPrint = false
		            	let url = window.URL.createObjectURL(new Blob([response.data], { type: 'data:application/vnd.ms-excel' }));
                        let filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '')
                        let link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', filename);
                        document.body.appendChild(link);
                        link.click();
			        })
			        .catch(({ response: { status, data: { errors } } }) => {
						this.isBusyPrint = false
						this.$toast.error('Data gagal di ekspor', {
							layout: "topRight"
						});
			        });
		    	}
		    }
		},
		mounted () {
			this.fetchKesatuan()
		}
	}
</script>
<style scoped>
	.ui.selection.dropdown {
	  	background: #868e96;
	  	color: #fff;
	}
	.default {
	  	color: #fff;
	}
</style>