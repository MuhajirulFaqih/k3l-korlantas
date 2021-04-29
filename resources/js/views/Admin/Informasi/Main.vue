<template>
	<div>
		<b-row>
	    	<b-col cols="3">
				<b-row>
	    			<b-col cols="12">
						<h4 class="d-inline-block mr-3">Informasi </h4>
						<b-button @click="$refs.modalTambah.show()" variant="primary" size="sm">
							<ph-plus class="phospor"/> Tambah
						</b-button>
					</b-col>
	    		</b-row>
			</b-col>
	    	<b-col cols="9">
		        <b-pagination
					align="right"
		          	:total-rows="totalRows"
		          	v-model="currentPage"
		          	:per-page="perPage" />
		    </b-col>
		    <!-- <b-col cols="4" md="4">
		    	<b-form-input
		        	align="right"
		          	v-model="filterDebounced"
		          	placeholder="Cari Channel..."/>
		    </b-col> -->
		</b-row>

		<div class="position-relative">
			<b-table responsive
	               ref="table"
				   :items="providerInformasi"
	               :busy.sync="isBusy"
	               :fields="tableColumns"
	               :current-page="currentPage"
	               :per-page="perPage"
	               :filter="filter"
	               :sort-by.sync="sortBy"
	               :sort-desc.sync="sortDesc">

		        <template v-slot:cell(index)="data">
		          	{{ ((currentPage - 1) * 10) + data.index + 1 }}
		        </template>
			    <template v-slot:cell(aktif)="row">
			    	<b-form-checkbox v-model="row.item.aktif" value="true" unchecked-value="false" @change="aktifChange(row.item)"/>
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
				@ok="submitInformasi"
				@hide="resetFormTambah"
				ref="modalTambah"
				size="lg">
				<template slot="modal-header">
					<h3 class="modal-title">Tambah informasi</h3>
				</template>
				<b-row>
					<b-form-group label-class="h3" class="col-md-6 col-xl-12">
						<b-form-group label="Informasi" label-class="h6 mt2" label-cols="2">
							<b-form-textarea v-model="payload.info" type="text" />
						</b-form-group>
					</b-form-group>
				</b-row>
		</b-modal>
	</div>
</template>
<script>
	import format from 'date-fns/format'
	import { debounce } from 'lodash'

	const dateFnsBahasa = {
	    locale: require('date-fns/locale/id')
	}

	export default {
		name: 'informasi',
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
					{ key: 'informasi', label: 'Informasi' },
					{ key: 'aktif', label: 'Status' },
				],
				payload: {
					info: null
				}
			}
		},
		methods: {
			providerInformasi (ctx) {
				this.isBusy = true
				let payload = { page: ctx.currentPage }
				var promise = axios.get("informasi", { params: payload, })
				.then(({data: { data, meta: {pagination}} }) => {
					this.totalRows = pagination.total
					this.perPage = pagination.per_page
					this.currentPage = pagination.current_page
					return data
				})
				.catch(({response}) => {
					return response
				})

				return promise
			},
			aktifChange(item) {
				if (this.isBusy) {
					return
				}
				axios.post('informasi/'+item.id, { aktif: item.aktif })
				.then(({data}) => {
					if ('success' in data){
						this.$toast.success('Status informasi berhasil di ubah')
					} else {
						this.$toast.error('Informasi gagal di ubah')
					}
					this.refreshTable()
				})
				.catch(({response}) => {
					this.$toast.error('Informasi gagal di ubah')
					this.refreshTable()
				})
			},
			resetFormTambah(){
				//this.payload.info = null
			},
			submitInformasi(e){
				e.preventDefault();
				
				if (this.payload.info === null){
					this.$toast.error('Informasi tidak boleh kosong')
					return
				}

				axios.post('informasi', this.payload)
				.then(({data}) => {
					if ('success' in data){
						this.$toast.success('Informasi berhasil tambah')
						this.payload.info = null
						this.$refs.modalTambah.hide()
					} else {
						this.$toast.error('Informasi gagal tambah')
					}
					this.refreshTable()
				})
				.catch(({response}) => {
					this.$toast.error('Informasi gagal tambah')
					this.refreshTable()
				})
			},
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
		}
	}
</script>