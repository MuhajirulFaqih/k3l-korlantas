<template>
    <b-modal ref="kegiatan"
            hide-footer centered
            modal-class="e-modal e-modal-mg"
            title-tag="h4"
            title="Kegiatan">
	  	<div class="d-block">
	  		<div class="position-relative mt-4">
                <b-table responsive
                    class="e-table"
                    ref="tabel"
                    :busy.sync="isBusy"
                    :items="providerTable"
                    :fields="tableColumns"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc">
                    <template v-slot:cell(index)="data">
                        {{ ((currentPage - 1) * perPage) + data.index + 1 }}.
                    </template>
                </b-table>
				<div class="loading">
			        <b-spinner variant="primary"></b-spinner>
			    </div>
	      	</div>
	    </div>
    </b-modal>
</template>

<script>
export default {
    name: 'kegiatan',
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
                { key: 'index', label: 'No.'},
                { key: 'tes', label: 'Tes', sortable: true},
            ],
        }
    },
    methods : {
        showModal() {
            this.$refs.kegiatan.show()
        },
        providerTable () {
            return [
                { tes: 'Tes' }
            ]
        }
    }
}
</script>