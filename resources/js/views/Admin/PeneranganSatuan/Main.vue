<template>
	<div>
		<b-row>
	    	<b-col cols="2" md="2">
	    		<b-row>
	    			<b-col cols="6"><h4>Penerangan Satuan</h4></b-col>
	    			<b-col cols="6">
	    				<b-button @click="$refs.modalTambah.show()" variant="primary" size="sm">
							<icon name="plus"/> Tambah
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
		    	<b-form-input
		        	align="right"
		          	v-model="filterDebounced"
		          	placeholder="Cari Penerangan satuan..."/>
		    </b-col>
		</b-row>

		<div class="position-relative mt-2">
			<b-table responsive
	               ref="tabelPeneranganSatuan"
	               :busy.sync="isBusy"
	               :fields="tableColumns"
				   :items="providerPeneranganSatuan"
	               :current-page="currentPage"
	               :per-page="perPage"
	               :filter="filter"
	               :sort-by.sync="sortBy"
	               :sort-desc.sync="sortDesc">

		        <template slot="index" slot-scope="data">
		          	{{ ((currentPage - 1) * 10) + data.index + 1 }}
		        </template>
				<template slot="jenis" slot-scope="row">
                    <icon :name="row.item.type === 'video' ? 'video' : (row.item.type === 'image' ? 'images' : 'file-pdf')"/> {{ row.item.type }}
				</template>
			    <template slot="aksi" slot-scope="row">
		            <b-button size="md" variant="danger" @click="prepareDelete(row.item)">
		              <icon name="trash"/>
		            </b-button>
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
				@ok="submitPeneranganSatuan"
				@hide="resetFormTambah"
				ref="modalTambah"
				size="xl">
				<template slot="modal-header">
					<h3 class="modal-title">Tambah Penerangan Satuan</h3>
				</template>
				<b-row>
					<b-form-group label-class="h3" class="col-md-6 col-xl-12">
						<b-form-group label="Judul" label-class="h6 mt2" label-cols="2">
							<b-form-input v-model="payload.judul" type="text" />
						</b-form-group>
                        <b-form-group label="Jenis" label-class="h6 mt2" label-cols="2">
                            <b-form-radio-group id="radioType" v-model="payload.type" :options="radioTypeOptions" name="radioType"></b-form-radio-group>
                        </b-form-group>
                        <div v-if="payload.type != 'video'">
                            <input type="file" ref="fileUpload" name="image" style="display:none" :accept="payload.type == 'image' ? 'image/*' : 'application/pdf'" multiple @change="onChange" />
                            <h5>Upload <icon
                                    name="upload"
                                    style="cursor: pointer"
                                    :disabled="isFormBusy"
                                    @click.native="handlerFileUpload" /></h5>
                            <b-progress class="mt-1" :max="100" show-value v-if="isFormBusy">
                                <b-progress-bar :value="valueProgressUpload" variant="success"/>
                            </b-progress>
                            <p v-if="!files.length || !payload.files.length" class="text-muted">Belum ada file yang dipilih.</p>
                            <ul class="list-unstyled">
                                <li v-for="file in payload.files" :key="'file-'+file.name" class="d-flex justify-content-between align-items-baseline mb-2">
                                    {{file.name}}
                                </li>
                                <li v-for="file in files" :key="'payload-'+file.id" class="d-flex justify-content-between align-items-baseline mb-2">
                                    {{file.file.replace(baseUrl+'/api/upload/penerangan_satuan/', '')}}
                                </li>
                            </ul>
                        </div>
                        <div v-else>
                            <b-form-group label="Youtube Url Video">
                                <b-form-input v-model="video" type="text" placeholder="https://youtube.com/watch?v=wG4sa0VDdUx"/>
                            </b-form-group>
                        </div>
					</b-form-group>
				</b-row>
				
				<template slot="modal-footer">
					<b-btn variant="primary" @click="submitPeneranganSatuan">Simpan</b-btn>
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
		name: 'PeneranganSatuan',
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
                isFormBusy: false,
        		sortBy: 'judul',
				sortDesc: false,
				valueProgressUpload: 0,
                files: [],
                video: null,
                radioTypeOptions: [
                    {text: 'Gambar', value: 'image'},
                    {text: 'Pdf', value: 'pdf'},
                    {text: 'Video', value: 'video'}
                ],
        		tableColumns: {
        			index: {
			            label: 'No.'
			        },
		          	jenis: {
		            	label: 'Jenis',
		            	sortable: false
		          	},
		          	judul: {
						label: 'Judul',
						sortable: true
					},
		          	aksi: {
		            	label: 'Aksi',
		          	},
				},
				payload: {
					id: null,
					judul: null,
					type: 'image',
					files: []
				},
				srcProfil: null,
				itemsKelurahan: [],
				pangkat: [],
				jabatan: [],
				kesatuan: [],
				kelurahan: []
			}
		},
		methods: {
			providerPeneranganSatuan (ctx) {
				let sortBy

				let payload = {
					page: ctx.currentPage,
					filter: ctx.filter === '' ? null : ctx.filter,
					sort: ctx.sortBy !== null ? (ctx.sortBy + ':' + (ctx.sortDesc ? 'desc' : 'asc')) : 'judul:desc'
				}

				var promise = axios.get(baseUrl+'/api/penerangan-satuan', {
						params: payload,
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
            handlerFileUpload(){
			  this.$refs.fileUpload.click();
            },
            onChange (e) {
			    this.payload.files = e.target.files

                /*var formData = new FormData()
                formData.append("files", file, file.name)
                formData.append("type", this.payload.type)
                this.isFormBusy = true

                axios.post(baseUrl+"/api/penerangan-satuan/upload", formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Authorization': sessionStorage.getItem('token'),
                            'Accept': 'application/json'
                        },
                        onUploadProgress: function (progressEvent) {
                            this.valueProgressUpload = (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
                        }.bind(this)
                    })
                    .then(({data}) => {
                        console.log(data)
                        this.isFormBusy = false
                        this.valueProgressUpload = 0
                        this.files.push(data.data)
                    })
                    .catch((err) => {
			            this.isFormBusy = false
                        this.valueProgressUpload = 0
                    })*/
            },
			submitPeneranganSatuan(e){
				e.preventDefault()

				if (this.payload.id === null) {
				    var payload = null
                    var headers  = {}
                    if (this.payload.type == 'video'){
				        headers = {
				            'Authorization' : sessionStorage.getItem('token'),
                            'Accept': 'application/json'
                        }
                        payload = {
				            judul: this.payload.judul,
                            jenis: this.payload.type,
                            files: [this.video]
                        }
                    }  else {
                        var formData = new FormData()
                        formData.append('jenis', this.payload.type)
                        formData.append('judul', this.payload.judul)
                        for (var i = 0; i < this.payload.files.length; i++){
                            formData.append("files[]", this.payload.files[i])
                        }

                        payload = formData

                        headers = {
                            'Content-Type': 'multipart/form-data',
                            'Authorization': sessionStorage.getItem('token'),
                            'Accept': 'application/json'
                        }
                    }

					axios.post(baseUrl+'/api/penerangan-satuan', payload, {
                        headers,
                        onUploadProgress: function (progressEvent) {
                            this.valueProgressUpload = (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
                        }.bind(this)
					})
					.then(({data}) => {
						this.$noty.success('Penerangan Satuan berhasil ditambah')
						this.$refs.modalTambah.hide()
						this.$refs.tabelPeneranganSatuan.refresh()
					})
					.catch((error) => {
						console.log(error)
						if (error.response) {
							if (error.response.status === 422) {
								this.$noty.error(flattenDeep(values(error.response.data.errors)).join('<br>'))
							}
						}
					})
				} else {

				}
				
			},
			imgProfilError (e){
				this.srcProfil = baseUrl+"/api/upload/personil/pocil.jpg"
			},
			resetFormTambah(){
				this.payload = {
					id: null,
					nama: null,
                    type: 'image'
				}
				this.video = null
				this.files = []
			},
			prepareUbah({ id }) {
				var promise = axios.get(baseUrl+'/api/penerangan-satuan/'+id, {
					headers: {
						Authorization: sessionStorage.getItem('token')
					}
				})
				.then(({data : { data }}) => {
				    this.payload.id = data.id
					this.payload.judul = data.judul
                    if(data.type == 'video'){
				        this.video = data.files[0].file
                    } else {
                        this.payload.files = data.files
                        this.files = data.files
                    }
					this.payload.type = data.type
					this.$refs.modalTambah.show()
				})
			},
			prepareReset (item) {
				Swal({
		            title: 'Reset password',
		            text: "Apakah anda yakin mereset password "+ item.nama +"?",
		            type: 'warning',
		            showCancelButton: true,
		            confirmButtonColor: '#2196f3',
		            confirmButtonText: 'Reset',
		            cancelButtonText: 'Batal'
		        }).then((result) => {
		          if (result.value) {
		              axios.post(baseUrl + '/api/personil/reset_password/', { id: item.id }, {
		                      headers: { Authorization: sessionStorage.getItem('token') }
		                })
		                .then((response) => {
		                  this.$noty.success('Password personil '+ item.nama +' berhasil di reset', { layout: 'topRight' })
		                  this.$refs.tabelPersonil.refresh()
		                })
		                .catch(({ response: { status, data: { errors }}}) => {
		                      if (status === 422)
		                        this.$noty.danger('Terjadi kesalahan saat menghapus data', { layout: 'topRight' })
		                })
		            }
		        })
			},
			prepareDelete (item) {
				Swal({
		            title: 'Hapus penerangan satuan',
		            text: "Apakah anda yakin menghapus data penerangan satuan "+ item.judul +"?",
		            type: 'warning',
		            showCancelButton: true,
		            confirmButtonColor: '##dc3545',
		            confirmButtonText: 'Hapus',
		            cancelButtonText: 'Batal'
		        }).then((result) => {
		          if (result.value) {
		              axios.delete(baseUrl + '/api/penerangan-satuan/' + item.id, {
		                      headers: { Authorization: sessionStorage.getItem('token') }
		                })
		                .then((response) => {
		                  this.$noty.success('Data penerangan satuan '+ item.judul +' berhasil di hapus', { layout: 'topRight' })
		                  this.$refs.tabelPeneranganSatuan.refresh()
		                })
		                .catch(({ response: { status, data: { errors }}}) => {
		                      if (status === 422)
		                        this.$noty.danger('Terjadi kesalahan saat menghapus data', { layout: 'topRight' })
		                })
		            }
		        })
			},
			onSelectKelurahan(items, lastSelectItem){
				this.itemsKelurahan = items
				this.payload.id_kelurahan = items.map((val) => val.value)
			},
			fetchJabatan(){
				var promise = axios.get(baseUrl+'/api/jabatan', {
					headers: {
						Authorization: sessionStorage.getItem('token')
					}
				})
				.then(({data: {data}}) => {
					this.jabatan = data.map((val) => {
						return {value: val.id, text: val.jabatan}
					})
				})
				.catch((error) => {
					
				})
			},
			fetchPangkat(){
				var promise = axios.get(baseUrl+'/api/pangkat', {
					headers: {
						Authorization: sessionStorage.getItem('token')
					}
				})
				.then(({data: {data}}) => {
					this.pangkat = data.map((val) => {
						return {value: val.id, text: val.pangkat}
					})
				})
				.catch((error) => {
					
				})
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
			fetchWilKel(){
				var promise = axios.get(baseUrl+'/api/wilayah')
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
			debounceFilter: debounce(function () {
				this.filter = this.filterDebounced
				this.currentPage = 1
			}, 500),  
		},
		mounted(){
			this.fetchJabatan()
			this.fetchPangkat()
			this.fetchKesatuan()
			this.fetchWilKel()
		},
		watch: {
			filterDebounced (newFilter) {
		        this.debounceFilter()
		    },/*
            files() {
			    this.payload.files = this.files
            }*/
		}
	}
</script>

<style scoped>
img.preview-profil {
	display:block;
  	margin-left:auto;
  	margin-right:auto;
}
.ui.selection.dropdown {
	background: #868e96;
	color: #fff;
}
.default {
	color: #fff;
}
</style>