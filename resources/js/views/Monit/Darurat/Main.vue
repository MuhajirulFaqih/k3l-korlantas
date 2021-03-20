<template>
    <b-modal ref="darurat"
            hide-footer centered
            modal-class="e-modal e-modal-mg"
            :no-close-on-backdrop="isBusy"
            :no-close-on-esc="isBusy"
            :hide-header-close="isBusy"
            title-tag="h4"
            title="Darurat">
	  	<div class="d-block">
	  		<div class="position-relative mt-4">
                <b-row>
                    <b-col cols="5">
                        <b-pagination
                            align="right"
                            class="e-pagination"
                            :total-rows="totalRows"
                            v-model="currentPage"
                            :per-page="perPage" />
                    </b-col>
                    <b-col cols="2">
                        <multiselect
                            v-model="status"
                            :options="statusOptions"
                            :searchable="false"
                            :show-labels="false"
                            value="value"
                            label="text"
                            placeholder="Pilih status..."
                            track-by="text"
                            :multiple="false"
                            @input="sortingStatus">
                        </multiselect>
                    </b-col>
                    <b-col cols="2">
			    	<multiselect
                            v-model="statusKejadian"
                            :options="statusKejadianOptions"
                            :searchable="false"
                            :show-labels="false"
                            value="value"
                            label="text"
                            placeholder="Pilih status kejadian..."
                            track-by="text"
                            :multiple="false"
                            @input="sortingStatusKejadian">
                        </multiselect>
                    </b-col>
                    <b-col cols="3">
                        <form @submit.prevent="search">
                            <b-input-group align="right">
                                <b-form-input
                                    align="right"
                                    class="e-form"
                                    @keyup="whenSearch"
                                    v-model="filterDebounced"
                                    placeholder="Cari judul, tipe, waktu kegiatan..."/>
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
			        <template v-slot:cell(nama)="data">
				      	{{ data.item.user.nama }} <br/> {{ data.item.user.jabatan }}
				    </template>
				    <template v-slot:cell(status)="data">
				      	<span v-if="data.item.selesai == 1">Selesai</span>
				      	<span v-else>Belum selesai</span>
				    </template>
				    <template v-slot:cell(status_kejadian)="data">
				      	<span v-if="data.item.kejadian != null">Di ubah ke kejadian</span>
				      	<span v-else>Tidak di ubah</span>
				    </template>
				    <template v-slot:cell(aksi)="data">
				    	<div class="dropdown-container">
                            <b-dropdown text="Pilih" class="e-btn-dropdown" boundary>
                                <b-dropdown-item @click="detail(data.item, 'bottombar')">
                                    <ph-note class="phospor"/> Detail
                                </b-dropdown-item>
                                <b-dropdown-item @click="lokasi(data.item)">
                                    <ph-map-pin class="phospor"/> Lihat lokasi
                                </b-dropdown-item>
                            </b-dropdown>
                        </div>
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
    name: 'darurat',
    data () {
        return {
            totalRows: 0,
            perPage: 10,
            currentPage: 1,
            filter: '',
            filterDebounced: '',
            isBusy: false,
            sortBy: 'created_at',
            sortDesc: true,
            tableColumns: [
                { key: 'index', label: 'No' },
                { key: 'nama', label: 'Pengirim', sortable: true },
                { key: 'lat', label: 'Latitude', sortable: true },
                { key: 'lng', label: 'Longitude', sortable: true },
                { key: 'acc', label: 'Akurasi', sortable: true },
                { 
                    key: 'created_at', label: 'Waktu dikirim', 
                    formatter: v => format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}) ,
                    sortable: true,
                },
                { key: 'status', label: 'Status', sortable: true },
                { key: 'status_kejadian', label: 'Status Kejadian', sortable: true },
                { key: 'aksi', label: 'Aksi' },
            ],
            status: { value: 'semua', text: 'Semua' },
            statusOptions: [
                { value: 'semua', text: 'Semua' },
                { value: 'selesai', text: 'Selesai' },
                { value: 'belum_selesai', text: 'Belum Selesai' },
            ],
            statusKejadian: { value: 'semua', text: 'Semua' },
            statusKejadianOptions: [
                { value: 'semua', text: 'Semua' },
                { value: 'belum_diubah', text: 'Tidak di ubah' },
                { value: 'ubah_kejadian', text: 'Di ubah ke kejadian' },
            ]
        }
    },
    methods : {
        showModal () {
            this.$refs.darurat.show()
        },
        provider (ctx) {
            let sortBy
            switch(ctx.sortBy) {
                case 'lat':
                    sortBy = 'lat'
                    break
                case 'lng':
                    sortBy = 'lng'
                    break
                case 'nama':
                    sortBy = 'id_user'
                    break
                case 'acc':
                    sortBy = 'acc'
                    break
                case 'created_at':
                    sortBy = 'created_at'
                    break
                default:
                    sortBy = 'created_at'
            }

            let payload = {
                page: ctx.currentPage,
                filter: ctx.filter === '' ? null : ctx.filter,
                status: this.status !== null ? this.status.value : null,
                statusKejadian: this.statusKejadian !== null ?
                                this.statusKejadian.value : null,
                sort: (sortBy != "" ? sortBy : 'created_at') + ':' + (ctx.sortDesc ? 'desc' : 'asc'),
            }

            return axios.get('darurat', {
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
        detail (item, type) {
            this.$refs.darurat.hide()
            let self = this
            setTimeout(function() {
                self.$refs.detail.showModal(item, type)
            }, 500)
        },
        refreshTable () {
            this.totalRows > this.perPage ? 
            (this.currentPage == 1 ? this.$refs.table.refresh() : this.currentPage = 1) 
            : this.$refs.table.refresh()
        },
        sortingStatus () {
            this.refreshTable()
        },
        sortingStatusKejadian () {
            this.refreshTable()
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