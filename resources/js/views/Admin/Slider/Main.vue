<template>
	<div>
		<b-row>
	    	<b-col cols="4">
				<b-row>
	    			<b-col cols="12">
						<h4 class="d-inline-block mr-3">Slider </h4>
						<b-button @click="$refs.modalTambah.show()" variant="primary" size="sm">
							<ph-plus class="phospor"/> Tambah
						</b-button>
					</b-col>
	    		</b-row>
			</b-col>
		</b-row>

		<div class="position-relative mt-4">
			<b-table responsive
	               ref="table"
				   :items="providerSlider"
	               :busy.sync="isBusy"
	               :fields="tableColumns">

		        <template v-slot:cell(index)="data">
		          	{{ data.index + 1 }}
		        </template>
		        <template v-slot:cell(foto)="row">
					<img :src="row.item.foto" style="width: 300px;" alt="">
		        </template>
			    <template v-slot:cell(aksi)="row">
			    	<b-button size="md" v-b-tooltip title="Hapus slider" variant="danger" @click="prepareDelete(row.item)">
			    		<ph-trash class="phospor"/>
			    	</b-button>
			    </template>
		    </b-table>

		    <div class="loading" v-show="isBusy">
		        <b-spinner variant="primary"></b-spinner>
		    </div>
	  	</div>
		<b-modal
				hide-header-close
				no-close-on-backdrop
				no-close-on-esc
				ok-title="Simpan"
				cancel-title="Batal"
				@ok="submitSlider"
				@hide="resetFormTambah"
				ref="modalTambah"
				size="lg">
				<template slot="modal-header">
					<h3 class="modal-title">Tambah slider</h3>
				</template>
				<b-row>
					<b-form-group label-class="h3" class="col-md-6 col-xl-12">
						<b-form-group label="Foto" label-class="h6 mt2" label-cols="2">
							<b-form-file id="foto" class="e-form" placeholder="Pilih gambar..." accept="image/*"></b-form-file>
							<br/>
							<span>*) Masukkan gambar dengan aspect ratio 169 : 61</span>
						</b-form-group>
					</b-form-group>
				</b-row>
		</b-modal>
	</div>
</template>
<script>
	import format from 'date-fns/format'
	import Swal from 'sweetalert2'
	import { debounce, flattenDepth, values } from 'lodash'

	const dateFnsBahasa = {
	    locale: require('date-fns/locale/id')
	}

	export default {
		name: 'slider',
		data () {
			return {
				isBusy: false,
        		tableColumns: [
					{ key: 'index', label: 'No' },
					{ key: 'foto', label: 'Foto' },
					{ key: 'aksi', label: 'Aksi' },
				],
				payload: {
					foto: null
				}
			}
		},
		methods: {
			providerSlider (ctx) {
				this.isBusy = true
				var promise = axios.get("slider")
				.then(({ data }) => {
					return data
				})
				.catch(({response}) => {
					return response
				})

				return promise
			},
			prepareDelete (item) {
		    	Swal.fire({
				  	title: 'Hapus data',
				  	text: "Apakah anda yakin menghapus data slider ini?",
				  	icon: 'error',
				  	showCancelButton: true,
				  	confirmButtonColor: '#e53935',
				  	confirmButtonText: 'Hapus',
				  	cancelButtonText: 'Batal'
				}).then((result) => {
				 	if (result.value) {
					    axios.delete('slider/' + item.id, {
			              	headers: {
			              		'Content-Type': 'multipart/form-data' 
			              	}
				        })
				        .then((response) => {
				        	this.$toast.success('Data slider berhasil dihapus')
				        	this.$refs.table.refresh()
				        })
				        .catch(({ response: { status, data: { errors }}}) => {
			                if (status === 422)
			                	this.$toast.danger('Terjadi kesalahan saat menghapus data', { layout: 'topRight' })
			            })
				  	}
				})
		    },
			resetFormTambah(){
				//this.payload.info = null
			},
			submitSlider(e){
				e.preventDefault();
				
				let data = new FormData()
				data.append('foto', document.getElementById('foto').files[0])

				axios.post(`slider`, data, {
					headers: { 'Content-Type': 'multipart/form-data' }
				})
				.then(({data}) => {
					this.$refs.modalTambah.hide()
					this.$refs.table.refresh()
					this.$toast.success('Slider berhasil ditambahkan')
				})
				.catch(({ response: { status, data: { errors }}}) => {
					if (status === 422)
						this.$toast.error(flattenDepth(values(errors)).join('<br>'))
				})
			},
		},
	}
</script>