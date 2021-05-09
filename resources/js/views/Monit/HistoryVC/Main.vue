<template>
    <b-modal ref="history"
            hide-footer centered
            modal-class="e-modal e-modal-xl"
            :no-close-on-backdrop="isBusy"
            :no-close-on-esc="isBusy"
            :hide-header-close="isBusy"
            title-tag="h4"
            title="History Video Call">
	  	<div class="d-block">
	  		<div class="position-relative mt-4">
                <b-row>
                    <b-col cols="8">
                        <b-pagination
                            align="right"
                            class="e-pagination"
                            :total-rows="totalRows"
                            v-model="currentPage"
                            :per-page="perPage" />
                    </b-col>
                    <b-col cols="4">
                        <form @submit.prevent="search">
                            <b-input-group align="right">
                                <b-form-input
                                    align="right"
                                    class="e-form"
                                    @keyup="whenSearch"
                                    v-model="filterDebounced"
                                    placeholder="Cari nama, waktu panggilan..."/>
                                <b-input-group-append>
                                    <button class="btn e-btn e-btn-primary" type="submit">
                                        <ph-magnifying-glass class="phospor"/>
                                    </button>
                                </b-input-group-append>
                            </b-input-group>
                        </form>
                    </b-col>
                </b-row>
                <b-table responsive
                    class="e-table"
                    ref="table"
                    :busy.sync="isBusy"
                    :items="provider"
                    :fields="tableColumns"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc">
                    <template v-slot:cell(index)="data">
                        {{ ((currentPage - 1) * perPage) + data.index + 1 }}.
                    </template>
			        <template v-slot:cell(from)="data">
                        <div v-if="data.item.from != null">
                            <span v-if="data.item.from.jenis_pemilik == 'personil'"
                            v-b-tooltip.hover 
                            title="Klik untuk melihat detail personil"
                            class="marker"
                            @click="detail(data.item.from.pemilik)">
                                {{ data.item.from.pemilik.nama }}
                            </span>
                            <span v-else>{{ data.item.from.pemilik.nama }}</span>
                        </div>
                        <div v-else>-</div>
			        </template>
			        <template v-slot:cell(to)="data">
                        <div v-if="data.item.to != null">
                            <span v-if="data.item.to.jenis_pemilik == 'personil'"
                            v-b-tooltip.hover 
                            title="Klik untuk melihat detail personil"
                            class="marker"
                            @click="detail(data.item.to.pemilik)">
                                {{ data.item.to.pemilik.nama }}
                            </span>
                            <span v-else>{{ data.item.to.pemilik.nama }}</span>
                        </div>
                        <div v-else>-</div>
			        </template>
				   <template v-slot:cell(jenis)="data">
			        	<b-badge variant="primary" v-if="userId == data.item.from.id">
			        		<ph-phone-outgoing class="phospor"/> Panggilan keluar
			        	</b-badge>
			        	<b-badge variant="success" v-else>
			        		<ph-phone-incoming class="phospor"/> Panggilan masuk
			        	</b-badge>
			        </template>
			    </b-table>
				<div class="loading" v-show="isBusy">
			        <b-spinner variant="primary"></b-spinner>
			    </div>
	      	</div>
	    </div>
    </b-modal>
</template>

<script>
import { format, formatISO, parseISO } from 'date-fns'
import { debounce } from 'lodash'
import id from 'date-fns/locale/id'
export default {
    name: 'history',
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
                { key: 'from', label: 'Dari' },
                { key: 'to', label: 'Kepada' },
                { 
                    key: 'start', label: 'Mulai', 
                    formatter: v => v != null ? format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}) : '-' ,
                },
                { 
                    key: 'end', label: 'Selesai', 
                    formatter: v => v != null ? format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}) : '-' ,
                },
                { key: 'jenis', label: 'Panggilan' },
            ]
        }
    },
    computed : {
        userId () {
            return this.$store.getters.userInfo ? this.$store.getters.userInfo.id : ''
        }
    },
    methods : {
        showModal () {
            this.$refs.history.show()
        },
        provider (ctx) {
            let sortBy = 'id'
            let payload = {
                page: ctx.currentPage,
                filter: ctx.filter === '' ? null : ctx.filter,
                sort: (sortBy != "" ? sortBy : 'id') + ':' + (ctx.sortDesc ? 'desc' : 'asc'),
            }

            return axios.get('call/history', {
                    params: payload,
            }).then(({ data: { data, meta: { pagination }}}) => {
                this.totalRows = pagination.total
                this.perPage = pagination.per_page
                this.currentPage = pagination.current_page
                return data
            }).catch(error => {
                this.totalRows = 0
                return []
            })
        },
        detail (personil) {
            
        },
        refreshTable () {
            this.totalRows > this.perPage ? 
            (this.currentPage == 1 ? this.$refs.table.refresh() : this.currentPage = 1) 
            : this.$refs.table.refresh()
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
    },
}
</script>