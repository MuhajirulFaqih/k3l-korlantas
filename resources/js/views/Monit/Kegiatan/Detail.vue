<template>
    <b-modal ref="detail"
            hide-footer centered
            modal-class="e-modal e-modal-xl"
            title-tag="h4"
            @hide="hideModal"
            title="Detail Kegiatan">
	  	<div class="d-block" v-if="single !== null">
            <b-row>
                <b-col cols="12 py-2">
                    <h4>{{ single.judul }}</h4>
                    <hr/>
                </b-col>
                <b-col cols="6">
                    <b-row>
                        <b-col cols="12">
                            <perfect-scrollbar class="e-data-body">
                                <b-row v-if="single.dokumentasi !== null">
                                    <b-col cols="12">
                                        <b-img :src="single.dokumentasi" alt="" class="w-100"/>
                                    </b-col>
                                </b-row>
                                <b-row v-if="single.keterangan != null">
                                    <b-col cols="12">
                                        <p v-if="single.keterangan !== ''">{{ single.keterangan }}</p>
                                    </b-col>
                                </b-row>
                                <b-row v-if="single.tipe.tipe != null">
                                    <b-col cols="3">Tipe</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.tipe.tipe }}</b></b-col>
                                </b-row>
                                <b-row v-if="single.sasaran != null">
                                    <b-col cols="3">Sasaran</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.sasaran }}</b></b-col>
                                </b-row>
                                <b-row v-if="single.lokasi != null">
                                    <b-col cols="3">Lokasi</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.lokasi }}</b></b-col>
                                </b-row>
                                <b-row v-if="single.kuat_pers != null">
                                    <b-col cols="3">Kuat pers</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.kuat_pers }} Personil</b></b-col>
                                </b-row>
                                <b-row v-if="single.hasil != null">
                                    <b-col cols="3">Hasil</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.hasil }} Personil</b></b-col>
                                </b-row>
                                <b-row v-if="single.jml_giat != null">
                                    <b-col cols="3">Jumlah giat</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.jml_giat }}</b></b-col>
                                </b-row>
                                <b-row v-if="single.jml_tsk != null">
                                    <b-col cols="3">Jumlah tsk</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.jml_tsk }}</b></b-col>
                                </b-row>
                                <b-row v-if="single.bb != null">
                                    <b-col cols="3">Jumlah bb</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.jml_bb }}</b></b-col>
                                </b-row>
                                <b-row v-if="single.perkembangan != null">
                                    <b-col cols="3">Perkembangan</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.perkembangan }}</b></b-col>
                                </b-row>
                                <b-row v-if="single.dasar != null">
                                    <b-col cols="3">Dasar</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.dasar }}</b></b-col>
                                </b-row>
                                <b-row v-if="single.modus != null">
                                    <b-col cols="3">Modus</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.modus }}</b></b-col>
                                </b-row>
                                <b-row v-if="single.tsk_bb != null">
                                    <b-col cols="3">Tsk BB</b-col>
                                    <b-col cols="1">:</b-col>
                                    <b-col cols="8"><b>{{ single.tsk_bb }}</b></b-col>
                                </b-row>
                            </perfect-scrollbar>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="6">
                    <div class="e-comment">
                        <perfect-scrollbar class="e-comment-body" ref="comment">
                            <p class="text-sm-left py-2" v-if="showCommentPage">
                                <span><b-link class="e-text-content" @click="getComment('pagination', single.id)">Lihat komentar sebelumnya...</b-link></span>
                            </p>
                            <center v-show="isBusy" class="my-1">
                                <b-spinner />
                            </center>
                            <div v-for="(k, i) in comment" :key="`comment-${i}`" :class="commentClass(k.user.id)">
                                <b>{{ k.user.nama }}</b>
                                <br/>
                                <small class="elapse">{{ dateFormat(k.w_komentar) }}</small>
                                <p>{{ k.komentar }}</p>
                            </div>
                            <div v-show="isBusyCreate" class="mt-1 mb-5 loading">
                                <b-spinner />
                            </div>
                            <div v-if="comment.length === 0">
                                <p class="text-sm-center pt-2">Belum ada komentar</p>
                            </div>
                        </perfect-scrollbar>
                        <div class="e-comment-form" @submit.prevent="sendComment">
                            <b-form>
                                <b-row>
                                    <b-col cols="9">
                                        <b-form-group>
                                            <b-form-input class="e-form" placeholder="Komentar" v-model="commentText" />
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="3">
                                        <button class="btn e-btn e-btn-primary btn-block" type="submit" :disabled="isBusyCreate">
                                            <ph-paper-plane-tilt class="phospor" />
                                        </button>
                                    </b-col>
                                </b-row>
                            </b-form>
                        </div>
                    </div>
                </b-col>
            </b-row>
	    </div>
        <div v-if="single !== null" class="w-100 mt-4">
            <hr/>
            <b-row>
                <b-col cols="7">
                    <b-row>
                        <b-col cols="1"> <ph-user class="phospor"/> </b-col>
                        <b-col cols="11">
                            <span v-if="single.user.jenis_pemilik !== 'masyarakat'" v-b-tooltip class="cursor-default"
                                :title="'[ ' + single.user.nrp + ' ]\n' + single.user.nama + '\n' + single.user.jabatan">
                                {{ single.user.nama  }}
                            </span>
                            <span v-else >{{ single.user.nama  }}</span>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="5">
                    <b-row>
                        <b-col cols="1"> <ph-phone class="phospor"/> </b-col>
                        <b-col cols="10">
                            <span>{{ single.user.no_telp  }}</span>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
            <b-row>
                <b-col cols="7">
                    <b-row>
                        <b-col cols="1"> <ph-clock class="phospor"/> </b-col>
                        <b-col cols="11">
                            <span>{{ humanizeFormat(single.w_kegiatan) }}</span>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="5">
                    <b-row>
                            <b-col cols="1"> <ph-chat-circle-dots class="phospor"/> </b-col>
                            <b-col cols="10">
                                <span>{{ totalRows + ' komentar' }}</span>
                            </b-col>
                    </b-row>
                </b-col>
            </b-row>
            <hr/>
            <center v-if="single.user.jenis_pemilik == 'personil' || single.user.jenis_pemilik == 'bhabin'">
                <button circle class="btn e-btn e-btn-primary">
                    <ph-video-camera class="phospor"/>
                </button>
            </center>
        </div>
    </b-modal>
</template>

<script>
import { format, formatISO, parseISO } from 'date-fns'
import { debounce, flattenDepth, values } from 'lodash'
import id from 'date-fns/locale/id'
import moment from 'moment'
moment.locale('id')
export default {
    name: 'detail-kegiatan',
    data () {
        return {
            totalRows: 0,
            perPage: 10,
            currentPage: 1,
            isBusy: false,
            isBusyCreate: false,
            single: null,
            commentText: null,
            comment: [],
        }
    },
    computed: {
        showCommentPage () {
            return this.totalRows > this.perPage && this.comment.length !== this.totalRows
        },
    },
    methods : {
        showModal (item) {
            this.totalRows = 0
            this.perPage = 10
            this.currentPage = 1
            this.single = item
            this.getComment('master', item.id)
            this.$refs.detail.show()
        },
        commentClass(id) {
            var id_user_login = this.$store.getters.userInfo.id ? this.$store.getters.userInfo.id : ''
            var cls = (id == id_user_login ) ? 'e-comment-right' : 'e-comment-left';
            return cls;
        },
        dateFormat(item) {
            let value = new Date(item)
            return format(parseISO(formatISO(value, { representation: 'complete' })), 'd MMMM yyyy HH:mm:ss', {locale: id}) 
        },
        humanizeFormat (value) {
            return moment(value).fromNow()
        },
        getComment(from, id) {
            if(this.totalRows !== 0 && (this.totalRows == this.comment.length)) {
                return
            }
            let payload = {
                page: from == 'master' ? this.currentPage : (this.currentPage + 1),
            }
            this.isBusy = true
            axios.get('kegiatan/' + id + '/komentar', {params: payload})
            .then(({ data: { data, meta: { pagination }}}) => {
                this.totalRows = pagination.total
                this.perPage = pagination.per_page
                this.currentPage = pagination.current_page
                var self = this
                if(from == 'master') { 
                    this.comment = data.reverse()
                } else {
                    var komentar = this.comment
                    data.forEach(function(key) {
                        komentar.unshift(key)
                    })
                }
                this.isBusy = false
            }).catch(error => {
                this.totalRows = 0
                return []
            })
        },
        sendComment () {
            if(this.commentText == null) {
                this.$toast.error("Kolom komentar wajib diisi")
                return
            }
            var self = this
            this.isBusyCreate = true
            var id = this.single.id
            var body = {
                komentar: this.commentText,
                kegiatan: id,
            }
            this.$nextTick(() => self.scrollToEnd())
            axios.post('kegiatan/' + id + '/komentar', body)
            .then(({ data }) => {
                var comment = data.data
                this.commentText = null
                if(!(this.totalRows !== 0 && (this.totalRows == this.comment.length))) {
                    this.comment.splice(0, 1)
                }
                this.totalRows++
                this.comment.push(comment)
                this.isBusyCreate = false
            })
            .catch(({ response: { status, data: { errors }}}) => {
                if (status === 422) {
                    this.isBusyCreate = false
                    this.$toast.error(flattenDepth(values(errors)).join('<br>'))
                }
            })
        },
        scrollToEnd: function () {
            this.$refs.comment.$el.scrollTop = 0;
        },
        hideModal() {
            this.single = null
        },
    },
}
</script>