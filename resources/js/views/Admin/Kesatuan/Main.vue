<template>
    <div>
        <b-row>
            <b-col cols="2" md="2">
                <b-row>
                    <b-col cols="6"><h4>Kesatuan</h4></b-col>
                    <b-col cols="6">
                        <!--<b-button variant="primary" size="sm" @click="prepareCreate">
                            <ph-plus class="phospor"/>
                            Tambah
                        </b-button>-->
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="6" md="6">
                <b-pagination
                    align="right"
                    :total-rows="totalRows"
                    v-model="currentPage"
                    :per-page="perPage"/>
            </b-col>
            <b-col cols="4" md="4">
                <form @submit.prevent="search">
                    <b-input-group align="right">
                        <b-form-input
                            align="right"
                            class="e-form"
                            @keyup="whenSearch"
                            v-model="filterDebounced"
                            placeholder="Cari Kesatuan, Induk..."/>
                        <b-input-group-append>
                            <button class="btn e-btn e-btn-primary" type="submit">
                                <ph-magnifying-glass class="phospor"/>
                            </button>
                        </b-input-group-append>
                    </b-input-group>
                </form>
            </b-col>
        </b-row>

        <div class="position-relative">
            <b-table responsive
                     ref="table"
                     :busy.sync="isBusy"
                     :fields="tableColumns"
                     :items="providerKesatuan"
                     :current-page="currentPage"
                     :per-page="perPage"
                     :filter="filter"
                     :sort-by.sync="sortBy"
                     :sort-desc.sync="sortDesc">
                <template #cell(index)="data">
                    {{ ((currentPage - 1) * 10) + data.index + 1 }}
                </template>
                <template #cell(aksi)="row">
                    <b-button size="md" v-b-tooltip :title="'Edit data ' + row.item.kesatuan" variant="primary"
                              @click="prepareEdit(row.item)">
                        <ph-pencil class="phospor"/>
                    </b-button>
                    <b-button size="md" v-b-tooltip :title="'Hapus data ' + row.item.kesatuan" variant="danger"
                              @click="prepareDelete(row.item)">
                        <ph-trash class="phospor"/>
                    </b-button>
                </template>
            </b-table>

            <div class="loading" v-show="isBusy">
                <b-spinner variant="primary"/>
            </div>
        </div>

        <!-- Modal form -->
        <b-form @submit.prevent="submitKesatuan">
            <b-modal ref="modalForm"
                     no-close-on-backdrop
                     no-close-on-esc
                     title-tag="h4"
                     size="lg"
                     header-class="bg-primary"
                     header-text-variant="white"
                     :title="modalTitle">
                <b-col cols="12">
                    <b-form-group
                        horizontal
                        :label-cols="2"
                        breakpoint="md"
                        label="Kesatuan">
                        <b-form-input type="text" v-model="singleKesatuan.kesatuan"/>
                    </b-form-group>
                </b-col>
                <template #modal-footer>
                    <b-btn v-if="!singleKesatuan.id" type="submit" variant="primary" @click="submitKesatuan"> Simpan</b-btn>
                    <b-btn v-else type="submit" variant="primary" @click="submitKesatuan">Edit</b-btn>
                    <b-btn variant="secondary" @click="$refs.modalForm.hide('cancel')">Batal</b-btn>
                </template>
            </b-modal>
        </b-form>
    </div>
</template>
<script>
    import format from 'date-fns/format'
    import {debounce, endsWith, flattenDepth, omit, flatMap, values, merge, chain} from 'lodash'
    import {ModelSelect, MultiSelect} from 'vue-search-select'
    import Swal from 'sweetalert2'

    const dateFnsBahasa = {
        locale: require('date-fns/locale/id')
    }

    export default {
        name: 'kesatuan',
        components: {
            ModelSelect,
            MultiSelect
        },
        data() {
            return {
                totalRows: 0,
                perPage: 10,
                currentPage: 1,
                filter: '',
                filterDebounced: '',
                isBusy: false,
                sortBy: 'id',
                sortDesc: false,
                tableColumns: [
                    {
                        key: 'index',
                        label: 'No.'
                    },
                    {
                        key: 'kesatuan',
                        label: 'Kesatuan',
                        sortable: true
                    },
                    {
                        key: 'parent.kesatuan',
                        label: 'Induk',
                        sortable: true
                    },
                    {
                        key: 'aksi',
                        label: 'Aksi',
                    },
                ],
                indukOptions: [],
                singleKesatuan: {
                    id: '',
                    kesatuan: '',
                    induk: '',
                    banner_grid: ''
                }
            }
        },
        computed: {
            modalTitle() {
                return this.singleKesatuan.id == '' ? 'Tambah kesatuan' : 'Edit kesatuan'
            }
        },
        methods: {
            providerKesatuan(ctx) {
                let sortBy

                switch (ctx.sortBy) {
                    case 'kesatuan':
                        sortBy = 'kesatuan'
                        break
                    case 'induk':
                        sortBy = 'induk'
                        break
                    default:
                        sortBy = ctx.sortBy

                }

                let payload = {
                    page: ctx.currentPage,
                    filter: ctx.filter === '' ? null : ctx.filter,
                    sort: (sortBy != null ? sortBy : 'id') + ':' + (ctx.sortDesc ? 'desc' : 'asc')
                }

                var promise = axios.get('kesatuan/all', {
                    params: payload,
                })
                    .then(({data: {data, meta: {pagination}}}) => {
                        this.totalRows = pagination.total
                        this.perPage = pagination.per_page
                        this.currentPage = pagination.current_page
                        return data
                    })
                    .catch(({response}) => {
                        // Catch error
                        return response
                    })

                return promise
            },
            prepareCreate() {
                this.resetSingleKesatuan()
                this.$refs.modalForm.show()
            },
            submitKesatuan() {
                let data = new FormData()
                // if(document.getElementById('banner').files[0])
                data.append('banner', null)

                let obj = this.singleKesatuan

                Object.keys(obj).forEach(function (key) {
                    data.append(key, obj[key]);
                })

                if (this.singleKesatuan.id) {
                    axios.post('kesatuan/' + this.singleKesatuan.id, data, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then((response) => {
                            this.$toast.success('Data kesatuan ' + this.singleKesatuan.kesatuan + ' berhasil di edit')
                            this.$refs.modalForm.hide()
                            this.resetSingleKesatuan()
                            this.refreshTable()
                        })
                        .catch(({response: {status, data: {errors}}}) => {
                            if (status === 422)
                                this.$toast.error(flattenDepth(values(errors)).join('<br>'))
                        })
                } else {
                    axios.post('kesatuan', data, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then((response) => {
                            this.$toast.success('Data kesatuan berhasil di tambahkan')
                            this.$refs.modalForm.hide()
                            this.resetSingleKesatuan()
                            this.refreshTable()
                        })
                        .catch(({response: {status, data: {errors}}}) => {
                            if (status === 422)
                                this.$toast.error(flattenDepth(values(errors)).join('<br>'))
                        })
                }
            },
            prepareEdit(item) {
                this.singleKesatuan = {
                    id: item.id,
                    kesatuan: item.kesatuan,
                    induk: item.induk,
                    banner_grid: item.banner
                }
                this.$refs.modalForm.show()
            },
            prepareDelete(item) {
                Swal.fire({
                    title: 'Hapus data',
                    text: "Apakah anda yakin menghapus data " + item.kesatuan + "?",
                    icon: 'error',
		            showCancelButton: true,
		            confirmButtonColor: '#dc3545',
		            confirmButtonText: 'Hapus',
		            cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        axios.delete('kesatuan/' + item.id, { 
                            headers: { 'Content-Type': 'multipart/form-data' } 
                        }).then((response) => {
                            this.$toast.success('Data kesatuan ' + item.kesatuan + ' berhasil di hapus')
                            this.refreshTable()
                        })
                        .catch(({ response: { status, data: { errors }}}) => {
                            if (status === 422) 
                                this.$toast.danger('Terjadi kesalahan saat menghapus data')
                        })
                    }
                })
            },
            resetSingleKesatuan() {
                this.singleKesatuan = {
                    id: '',
                    kesatuan: '',
                    induk: '',
                    banner: '',
                    banner_grid: ''
                }
                // this.$refs.banner.reset();
            },
            fetchInduk() {
                var ind = induk
                var explode = ind.split(",")
                var self = this

                explode.forEach(function (key) {
                    self.indukOptions.push({text: key, value: key})
                })
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
            refreshTable () {
				this.totalRows > this.perPage ? 
				(this.currentPage == 1 ? this.$refs.table.refresh() : this.currentPage = 1) 
				: this.$refs.table.refresh()
			},
        },
        mounted() {
            this.fetchInduk()
        }
    }
</script>
