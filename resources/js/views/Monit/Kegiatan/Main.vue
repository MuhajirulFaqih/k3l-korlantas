<template>
    <div>
        <b-modal ref="kegiatan"
                hide-footer centered
                modal-class="e-modal e-modal-mg"
                :no-close-on-backdrop="isBusy"
                :no-close-on-esc="isBusy"
                :hide-header-close="isBusy"
                title-tag="h4"
                title="Kegiatan">
            <div class="d-block">
                <div class="position-relative mt-4">
                    <b-row>
                        <b-col cols="9">
                            <b-pagination
                                align="right"
                                class="e-pagination"
                                :total-rows="totalRows"
                                v-model="currentPage"
                                :per-page="perPage" />
                        </b-col>
                        <b-col cols="3">
                            <form @submit.prevent="search">
                                <b-input-group align="right">
                                    <b-form-input
                                        align="right"
                                        class="e-form"
                                        @keyup="whenSearch"
                                        v-model="filterDebounced"
                                        placeholder="Cari detail, jenis, waktu kegiatan..."/>
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
import Detail from '@/views/Monit/Kegiatan/Detail'
import { format, formatISO, parseISO } from 'date-fns'
import { debounce } from 'lodash'
import id from 'date-fns/locale/id'
export default {
    name: 'kegiatan',
    components: { Detail },
    data () {
        return {
            totalRows: 0,
            perPage: 10,
            currentPage: 1,
            filter: '',
            filterDebounced: '',
            isBusy: false,
            sortBy: 'waktu_kegiatan',
            sortDesc: true,
            tableColumns: [
                { key: 'index', label: 'No' },
                { key: 'detail', label: 'Detail', thStyle: { width: '25%' }, sortable: true },
                { 
                    key: 'jenis', 
                    label: 'Jenis', 
                    sortable: false, 
                    formatter: v => {
                        var viewJenis = ''
                        v.forEach((w) => {
                            switch (w.jenis.keterangan) {
                                case 'jenis_kegiatan':
                                    viewJenis += `<div class="4">Jenis Kegiatan</div> 
                                                <div class="1">:</div> 
                                                <div class="7">${v.jenis.jenis}</div>`
                                    break;
                                default:
                                    break;
                            }
                        })
                        return viewJenis == '' ? '-' : viewJenis
                    } 
                },
                { 
                    key: 'w_kegiatan', label: 'Waktu kegiatan', 
                    formatter: v => format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}) ,
                    sortable: true,
                },
                { key: 'user.nrp', label: 'Nrp', sortable: true },
                { key: 'nama', label: 'Nama', sortable: true },
                { key: 'aksi', label: 'Aksi' },
            ]
        }
    },
    methods : {
        showModal (item) {
            this.single = item
            this.$refs.kegiatan.show()
        },
        provider (ctx) {
            let sortBy
            switch(ctx.sortBy) {
                case 'judul':
                    sortBy = 'judul'
                    break
                case 'w_kegiatan':
                    sortBy = 'waktu_kegiatan'
                    break
                case 'user.nrp':
                    sortBy = 'id_user'
                    break
                case 'nama':
                    sortBy = 'id_user'
                    break
                default:
                    sortBy = 'waktu_kegiatan'
            }

            let payload = {
                page: ctx.currentPage,
                filter: ctx.filter === '' ? null : ctx.filter,
                sort: (sortBy != "" ? sortBy : 'waktu_kegiatan') + ':' + (ctx.sortDesc ? 'desc' : 'asc'),
            }

            return axios.get('kegiatan', {
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
            var marker = { type: 'kegiatan', data: item }
            this.$parent.$parent.getMarkerSingle(marker)
            this.$refs.kegiatan.hide()
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