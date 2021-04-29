<template>
	<div>
		<b-row>
	    	<b-col cols="12">
		        <b-pagination
		          	align="right"
		          	:total-rows="totalRows"
		          	v-model="currentPage"
		          	:per-page="perPage" />
		    </b-col>
		</b-row>

		<div class="position-relative">
			<b-table responsive
				ref="tabelRincianBelanja"
				:busy.sync="isBusy"
				:items="providerRincianBelanja"
				:fields="tableColumns"
				:current-page="currentPage"
				:per-page="perPage"
				:sort-by.sync="sortBy"
				:sort-desc.sync="sortDesc">
		        <template v-slot:cell(index)="data">
		          	{{ ((currentPage - 1) * 10) + data.index + 1 }}
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
	import { debounce } from 'lodash'

	const dateFnsBahasa = {
	    locale: require('date-fns/locale/id')
	}

	export default {
		name: 'rincian-belanja',
		data () {
			return {
				totalRows: 0,
				perPage: 10,
        		currentPage: 1,
        		isBusy: false,
        		sortBy: 'created_at',
				sortDesc: false,
				tableColumns: [
					{ key: 'index', label: 'No' },
					{ key: 'uraian', label: 'Uraian', formatter: v => { return 'Rp.' + v }, sortable: false },
					{ key: 'jumlah', label: 'Jumlah', formatter: v => { return 'Rp.' + v }, sortable: false },
					{ key: 'tahun_anggaran', label: 'Tahun anggaran', formatter: v => { return v.substr(0, 4) }, sortable: false },
				],
        		params: ''
			}
		},
		methods: {
			fetchData (params) {
				this.params = params
				this.$refs.tabelRincianBelanja.refresh()
			},
			providerRincianBelanja (ctx) {
		        let sortBy
				sortBy = ctx.sortBy

		        let payload = {
		          	page: ctx.currentPage,
		          	sort: sortBy + ':' + (ctx.sortDesc ? 'desc' : 'asc'),
		          	desa: this.params.desa,
		          	tipe: this.params.tipe,
		          	tahun: this.params.tahun
		        }
		        
				return axios.get('report/rincian/' , { params: payload})
					.then(({ data: { data, meta: { pagination }}}) => {
	            	this.totalRows = pagination.total
	            	this.perPage = pagination.per_page
	            	this.currentPage = pagination.current_page
		            return data
	          	}).catch(error => {
	            	this.totalRows = 0

	            	return []
	          	})
	      	},
		},
	}
</script>