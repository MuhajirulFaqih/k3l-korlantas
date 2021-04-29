<template>
	<div>
		<b-row>
	    	<b-col cols="2" md="2">
				<b-row>
                    <b-col cols="12">
                        <h4 class="d-inline-block mr-3">HT Channel </h4>
                        <b-button @click="prepareCreate" variant="primary" size="sm">
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
		    	<b-form-input
		        	align="right"
		          	v-model="filterDebounced"
		          	placeholder="Cari Channel..."/>
		    </b-col>
		</b-row>

		<div class="position-relative">
			<b-table responsive
	               ref="table"
	               :busy.sync="isBusy"
	               :fields="tableColumns"
				   :items="providerChannel"
	               :current-page="currentPage"
	               :per-page="perPage"
	               :filter="filter"
	               :sort-by.sync="sortBy"
	               :sort-desc.sync="sortDesc">
		        <template v-slot:cell(index)="data">
		          	{{ ((currentPage - 1) * 10) + data.index + 1 }}
		        </template>
			    <template v-slot:cell(aksi)="row">
			    	<b-button size="md" v-b-tooltip :title="'Edit data ' + row.item.name" variant="info" @click="prepareEdit(row.item)">
			    		<ph-pencil class="phospor"/>
			    	</b-button>
			    	<b-button size="md" v-b-tooltip :title="'Hapus data ' + row.item.name" variant="danger" @click="prepareDelete(row.item)">
			    		<ph-trash class="phospor"/>
			    	</b-button>
			    </template>
		    </b-table>

		    <div class="loading" v-show="isBusy">
		        <b-spinner variant="primary"></b-spinner>
		    </div>
	  	</div>

	  	<!-- Modal form -->
		<b-form @submit.prevent="submitChannel">
			<b-modal ref="modalForm"
					no-close-on-backdrop
					no-close-on-esc
					title-tag="h4"
					size="md"
					header-class="red"
					header-text-variant="white"
					:title="modalTitle">
				<b-col cols="12">
			      	<b-form-group
			            horizontal
			            :label-cols="2"
			            breakpoint="md"
			            label="Channel">
							<b-form-input type="text" v-model="singleChannel.channel" />
			        </b-form-group>
				</b-col>
				<template slot="modal-footer">
					<b-btn v-if="!singleChannel.channel_id" type="submit" variant="primary">
						<icon name="save"/> Simpan
					</b-btn>
					<b-btn v-else type="submit" variant="primary">
						<icon name="paper-plane"/> Edit
					</b-btn>
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
		name: 'channel',
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
        		sortBy: 'channel_id',
				sortDesc: false,
				tableColumns: [
					{ key: 'index', label: 'No' },
					{ key: 'name', label: 'Channel', sortable: true },
					{ key: 'aksi', label: 'Aksi'}
				],
        		singleChannel: {
        			channel_id: '',
        			channel: '',
        		}
			}
		},
		computed: {
			modalTitle () {
				return this.singleChannel.id == '' ? 'Tambah channel' : 'Edit channel'
			}
		},
		methods: {
			providerChannel (ctx) {
				let sortBy

				switch(ctx.sortBy) {
					case 'channel':
						sortBy = 'channel'
						break
					default:
						sortBy = ctx.sortBy

				}

				let payload = {
					page: ctx.currentPage,
					filter: ctx.filter === '' ? null : ctx.filter,
					sort: (sortBy != null ? sortBy : 'channel_id') + ':' + (ctx.sortDesc ? 'desc' : 'asc')
				}

				var promise = axios.get('user/ht', {
						params: payload
					})
					.then(({data : {data, meta: { pagination }}}) => {
						this.totalRows = pagination.total
						this.perPage = pagination.per_page
						this.currentPage = pagination.current_page
						return data
					})
					.catch(({ response }) => {
						return response
					})

				return promise
		    },
		    prepareCreate () {
		    	this.resetSingleChannel()
		    	this.$refs.modalForm.show()
		    },
		    submitChannel () {
		    	if(this.singleChannel.channel_id) {
		        	axios.post('user/ht/' + this.singleChannel.channel_id, this.singleChannel)
			        .then((response) => {
			        	this.$toast.success('Data channel '+ this.singleChannel.channel +' berhasil di edit', { layout: 'topRight' })
			        	this.$refs.modalForm.hide()
			        	this.resetSingleChannel()
			        	this.refreshTable()
			        })
			        .catch(({ response: { status, data: { errors }}}) => {
		                if (status === 422)
		                	this.$toast.error(flattenDepth(values(errors)).join('<br>'), { layout: 'topRight' })
		            })
		        } 
		        else {
		        	axios.post('user/ht', this.singleChannel)
			        .then((response) => {
			        	this.$toast.success('Data channel berhasil di tambahkan', { layout: 'topRight' })
			        	this.$refs.modalForm.hide()
			        	this.resetSingleChannel()
			        	this.refreshTable()
			        })
			        .catch(({ response: { status, data: { errors }}}) => {
		                if (status === 422)
		                	this.$toast.error(flattenDepth(values(errors)).join('<br>'), { layout: 'topRight' })
		            })
		        }
		    },
		    prepareEdit (item) {
		    	this.singleChannel = {
		    		channel_id: item.channel_id,
		    		channel: item.name,
		    	}
		    	this.$refs.modalForm.show()
		    },
		    prepareDelete (item) {
		    	Swal.fire({
				  	title: 'Hapus data',
				  	text: "Apakah anda yakin menghapus data "+ item.name +"?",
				  	icon: 'error',
				  	showCancelButton: true,
				  	confirmButtonColor: '#e53935',
				  	confirmButtonText: 'Hapus',
				  	cancelButtonText: 'Batal'
				}).then((result) => {
				 	if (result.value) {
					    axios.delete('user/ht/' + item.channel_id, {
			              	headers: { 'Content-Type': 'multipart/form-data' }
				        })
				        .then((response) => {
				        	this.$toast.success('Data channel '+ item.name +' berhasil di hapus', { layout: 'topRight' })
				        	this.refreshTable()
				        })
				        .catch(({ response: { status, data: { errors }}}) => {
			                if (status === 422)
			                	this.$toast.danger('Terjadi kesalahan saat menghapus data', { layout: 'topRight' })
			            })
				  	}
				})
		    },
		    resetSingleChannel () {
		    	this.singleChannel = {
        			channel_id: '',
        			channel: '',
        		}
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