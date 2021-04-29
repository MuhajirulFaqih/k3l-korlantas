<template>
	<div>
		<b-row>
	    	<b-col cols="2" md="2">
				<b-row>
	    			<b-col><h4>Masyarakat</h4></b-col>
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
		          	placeholder="Cari NIK, Nama, Alamat, No telepon..."/>
		    </b-col>
		</b-row>

		<div class="position-relative">
			<b-table responsive
	               ref="table"
	               :busy.sync="isBusy"
	               :fields="tableColumns"
				   :items="providerMasyarakat"
	               :current-page="currentPage"
	               :per-page="perPage"
	               :filter="filter"
	               :sort-by.sync="sortBy"
	               :sort-desc.sync="sortDesc">
		        <template v-slot:cell(index)="data">
		          	{{ ((currentPage - 1) * 10) + data.index + 1 }}
		        </template>
			    <template v-slot:cell(aksi)="row">
			    	<b-button size="md" v-b-tooltip :title="'Edit data ' + row.item.nama" variant="info" @click="prepareEdit(row.item)">
			    		<ph-pencil class="phospor"/>
			    	</b-button>
			    	<b-button size="md" v-b-tooltip :title="'Hapus data ' + row.item.nama" variant="danger" @click="prepareDelete(row.item)">
			    		<ph-trash class="phospor"/>
			    	</b-button>
			    </template>
		    </b-table>

		    <div class="loading" v-show="isBusy">
		        <b-spinner variant="primary"></b-spinner>
		    </div>
	  	</div>

	  	<!-- Modal form -->
		<b-form>
			<b-modal ref="modalForm"
					no-close-on-backdrop
					no-close-on-esc
					title-tag="h4"
					size="lg"
					header-class="red"
					header-text-variant="white"
					title="Edit data masyarakat">
				<b-col cols="12">
			      	<b-form-group
			            horizontal
			            :label-cols="3"
			            breakpoint="md"
			            label="NIK">
							<b-form-input type="text" v-model="singleMasyarakat.nik" maxlength="16" />
			        </b-form-group>
			      	<b-form-group
			            horizontal
			            :label-cols="3"
			            breakpoint="md"
			            label="Nama">
							<b-form-input type="text" v-model="singleMasyarakat.nama" />
			        </b-form-group>
			        <b-form-group
			            horizontal
			            :label-cols="3"
			            breakpoint="md"
			            label="Alamat">
							<b-form-textarea v-model="singleMasyarakat.alamat"
						                   :rows="3"></b-form-textarea>
			        </b-form-group>
			        <b-form-group
			            horizontal
			            :label-cols="3"
			            breakpoint="md"
			            label="No Telepon">
							<b-form-input type="text" v-model="singleMasyarakat.no_telp" maxlength="13" />
			        </b-form-group>
			        <!-- <b-form-group
			            horizontal
			            :label-cols="3"
			            breakpoint="md"
			            label="Provinsi">
							<model-select v-model="provinsi" :options="provinsiOptions" placeholder="Pilih provinsi" @input="setProvinsi"/>
			        </b-form-group>
			        <b-form-group
			            horizontal
			            :label-cols="3"
			            breakpoint="md"
			            label="Kabupaten">
							<model-select v-model="kabupaten" :options="kabupatenOptions" placeholder="Pilih kabupaten" @input="setKabupaten"/>
			        </b-form-group>
			        <b-form-group
			            horizontal
			            :label-cols="3"
			            breakpoint="md"
			            label="Kecamatan">
							<model-select v-model="kecamatan" :options="kecamatanOptions" placeholder="Pilih kecamatan" @input="setKecamatan"/>
			        </b-form-group>
			        <b-form-group
			            horizontal
			            :label-cols="3"
			            breakpoint="md"
			            label="Desa/Kelurahan">
							<model-select v-model="singleMasyarakat.id_kel" :options="kelurahanOptions" placeholder="Pilih desa/kelurahan"/>
			        </b-form-group> -->
			        <b-form-group
					    horizontal
					    :label-cols="3"
					    breakpoint="md"
					    label="Foto lama"
					    v-if="this.singleMasyarakat.id">
							<h5 v-if="singleMasyarakat.foto == ''" class="mt-3"><b-badge variant="info">Belum di set</b-badge></h5>
							<b-img v-else :src="singleMasyarakat.foto" width="200" />
					</b-form-group>
			        <b-form-group
			            horizontal
			            :label-cols="3"
			            breakpoint="md"
			            label="Foto">
							<b-form-file ref="foto" id="foto" accept="image/*"/>
							<small v-if="this.singleMasyarakat.id">*) Kosongi jika foto tidak diganti</small>
			        </b-form-group>
				</b-col>
				<template slot="modal-footer">
					<b-btn variant="primary" @click="submitMasyarakat">Edit</b-btn>
					<b-btn variant="secondary" @click="$refs.modalForm.hide('cancel')">Batal</b-btn>
				</template>
			</b-modal>
		</b-form>
	</div>
</template>
<script>
	import format from 'date-fns/format'
	import { debounce, endsWith, flattenDepth, omit, flatMap, values, merge, chain } from 'lodash'
	import { ModelSelect, MultiSelect} from 'vue-search-select'
	import Swal from 'sweetalert2'

	const dateFnsBahasa = {
	    locale: require('date-fns/locale/id')
	}

	export default {
		name: 'masyarakat',
		components: {
			ModelSelect,
			MultiSelect,
		},
		data () {
			return {
				totalRows: 0,
				perPage: 10,
        		currentPage: 1,
        		filter: '',
        		filterDebounced: '',
        		isBusy: false,
        		sortBy: 'id',
				sortDesc: true,
				tableColumns: [
					{ key: 'index', label: 'No' },
					{ key: 'nik', label: 'NIK', sortable: true, thStyle: { width: '100px' } },
					{ key: 'nama', label: 'Nama', sortable: true },
					{ key: 'alamat', label: 'Alamat', sortable: true, thStyle: { width: '300px' } },
					{ key: 'no_telp', label: 'Nomor telepon', sortable: true },
					{ key: 'aksi', label: 'Aksi'}
				],
        		singleMasyarakat: {
        			id: '',
        			nik: '',
		    		nama: '',
        			alamat: '',
        			no_telp: '',
        			foto: '',
        			id_kel: '',
        		},
        		wilayah: [],
        		kecamatanOptions: [],
        		kelurahanOptions: [],
        		provinsi: null,
        		kabupaten: null,
        		kecamatan: null,
			}
		},
		computed: {
			provinsiOptions () {
		        return this.wilayah.map(o => { return {
		            text: o.nama, value: o.id_prov
		        } })
		    },
		    kabupatenOptions () {
		    	if(this.provinsi !== null) {
			    	var wilayah = this.wilayah.filter(({ id_prov }) => id_prov == this.provinsi)
			    	return wilayah[0].kabupaten.map(o => { return {
			            text: o.nama, value: o.id_kab
			        } })
		    	} 
				else {
		    		return []
		    	}
		    },
		},
		methods: {
			providerMasyarakat (ctx) {
				let sortBy

				switch(ctx.sortBy) {
					case 'kelurahan.nama':
						sortBy = 'id_kel'
						break
					default:
						sortBy = ctx.sortBy

				}

				let payload = {
					page: ctx.currentPage,
					filter: ctx.filter === '' ? null : ctx.filter,
					sort: (sortBy != null ? sortBy : 'id') + ':' + (ctx.sortDesc ? 'desc' : 'asc')
				}

				var promise = axios.get('masyarakat', {
						params: payload
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
		    prepareEdit (item) {
		    	// if(item.kelurahan != null) {
		    	// 	this.setKabupaten(parseInt(item.kelurahan.kecamatan.kabupaten.id_kab))
			    // 	this.setKecamatan(parseInt(item.kelurahan.kecamatan.id_kec))
		    	// }
		    	this.singleMasyarakat = {
		    		id: item.id,
		    		nik: item.nik,
		    		nama: item.nama,
        			alamat: item.alamat,
        			no_telp: item.no_telp,
        			foto: item.foto,
        			id_kel: null,
        			// id_kel: item.kelurahan != null ? item.kelurahan.id_kel : null,
		    	}
		    	// if(item.kelurahan != null) {
			    // 	this.provinsi = parseInt(item.kelurahan.kecamatan.kabupaten.id_prov)
			    // 	this.kabupaten = parseInt(item.kelurahan.kecamatan.kabupaten.id_kab)
			    // 	this.kecamatan = parseInt(item.kelurahan.kecamatan.id_kec)
			    // }
			    // else {
			    // 	this.provinsi = null
			    // 	this.kabupaten = null
			    // 	this.kecamatan = null
			    // }
		    	this.$refs.modalForm.show()
		    },
		    submitMasyarakat () {
		    	let data = new FormData()
		    	if(document.getElementById('foto').files[0])
					data.append('fotos', document.getElementById('foto').files[0])

		        let obj = this.singleMasyarakat

		        Object.keys(obj).forEach(function(key) {
		            data.append(key, obj[key]);
		        })
		    	axios.post('masyarakat/' + this.singleMasyarakat.id, data, {
	              	headers: { 'Content-Type': 'multipart/form-data' }
		        })
		        .then((response) => {
		        	this.$toast.success('Data masyarakat berhasil di perbarui', { layout: 'topRight' })
		        	this.$refs.modalForm.hide()
		        	this.resetSingleMasyarakat()
		        	this.refreshTable()
		        })
		        .catch(({ response: { status, data: { errors }}}) => {
	                if (status === 422)
	                	this.$toast.error(flattenDepth(values(errors)).join('<br>'), { layout: 'topRight' })
	            })
		    },
		    prepareDelete (item) {
		    	Swal.fire({
				  	title: 'Hapus data',
				  	text: "Apakah anda yakin menghapus data "+ item.nama +"?",
				  	icon: 'error',
				  	showCancelButton: true,
				  	confirmButtonColor: '#e53935',
				  	confirmButtonText: 'Hapus',
				  	cancelButtonText: 'Batal'
				}).then((result) => {
				 	if (result.value) {
					    axios.delete('masyarakat' + item.id, {
			              	headers: {
			              		'Content-Type': 'multipart/form-data' 
			              	}
				        })
				        .then((response) => {
				        	this.$toast.success('Data masyarakat '+ item.nama +' berhasil di hapus', { layout: 'topRight' })
				        	this.refreshTable()
				        })
				        .catch(({ response: { status, data: { errors }}}) => {
			                if (status === 422)
			                	this.$toast.danger('Terjadi kesalahan saat menghapus data', { layout: 'topRight' })
			            })
				  	}
				})
		    },
		    resetSingleMasyarakat () {
		    	this.singleMasyarakat = {
        			id: '',
		    		nik: '',
		    		nama: '',
        			alamat: '',
        			no_telp: '',
        			foto: '',
        			id_kel: '',
        		}
        		this.$refs.foto.reset();
		    },
		    fetchWilayah () {
				axios.get('provinsi')
		        .then((response) => {
					this.wilayah = response.data.provinsi
				})
			},
			setProvinsi () {
				this.kabuten = null
				this.kecamatan = null
				this.singleMasyarakat.id_kel = null
			},
			setKabupaten (kabupaten) {
				this.kecamatan = null
				this.singleMasyarakat.id_kel = null
				axios.get('kecamatan/' + kabupaten)
		        .then((response) => {
		        	this.kecamatanOptions = response.data.kecamatan.map(o => { return {
			            text: o.nama, value: o.id_kec
			        } })
				})
			},
			setKecamatan (kecamatan) {
				this.kelurahan = null
				axios.get('kelurahan/' + kecamatan)
		        .then((response) => {
		        	this.kelurahanOptions = response.data.kelurahan.map(o => { return {
			            text: o.nama, value: o.id_kel
			        } })
				})
			},
		    debounceFilter: debounce(function () {
		        this.filter = this.filterDebounced
		        this.currentPage = 1
			}, 500),
			refreshTable () {
				this.totalRows > this.perPage ? 
				(this.currentPage == 1 ? this.$refs.table.refresh() : this.currentPage = 1) 
				: this.$refs.table.refresh()
			}, 
		},
		watch: {
			filterDebounced (newFilter) {
		        this.debounceFilter()
		    },
		},
		mounted () {
			this.fetchWilayah()
		}
	}
</script>
<style scoped="">
	.ui.fluid.dropdown {
	  background: #868e96;
	  color: #fff;
	}
	.ui.search.dropdown > .text {
	  color: #fff;
	}
</style>