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
                  	:shortcuts="shortcuts"
                  	range
                  	lang="en"
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
	              	<icon name="history"/> Reset
	            </button>
				<button class="btn btn-primary" v-if="isSearch" @click="prepareCetak">
	              	<icon name="print"/> Cetak
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
		        <template slot="index" slot-scope="data">
		          	{{ ((currentPage - 1) * 10) + data.index + 1 }}
		        </template>
		        <template slot="nrp" slot-scope="data">
		          	{{ data.item.personil.nrp }}
		        </template>
		        <template slot="personil" slot-scope="data">
		          	{{ data.item.personil.pangkat }}
		          	{{ data.item.personil.nama }} <br/>
		          	{{ data.item.personil.jabatan }}
		        </template>
		        <template slot="kesatuan" slot-scope="data">
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
	import format from 'date-fns/format'
	import { debounce, endsWith, flattenDepth, omit, flatMap, values, merge, chain } from 'lodash'
	import { ModelSelect, MultiSelect} from 'vue-search-select'
	import Swal from 'sweetalert2'
	import DatePicker from "vue2-datepicker"

	const dateFnsBahasa = {
	    locale: require('date-fns/locale/id')
	}

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
        		sortBy: 'id',
        		sortDesc: true,
        		rentangTanggal: null,
        		tableColumns: {
        			index: {
			            label: 'No.'
			        },
		          	nrp: {
		            	label: 'NRP',
		            	sortable: true
		          	},
		          	personil: {
		            	label: 'Nama Personil',
		            	sortable: true
		          	},
					kesatuan: {
		            	label: 'Kesatuan',
		            	sortable: true
		          	},
		          	waktu_mulai: {
		            	label: 'Datang',
		            	sortable: true,
		            	formatter: v => {
				            return format(v, "D MMMM YYYY H:m:s", dateFnsBahasa);
				        },
		          	},
		          	waktu_selesai: {
		            	label: 'Pulang',
		            	sortable: true,
		            	formatter: v => {
				            return v != null ? format(v, "D MMMM YYYY H:m:s", dateFnsBahasa) : '-';
				        },
		          	},
        		},
        		shortcuts: [
			        {
			         	test: "Today",
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

				var promise = axios.post(baseUrl+'/api/absensi', payload, {
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
		    		this.$noty.error('Mohon untuk memilih indikator pencarian terlebih dahulu')
		    	} 
		    	else {
		    		axios({
			          	method: "POST",
			          	headers: { Authorization: sessionStorage.getItem("token") },
			          	url: baseUrl + "/api/absensi/export",
			          	data: this.payload,
			          	responseType: "blob"
			        })
			        .then(response => {
		            	let url = window.URL.createObjectURL(new Blob([response.data]));
		            	let link = document.createElement("a");
		            	let myDate = new Date();
		            	let month = ("0" + (myDate.getMonth() + 1)).slice(-2);
		            	let date = ("0" + myDate.getDate()).slice(-2);
		            	let year = myDate.getFullYear();
		            	let fullDate = year + "-" + month + "-" + date;
		            	link.href = url;
		            	link.setAttribute("download", "Absensi_" + fullDate + ".xlsx");
		            	document.body.appendChild(link);
		            	link.click();
			        })
			        .catch(({ response: { status, data: { errors } } }) => {
			            	this.$noty.error('Data gagal di ekspor', {
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