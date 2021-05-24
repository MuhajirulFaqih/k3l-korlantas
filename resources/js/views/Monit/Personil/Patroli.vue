<template>
    <div>
        <b-modal ref="patroli"
            hide-footer centered
            modal-class="e-modal e-modal-mg"
            :no-close-on-backdrop="isBusy"
            :no-close-on-esc="isBusy"
            :hide-header-close="isBusy"
            title-tag="h4"
            title="Patroli personil">
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
                                        placeholder="Cari nrp, nama, jabatan..."/>
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
                            {{ data.item.personil.pangkat }} {{ data.item.personil.nama }}
                        </template>
                        <template v-slot:cell(aksi)="data">
                           <div class="dropdown-container">
                                <b-dropdown text="Pilih" class="e-btn-dropdown" boundary>
                                    <b-dropdown-item @click="detail(data.item.personil)">
                                        <ph-note class="phospor"/> Detail
                                    </b-dropdown-item>
                                    <b-dropdown-item @click="lokasi(data.item)">
                                        <ph-path class="phospor"/> Lihat rute
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
    </div>
</template>

<script>
import { format, formatISO, parseISO } from 'date-fns'
import { debounce } from 'lodash'
import id from 'date-fns/locale/id'
export default {
    name: 'personil',
    data () {
        return {
            totalRows: 0,
            perPage: 10,
            currentPage: 1,
            filter: '',
            filterDebounced: '',
            isBusy: false,
            sortBy: 'created_at',
            sortDesc: false,
            tableColumns: [
                { key: 'index', label: 'No' },
                { key: 'personil.nrp', label: 'NRP', sortable: true },
                { key: 'nama', label: 'Nama', thStyle: { width: '25%' }, sortable: true },
                { key: 'personil.jabatan', label: 'Jabatan', sortable: true },
                { key: 'personil.kesatuan', label: 'Kesatuan', sortable: true },
                { 
                    key: 'waktu_mulai_dinas', label: 'Waktu mulai dinas', 
                    formatter: v => (v !== null ? format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}) : ''),
                    sortable: true,
                },
                { 
                    key: 'waktu_selesai_dinas', label: 'Waktu selesai dinas', 
                    formatter: v => (v !== null ? format(parseISO(v), 'd MMMM yyyy HH:mm:ss', {locale: id}) : ''),
                    sortable: true,
                },
                { key: 'aksi', label: 'Aksi' },
            ],
        }
    },
    methods : {
        showModal () {
            this.$refs.patroli.show()
        },
        provider (ctx) {
            let sortBy = 'created_at'

            let payload = {
                page: ctx.currentPage,
                filter: ctx.filter === '' ? null : ctx.filter,
                sort: (sortBy != "" ? sortBy : 'created_at') + ':' + (ctx.sortDesc ? 'desc' : 'asc'),
            }

            return axios.get('personil/patroli', {
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
                self.$parent.$refs.detail.showModal(item)
            }, 500)
        },
        refreshTable () {
            if(typeof this.$refs.table != 'undefined') {
                this.totalRows > this.perPage ? 
                (this.currentPage == 1 ? this.$refs.table.refresh() : this.currentPage = 1) 
                : this.$refs.table.refresh()
            }
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
        lokasi (item) {
            if(item.patroliPengawalan.length == 0) {
                this.$toast.error('Lokasi belum dapat di tracking')
                return
            }
            var self = this
            this.$parent.$parent.$parent.logPatroli(item)
            this.$refs.patroli.hide()
            this.$parent.$refs.personil.hide()
        }
    },
}
</script>