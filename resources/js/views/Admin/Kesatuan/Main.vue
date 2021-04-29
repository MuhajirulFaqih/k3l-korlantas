<template>
    <div>
        <b-row>
            <b-col cols="2" md="2">
                <b-row>
                    <b-col cols="12">
                        <h4 class="d-inline-block mr-3">Kesatuan </h4>
                        <b-button @click="$refs.modalForm.show()" variant="primary" size="sm">
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
                    :per-page="perPage"/>
            </b-col>
            <b-col cols="4" md="4">
                <b-form-input
                    align="right"
                    v-model="filterDebounced"
                    placeholder="Cari Kesatuan, Email Polri, Induk..."/>
            </b-col>
        </b-row>

        <div class="position-relative">
            <b-table
                responsive="responsive"
                ref="table"
                :busy.sync="isBusy"
                :fields="tableColumns"
                :items="providerKesatuan"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc">
                <template v-slot:cell(index)="data">
                    {{ ((currentPage - 1) * 10) + data.index + 1 }}
                </template>
                <template v-slot:cell(aksi)="row">
                    <b-button
                        size="md"
                        v-b-tooltip
                        :title="'Edit data ' + row.item.kesatuan"
                        variant="primary"
                        @click="prepareEdit(row.item)">
                        <ph-pencil class="phospor"/>
                    </b-button>
                    <b-button
                        size="md"
                        v-b-tooltip
                        :title="'Hapus data ' + row.item.kesatuan"
                        variant="danger"
                        @click="prepareDelete(row.item)">
                        <ph-trash class="phospor"/>
                    </b-button>
                </template>
            </b-table>

            <div class="loading" v-show="isBusy">
                <b-spinner variant="primary"></b-spinner>
            </div>
        </div>

        <!-- Modal form -->
        <b-form>
            <b-modal
                ref="modalForm"
                no-close-on-backdrop="no-close-on-backdrop"
                no-close-on-esc="no-close-on-esc"
                title-tag="h4"
                size="lg"
                header-class="red"
                header-text-variant="white"
                :title="modalTitle">
                <b-col cols="12">
                    <b-form-group
                        horizontal="horizontal"
                        :label-cols="2"
                        breakpoint="md"
                        label="Kesatuan">
                        <b-form-input type="text" v-model="singleKesatuan.kesatuan"/>
                    </b-form-group>
                    <b-form-group
                        horizontal="horizontal"
                        :label-cols="2"
                        breakpoint="md"
                        label="Email">
                        <b-form-input type="email" v-model="singleKesatuan.email"/>
                    </b-form-group>
                    <b-form-group
                        horizontal="horizontal"
                        :label-cols="2"
                        breakpoint="md"
                        label="Induk">
                        <model-select
                            v-model="singleKesatuan.induk"
                            :options="indukOptions"
                            placeholder="Pilih induk"/>
                    </b-form-group>
                    <b-form-group
                        horizontal="horizontal"
                        :label-cols="2"
                        breakpoint="md"
                        label="Banner lama"
                        v-if="this.singleKesatuan.id">
                        <h5 v-if="singleKesatuan.banner_grid == null" class="mt-3">
                            <b-badge variant="info">Belum di set</b-badge>
                        </h5>
                        <b-img v-else :src="singleKesatuan.banner_grid" width="300"/>
                    </b-form-group>
                    <b-form-group
                        horizontal="horizontal"
                        :label-cols="2"
                        breakpoint="md"
                        label="Banner">
                        <b-form-file ref="banner" id="banner" accept="image/*"/>
                        <small v-if="this.singleKesatuan.id">*) Kosongi jika banner tidak diganti</small>
                    </b-form-group>
                </b-col>
                <template slot="modal-footer">
                    <b-btn v-if="!singleKesatuan.id" type="submit" variant="primary" @click="submitKesatuan">Simpan</b-btn>
                    <b-btn v-else type="submit" variant="primary" @click="submitKesatuan">Edit</b-btn>
                    <b-btn variant="secondary" @click="$refs.modalForm.hide('cancel')">Batal</b-btn>
                </template>
            </b-modal>
        </b-form>
    </div>
</template>
<script>
    import format from 'date-fns/format'
    import { debounce, endsWith, flattenDepth, omit, flatMap, values, merge, chain } from 'lodash'
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
                sortDesc: true,
                tableColumns: [
					{ key: 'index', label: 'No' },
					{ key: 'kesatuan', label: 'Kesatuan', sortable: true },
					{ key: 'email', label: 'Email Polri', sortable: true },
					{ key: 'induk', label: 'Induk', sortable: true },
					{ key: 'aksi', label: 'Aksi'}
				],
                indukOptions: [],
                singleKesatuan: {
                    id: '',
                    kesatuan: '',
                    email: '',
                    induk: '',
                    banner_grid: ''
                }
            }
        },
        computed: {
            modalTitle() {
                return this.singleKesatuan.id == ''
                    ? 'Tambah kesatuan'
                    : 'Edit kesatuan'
            }
        },
        methods: {
            providerKesatuan(ctx) {
                let sortBy

                switch (ctx.sortBy) {
                    case 'email':
                        sortBy = 'email_polri'
                        break
                    default:
                        sortBy = ctx.sortBy

                }

                let payload = {
                    page: ctx.currentPage,
                    filter: ctx.filter === '' ? null : ctx.filter,
                    sort: ( sortBy != null ? sortBy : 'id' ) + ':' + ( ctx.sortDesc ? 'desc' : 'asc' )
                }

                var promise = axios.get('kesatuan/all', { params: payload })
                    .then(( { data: { data, meta: { pagination } } }) => {
                        this.totalRows = pagination.total
                        this.perPage = pagination.per_page
                        this.currentPage = pagination.current_page
                        return data
                    })
                    .catch(({response}) => {
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
                if (document.getElementById('banner').files[0]) 
                    data.append('banner', document.getElementById('banner').files[0])

                let obj = this.singleKesatuan
                Object.keys(obj).forEach(function (key) {
                    data.append(key, obj[key]);
                })

                if (this.singleKesatuan.id) {
                    axios.post('kesatuan/' + this.singleKesatuan.id, data, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        },
                    })
                    .then((response) => {
                        this.$toast.success( 'Data kesatuan ' + this.singleKesatuan.kesatuan + ' berhasil di edit', {layout: 'topRight'})
                        this.$refs.modalForm.hide()
                        this.resetSingleKesatuan()
                        this.refreshTable()
                    })
                    .catch(({ response: { status, data: { errors } } }) => {
                        if (status === 422) 
                            this.$toast.error(flattenDepth(values(errors)).join('<br>'), {layout: 'topRight'})
                    })
                } else {
                    axios.post('kesatuan', data, { 
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        },
                    })
                    .then((response) => {
                        this.$toast.success('Data kesatuan berhasil di tambahkan', {layout: 'topRight'})
                        this.$refs.modalForm.hide()
                        this.resetSingleKesatuan()
                        this.refreshTable()
                    })
                    .catch(({ response: { status, data: { errors } } }) => {
                        if (status === 422) 
                            this.$toast.error(flattenDepth(values(errors)).join('<br>'), {layout: 'topRight'})
                    })
                }
            },
            prepareEdit(item) {
                this.singleKesatuan = {
                    id: item.id,
                    kesatuan: item.kesatuan,
                    email: item.email,
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
                            this.$toast.success('Data kesatuan ' + item.kesatuan + ' berhasil di hapus', {layout: 'topRight'})
                            this.refreshTable()
                        })
                        .catch(({ response: { status, data: { errors }}}) => {
                            if (status === 422) 
                                this.$toast.danger('Terjadi kesalahan saat menghapus data', {layout: 'topRight'})
                        })
                    }
                })
            },
            resetSingleKesatuan() {
                this.singleKesatuan = {
                    id: '',
                    kesatuan: '',
                    email: '',
                    induk: '',
                    banner: '',
                    banner_grid: ''
                }
                this.$refs.banner.reset();
            },
            fetchInduk() {
                var ind = induk
                var explode = ind.split(",")
                var self = this

                explode.forEach(function (key) {
                    self.indukOptions.push({text: key, value: key})
                })
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
            filterDebounced(newFilter) {
                this.debounceFilter()
            }
        },
        mounted() {
            this.fetchInduk()
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