<template>
	<div>
		<b-row>
	    	<b-col cols="2" md="2">
	    		<b-row>
	    			<b-col cols="6"><h4>Personil</h4></b-col>
	    			<b-col cols="6">
	    				<b-button @click="$refs.modalTambah.show()" variant="primary" size="sm">
							<ph-plus class="phospor"/> Tambah
						</b-button>
	    			</b-col>
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
				<form @submit.prevent="search">
					<b-input-group align="right">
						<b-form-input
							align="right"
							class="e-form"
							@keyup="whenSearch"
							v-model="filterDebounced"
							placeholder="Cari NRP, Nama, Jabatan, Status dinas.."/>
						<b-input-group-append>
							<button class="btn e-btn e-btn-primary" type="submit">
								<ph-magnifying-glass class="phospor"/>
							</button>
						</b-input-group-append>
					</b-input-group>
				</form>
		    </b-col>
		</b-row>

		<div class="position-relative mt-2">
			<b-table responsive
	               ref="table"
	               :busy.sync="isBusy"
	               :fields="tableColumns"
				   :items="providerPersonil"
	               :current-page="currentPage"
	               :per-page="perPage"
	               :filter="filter"
	               :sort-by.sync="sortBy"
	               :sort-desc.sync="sortDesc">

		        <template #cell(index)="data">
		          	{{ ((currentPage - 1) * 10) + data.index + 1 }}
		        </template>
			    <template #cell(aksi)="row">
					<div class="dropdown-container">
						<b-dropdown text="Pilih" class="btn-dropdown" boundary>
							<b-dropdown-item @click="prepareUbah(row.item)">
								<ph-pencil class="phospor"/> Ubah
							</b-dropdown-item>
							<b-dropdown-item @click="prepareReset(row.item)">
								<ph-arrows-clockwise class="phospor"/> Reset password
							</b-dropdown-item>
							<b-dropdown-item @click="prepareBeat(row.item)">
								<span v-if="row.item.is_patroli_beat == 1">
									<ph-check-circle class="phospor text-success"/> Patroli Beat Aktif
								</span>
								<span v-else>
									<ph-x-circle class="phospor text-danger"/> Patroli Beat Nonaktif
								</span>
							</b-dropdown-item>
							<b-dropdown-item @click="updatePtt(row.item)">
								<span v-if="row.item.ptt_ht">
									<ph-microphone class="phospor text-success"/> PTT HT Aktif
								</span>
								<span v-else>
									<ph-microphone-slash class="phospor text-danger"/> PTT HT Nonaktif
								</span>
							</b-dropdown-item>
							<b-dropdown-item @click="prepareDelete(row.item)">
								<ph-trash class="phospor"/> Hapus
							</b-dropdown-item>
						</b-dropdown>
					</div>
			    </template>
		    </b-table>

		    <div class="loading" v-show="isBusy">
		        <b-spinner variant="primary"></b-spinner>
		    </div>

			<b-modal
				hide-header-close
				no-close-on-backdrop
				no-close-on-esc
				ok-title="Simpan"
				cancel-title="Batal"
				@ok="submitPersonil"
				header-class="bg-primary"
				header-text-variant="white"
				@hide="resetFormTambah"
				ref="modalTambah"
				size="xl">
				<template #modal-header>
					<h3 class="modal-title">Personil</h3>
				</template>
				<b-row>
					<b-form-group label-class="h3" class="col-md-6 col-xl-12">
                        <b-form-group label="NRP" label-class="h6 mt2" label-cols="2">
                            <b-input-group>
                                <b-form-input v-model="payload.nrp" type="text"/>
                                <b-input-group-append>
									<b-button variant="primary" @click="fetchPersonil" :disabled="isBusySearch">
										<b-spinner style="width: 1rem; height: 1rem; top: -3px; position: relative;" variant="light" v-if="isBusySearch"/>
										<ph-magnifying-glass class="phospor" v-else/> 
										Cari
									</b-button> 
								</b-input-group-append>
                            </b-input-group>
                        </b-form-group>
						<b-form-group label="Nama" label-class="h6 mt2" label-cols="2">
							<b-form-input v-model="payload.nama" type="text" disabled/>
						</b-form-group>
						<b-form-group label="Pangkat" label-class="h6 mt2" label-cols="2">
							<b-form-select v-model="payload.id_pangkat" :options="pangkat" placeholder="Pilih pangkat" disabled/>
						</b-form-group>
						<b-form-group label="Kesatuan" label-class="h6 mt2" label-cols="2">
                            <b-form-input list="datalist-kesatuan" v-model="inputKesatuan" disabled/>
                            <b-form-datalist id="datalist-kesatuan" :options="optionsKesatuan"></b-form-datalist>
						</b-form-group>
						<b-form-group label="Jabatan" label-class="h6 mt2" label-cols="2">
                            <b-form-input list="datalist-jabatan" v-model="inputJabatan" disabled/>
                            <b-form-datalist id="input-list" :options="optionsJabatan"></b-form-datalist>
						</b-form-group>
                        <b-form-group label="No Handphone" label-class="h6 mt2" label-cols="2">
                            <b-form-input v-model="payload.no_telp" type="text"/>
                        </b-form-group>
                        <b-form-group label="Alamat" label-class="h6 mt2" label-cols="2">
                            <b-form-textarea rows="4" v-model="payload.alamat" type="text"/>
                        </b-form-group>
						<b-form-group label="Foto" label-class="h6 mt2" label-cols="2">
							<b-form-file @change="onFotoChange" accept="image/*"/>
							<b-progress class="mt-1" :max="100" show-value v-if="valueProgressUpload > 0">
								<b-progress-bar :value="valueProgressUpload" variant="success"/>
							</b-progress>
							<img class="mt-1 preview-profil" :src="srcProfil" @error="imgProfilError" width="50%" style="margin: 0 auto"/>
						</b-form-group>
					</b-form-group>
				</b-row>

				<template #modal-footer>
					<b-btn variant="primary" @click="submitPersonil">Simpan</b-btn>
					<b-btn variant="secondary" @click="$refs.modalTambah.hide('cancel')">Batal</b-btn>
				</template>
			</b-modal>
	  	</div>
	</div>
</template>
<script>
	import format from 'date-fns/format'
	import { ModelSelect, MultiSelect} from 'vue-search-select'
	import { debounce, flattenDeep, values } from 'lodash'
	import Swal from 'sweetalert2'

	const dateFnsBahasa = {
	    locale: require('date-fns/locale/id')
	}

	export default {
		name: 'personil',
		components: {
			ModelSelect,
			MultiSelect
		},
		data () {
			return {
				totalRows: 0,
				perPage: 10,
        		currentPage: 1,
        		filter: '',
        		filterDebounced: '',
        		isBusy: false,
        		isBusySearch: false,
        		sortBy: 'id_jabatan',
				sortDesc: false,
				valueProgressUpload: 0,
        		tableColumns: [
        			{
						key: 'index',
			            label: 'No.'
			        },
		          	{
						key: 'nrp',
		            	label: 'NRP',
		            	sortable: true
		          	},
		          	{
						key: 'nama',
						label: 'Nama',
						sortable: true
					},
					{
						key: 'jabatan',
		            	label: 'Jabatan',
		            	sortable: true
		          	},
		          	{
		          		key: 'dinas.kegiatan',
		            	label: 'Status Dinas',
		            	sortable: true
		          	},
		          	{
		          		key: 'aksi',
						label: 'Aksi',
						thStyle: { width: '200px' }
		          	},
				],
                inputKesatuan: null,
                inputJabatan: null,
				payload: {
					id: null,
					nama: null,
					nrp: null,
					id_jabatan: null,
					id_pangkat: null,
					id_kesatuan: null,
					kelamin: null,
					alamat: null,
                    no_telp: null,
					foto: null,
					isBhabin: false,
					id_kelurahan: []
				},
				srcProfil: null,
				itemsKelurahan: [],
				pangkat: [],
				jabatan: [],
				kesatuan: [],
                optionsKesatuan: [],
                optionsJabatan: [],
				kelurahan: []
			}
		},
		methods: {
			providerPersonil (ctx) {
				let sortBy

				switch(ctx.sortBy) {
					case 'nrp':
						sortBy = 'nrp'
						break
					case 'nama':
						sortBy = 'nama'
						break
					case 'jabatan':
						sortBy = 'id_jabatan'
						break
					case 'dinas.kegiatan':
						sortBy = 'status_dinas'
						break
					default:
						sortBy = 'id'
				}

				let payload = {
					page: ctx.currentPage,
					filter: ctx.filter === '' ? null : ctx.filter,
					sort: sortBy !== null ? (sortBy + ':' + (ctx.sortDesc ? 'desc' : 'asc')) : 'wAgenda:desc'
				}

				var promise = axios.get('personil', { params: payload })
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
            fetchPersonil(e){
				this.isBusySearch = true
			    axios.get("personil/fetch?nrp="+this.payload.nrp, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        }
                    })
                    .then(({data: {data}}) => {
						this.isBusySearch = false
						if(data == null) {
							this.$toast.error("Personil tidak ditemukan")	
							return
						}
                        this.payload.nama = data.nama
                        this.payload.no_telp = data.handphone
                        this.payload.id_kesatuan = data.id_kesatuan
                        this.payload.id_pangkat = data.id_pangkat
                        this.payload.id_jabatan = data.id_jabatan
                        this.inputKesatuan = data.kesatuan_lengkap
                        this.inputJabatan = data.jabatan
                        this.srcProfil = data.foto_file
                        this.payload.foto = data.foto_file
                    })
                    .catch(({response}) => {
						this.isBusySearch = false
                        if (response.status == 404)
                            this.$toast.error("Personil tidak ditemukan")
                    })
            },
			onFotoChange(e){
				var foto = e.target.files[0]
				//srcProfil = URL.createObjectURL(foto)

				var formData = new FormData()
				formData.append("foto", foto, foto.name)

				axios.post(baseUrl+"/api/personil/foto", formData, {
					headers: {
						'Content-Type': 'multipart/form-data',
						'Accept': 'application/json'
					},
					onUploadProgress: function (progressEvent) {
						this.valueProgressUpload = (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
					}.bind(this)
				})
				.then(({data}) => {
					console.log(data)
					this.valueProgressUpload = 0
					if ('error' in data){
						this.$toast.error(data.error)
					}
					else if('success' in data){
						this.srcProfil = baseUrl+"/api/upload/"+data.foto
						this.payload.foto = data.foto
					}
				})
				.catch(({response}) => {
					this.valueProgressUpload = 0
				})
			},
			submitPersonil(e){
				e.preventDefault()

				if (this.payload.id === null) {
					axios.post('personil', this.payload)
					.then(({data}) => {
						this.$toast.success('Personil berhasil ditambah')
						this.$refs.modalTambah.hide()
						this.refreshTable()
					})
					.catch((error) => {
						console.log(error)
						if (error.response) {
							if (error.response.status === 422) {
								this.$toast.error(flattenDeep(values(error.response.data.errors)).join('<br>'))
							}
						}
					})
				} else {
					axios.post('personil/'+this.payload.id+'/edit', this.payload)
					.then(({data}) => {
						if ('error' in data)
							this.$toast.error(data.error)
						else if('success' in data){
							this.$toast.success('Data personil berhasil diubah')
							this.$refs.modalTambah.hide()
                            this.refreshTable()
						}
					})
					.catch(({ response: { status, data: { errors }}}) => {
	                if (status === 422)
	                	this.$toast.error(flattenDeep(values(errors)).join('<br>'))
	            	})
				}

			},
			imgProfilError (e){
				this.srcProfil = baseUrl+"/api/upload/personil/pocil.jpg"
			},
			resetFormTambah(){
				this.payload = {
					id: null,
					nama: null,
					nrp: null,
					id_jabatan: null,
					id_pangkat: null,
					id_kesatuan: null,
					kelamin: null,
					alamat: null,
					foto: null,
					isBhabin: false,
					id_kelurahan: [],
                    no_telp: null
				}
				this.inputJabatan = null
                this.inputKesatuan = null
				this.srcProfil = null
				this.itemsKelurahan = []
			},
            prepareBeat(item){
			    var promise = axios.get('personil/'+item.id+'/beat')
				.then(({ data }) => {
					if ('success' in data){
						this.$toast.success('Status patroli beat personil '+ item.nama +' berhasil di ubah')
						this.refreshTable()
					}
					else {
						this.$toast.error('Status patroli beat personil '+ item.nama +' gagal di ubah')
					}
				})
			},
            updatePtt(item){
			    var promise = axios.get('personil/'+item.id+'/ptt')
				.then(({ data }) => {
					if ('success' in data){
						this.$toast.success('PTT HT personil '+ item.nama +' berhasil di ubah')
						this.refreshTable()
					}
					else {
						this.$toast.error('PTT HT personil '+ item.nama +' gagal di ubah')
					}
				})
			},
			prepareUbah({ id }) {
				var promise = axios.get('personil/'+id)
				.then(({data : { data }}) => {
					this.payload.id = data.id
					this.payload.nama = data.nama
					this.payload.nrp = data.nrp
					this.payload.id_jabatan = data.id_jabatan
					this.payload.id_pangkat = data.id_pangkat
					this.payload.id_kesatuan = data.id_kesatuan
					this.payload.kelamin = data.kelamin
					this.payload.alamat = data.alamat
					this.payload.foto = null
					this.payload.isBhabin = data.isBhabin
					this.payload.id_kelurahan= data.kelurahan == null ? [] : data.kelurahan

                    this.inputKesatuan = data.kesatuan_lengkap
                    this.inputJabatan = data.jabatan

					this.srcProfil = data.foto

					this.itemsKelurahan = this.kelurahan.filter((val) => this.payload.id_kelurahan.indexOf(val.value) > -1)

					this.$refs.modalTambah.show()
				})
			},
			prepareReset (item) {
				Swal.fire({
		            title: 'Reset password',
		            text: "Apakah anda yakin mereset password "+ item.nama +"?",
		            icon: 'warning',
		            showCancelButton: true,
		            confirmButtonColor: '#2196f3',
		            confirmButtonText: 'Reset',
		            cancelButtonText: 'Batal'
		        }).then((result) => {
		          if (result.value) {
		              axios.post('personil/reset_password', { id: item.id })
		                .then((response) => {
		                  this.$toast.success('Password personil '+ item.nama +' berhasil di reset')
		                  this.refreshTable()
		                })
		                .catch(({ response: { status, data: { errors }}}) => {
		                      if (status === 422)
		                        this.$toast.danger('Terjadi kesalahan saat menghapus data')
		                })
		            }
		        })
			},
			prepareDelete (item) {
				Swal.fire({
		            title: 'Hapus personil',
		            text: "Apakah anda yakin menghapus data personil "+ item.nama +"?",
		            icon: 'error',
		            showCancelButton: true,
		            confirmButtonColor: '#dc3545',
		            confirmButtonText: 'Hapus',
		            cancelButtonText: 'Batal'
		        }).then((result) => {
		          if (result.value) {
		              axios.delete('personil/' + item.id)
		                .then((response) => {
		                  this.$toast.success('Data personil '+ item.nama +' berhasil di hapus')
		                  this.refreshTable()
		                })
		                .catch(({ response: { status, data: { errors }}}) => {
		                      if (status === 422)
		                        this.$toast.danger('Terjadi kesalahan saat menghapus data')
		                })
		            }
		        })
			},
			onSelectKelurahan(items, lastSelectItem){
				this.itemsKelurahan = items
				this.payload.id_kelurahan = items.map((val) => val.value)
			},
			fetchJabatan(id_kesatuan = null){
				var promise = axios.get('jabatan?id_kesatuan='+id_kesatuan)
				.then(({data: {data}}) => {
				    this.jabatan = data
					this.optionsJabatan = data.map((val) => {
						return val.jabatan
					})
				})
				.catch((error) => {

				})
			},
			fetchPangkat(){
				var promise = axios.get('pangkat')
				.then(({data: {data}}) => {
					this.pangkat = data.map((val) => {
						return {value: val.id, text: val.pangkat}
					})
				})
				.catch((error) => {

				})
			},
			fetchKesatuan(){
				var promise = axios.get('kesatuan')
				.then(({data: {data}}) => {
				    this.kesatuan = data
					this.optionsKesatuan = data.map((val) => {
						return val.kesatuan_lengkap
					})
				})
				.catch((error) => {

				})
			},
			fetchWilKel(){
				var promise = axios.get('wilayah')
				.then(({data : { data }}) => {
					var kels = []
					data.forEach((kec) => {
						kec.kelurahan.forEach((kel) => {
							kels.push({value: kel.id_kel, text: kel.jenis.nama+' '+ kel.nama+' Kec. '+kec.nama})
						})
					})
					this.kelurahan = kels
				})
				.catch((error) => {

				})

			},
            setKesatuan(newKesatuan){
			    const selectedKesatuan = this.kesatuan.find(o => o.kesatuan_lengkap === newKesatuan)
                if (selectedKesatuan){
                    this.payload.id_kesatuan = selectedKesatuan.id
                    this.fetchJabatan(this.payload.id_kesatuan)
                }

            },
            setJabatan(newJabatan){
                const selectedJabatan = this.jabatan.find(o => o.jabatan === newJabatan)
                if (selectedJabatan){
                    this.payload.id_jabatan = selectedJabatan.id
                }

            },
            search: debounce(function () {
				this.filter = this.filterDebounced
				this.currentPage = 1
			}, 500),
			whenSearch () {
				if(this.filterDebounced == '') {
					this.search()
				}
			},
			refreshTable () {
				this.totalRows > this.perPage ? 
				(this.currentPage == 1 ? this.$refs.table.refresh() : this.currentPage = 1) 
				: this.$refs.table.refresh()
			},
		},
		mounted(){
			this.fetchJabatan()
			this.fetchPangkat()
			this.fetchKesatuan()
			/*this.fetchWilKel()*/
		},
		watch: {
			inputKesatuan: {
			    handler(old_kesatuan, new_kesatuan){
			        this.setKesatuan(new_kesatuan)
                }
            },
            inputJabatan: {
			    handler(old_jabatan, new_jabatan) {
			        this.setJabatan(new_jabatan)
                }
            }
		}
	}
</script>

<style scoped>
img.preview-profil {
	display:block;
  	margin-left:auto;
  	margin-right:auto;
}
</style>
