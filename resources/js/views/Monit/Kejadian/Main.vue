<template>
    <div>
        <b-modal ref="kejadian"
                hide-footer centered
                modal-class="e-modal e-modal-mg"
                title-tag="h4"
                :no-close-on-backdrop="isBusy"
                :no-close-on-esc="isBusy"
                :hide-header-close="isBusy"
                title="Kejadian">
            <div class="d-block">
                <div class="position-relative mt-4">
                    <b-row>
                        <b-col cols="7">
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
                        <b-col cols="3">
                            <form @submit.prevent="search">
                                <b-input-group align="right">
                                    <b-form-input
                                        align="right"
                                        class="e-form"
                                        @keyup="whenSearch"
                                        v-model="filterDebounced"
                                        placeholder="Cari kejadian, lokasi, waktu kejadian..."/>
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
                        <template v-slot:cell(status)="data">
                            {{ data.item.status }}
                        </template>
                        <template v-slot:cell(nama)="data">
                            {{ data.item.user.nama }} <br/> {{ data.item.user.jabatan }}
                        </template>
                        <template v-slot:cell(aksi)="data">
                            <div class="dropdown-container">
                                <b-dropdown text="Pilih" class="e-btn-dropdown" boundary>
                                    <b-dropdown-item @click="detail(data.item)">
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
        <Detail ref="detail"/>
    </div>
</template>

<script>
import Detail from '@/views/Monit/Kejadian/Detail'
import { format, formatISO, parseISO } from 'date-fns'
import { debounce } from 'lodash'
import id from 'date-fns/locale/id'
export default {
    name: 'kejadian',
    components: { Detail },
    data () {
        return {
            totalRows: 0,
            perPage: 10,
            currentPage: 1,
            filter: '',
            filterDebounced: '',
            isBusy: false,
            sortBy: 'w_kejadian',
            sortDesc: true,
            tableColumns: [
                { key: 'index', label: 'No' },
                { key: 'kejadian', label: 'Kejadian', thStyle: { width: '25%' }, sortable: true },
                { key: 'lokasi', label: 'Lokasi', sortable: true },
                { key: 'kuat', label: 'Kuat pers', sortable: true },
                { 
                    key: 'w_kejadian', label: 'Waktu kejadian', 
                    formatter: v => format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}) ,
                    sortable: true,
                },
                { key: 'user.nrp', label: 'Nrp', sortable: true },
                { key: 'nama', label: 'Nama', sortable: true },
                { key: 'status', label: 'Status', sortable: true },
                { key: 'aksi', label: 'Aksi' },
            ],
            status: null,
            statusOptions: [
                { value: 'semua', text: 'Semua' },
                { value: 'belum_diverifikasi', text: 'Belum Diverifikasi' },
                { value: 'laporan_baru', text: 'Laporan Baru' },
                { value: 'proses_penanganan', text: 'Proses Penanganan' },
                { value: 'selesai', text: 'Selesai' },
            ]
        }
    },
    methods : {
        showModal () {
            this.$refs.kejadian.show()
        },
        provider (ctx) {
            let sortBy
            switch(ctx.sortBy) {
                case 'kejadian':
                    sortBy = 'kejadian'
                    break
                case 'lokasi':
                    sortBy = 'lokasi'
                    break
                case 'w_kejadian':
                    sortBy = 'w_kejadian'
                    break
                case 'id_user':
                    sortBy = 'user.nrp'
                    break
                case 'status':
                    sortBy = 'kejadian'
                    break
                default:
                    sortBy = 'w_kejadian'
            }

            let payload = {
                page: ctx.currentPage,
                filter: ctx.filter === '' ? null : ctx.filter,
                status: this.status !== null ? this.status.value : null,
                sort: (sortBy != "" ? sortBy : 'w_kejadian') + ':' + (ctx.sortDesc ? 'desc' : 'asc'),
            }

            return axios.get('kejadian', {
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
        detail (item) {
            let self = this
            setTimeout(function() {
                self.$refs.detail.showModal(item)
            }, 500)
        },
        lokasi (item) {
            if(item.lat == null && item.lng == null) { 
                this.$toast.error("Lokasi tidak dapat di tampilkan")
                return
            }
            var marker = { type: 'kejadian', data: item }
            this.$parent.$parent.getMarkerSingle(marker)
            this.$refs.kejadian.hide()
        },
        refreshTable () {
            this.totalRows > this.perPage ? 
            (this.currentPage == 1 ? this.$refs.table.refresh() : this.currentPage = 1) 
            : this.$refs.table.refresh()
        },
        sortingStatus () {
            this.refreshTable ()
        },
        search: debounce(function () {
            this.filter = this.filterDebounced
            this.currentPage = 1
        }, 500),
        whenSearch () {
            if(this.filterDebounced == '') {
                this.search()
            }
        }
    },
}
</script>