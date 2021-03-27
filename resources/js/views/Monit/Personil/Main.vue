<template>
    <div>
        <b-modal ref="personil"
            hide-footer centered
            modal-class="e-modal e-modal-mg"
            :no-close-on-backdrop="isBusy"
            :no-close-on-esc="isBusy"
            :hide-header-close="isBusy"
            title-tag="h4"
            title="Personil">
            <div class="d-block">
                <div class="position-relative mt-4">
                    <b-row>
                        <b-col cols="4">
                            <button class="btn e-btn e-btn-danger" @click="showPatroli">
                                <ph-car class="phospor"/> Patroli
                            </button>
                            <button class="btn e-btn e-btn-success" @click="showPengawalan">
                                <ph-car-simple class="phospor"/> Pengawalan
                            </button>
                        </b-col>
                        <b-col cols="5">
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
                                        placeholder="Cari nrp, nama, jabatan, status dinas..."/>
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
                            {{ data.item.pangkat }} {{ data.item.nama }}
                        </template>
                        <template v-slot:cell(statusLogin)="data">
                            <b-badge v-if="data.item.loginStatus == '0'" variant="danger">Belum Pernah Login</b-badge>
                            <b-badge v-else variant="success">Sudah Pernah Login</b-badge>
                        </template>
                        <template v-slot:cell(statusAktif)="data">
                            <b-badge v-if="data.item.activeStatus == '0'" variant="warning">Pasif</b-badge>
                            <b-badge v-else variant="success">Aktif</b-badge>
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
        <Patroli ref="patroli"/>
        <Pengawalan ref="pengawalan"/>
    </div>
</template>

<script>
import Detail from '@/views/Monit/Personil/Detail'
import Patroli from '@/views/Monit/Personil/Patroli'
import Pengawalan from '@/views/Monit/Personil/Pengawalan'
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
            sortBy: 'id_pangkat',
            sortDesc: false,
            tableColumns: [
                { key: 'index', label: 'No' },
                { key: 'nrp', label: 'NRP', sortable: true },
                { key: 'nama', label: 'Nama', thStyle: { width: '25%' }, sortable: true },
                { key: 'jabatan', label: 'Jabatan', sortable: true },
                { key: 'kesatuan', label: 'Kesatuan', sortable: true },
                { key: 'dinas.kegiatan', label: 'Status Dinas', sortable: true },
                { key: 'statusLogin', label: 'Status Login', sortable: true },
                { key: 'statusAktif', label: 'Aktif/Pasif', sortable: true },
                { key: 'aksi', label: 'Aksi' },
            ],
        }
    },
    components: { Detail, Patroli, Pengawalan },
    methods : {
        showModal () {
            this.$refs.personil.show()
        },
        showPengawalan () {
            this.$refs.pengawalan.showModal()
        },
        showPatroli () {
            this.$refs.patroli.showModal()
        },
        provider (ctx) {
            let sortBy
            switch(ctx.sortBy) {
                case 'nrp':
                    sortBy = 'nrp'
                    break
                case 'nama':
                    sortBy = 'nama'
                    break
                case 'jabatan':
                    sortBy = 'id_jabatan'
                    break
                case 'kesatuan':
                    sortBy = 'id_kesatuan'
                    break
                case 'dinas.kegiatan':
                    sortBy = 'status_dinas'
                    break
                default:
                    sortBy = 'id'
            }

            let payload = {
                page: ctx.currentPage,
                filter: ctx.filter === '' ? null : ctx.filter,
                sort: (sortBy != "" ? sortBy : 'id_pangkat') + ':' + (ctx.sortDesc ? 'desc' : 'asc'),
            }

            return axios.get('personil', {
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
        },
    },
}
</script>