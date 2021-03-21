<template>
    <b-modal ref="detail"
            hide-footer centered
            modal-class="e-modal e-modal-xl"
            title-tag="h4"
            @hide="hideModal"
            title="Detail Pengaduan">
	  	<div class="d-block" v-if="single !== null">
            <b-row>
                <b-col cols="12 py-2">
                    <h4>{{ single.keterangan }}</h4>
                    <hr/>
                </b-col>
                <b-col cols="6">
                    <b-row>
                        <b-col cols="12">
                            <perfect-scrollbar class="e-data-body">
                                <b-row v-if="single.foto !== null">
                                    <b-col cols="12">
                                        <b-img :src="single.foto" alt="" class="w-100"/>
                                    </b-col>
                                </b-row>
                                <b-row v-if="single.keterangan != null">
                                    <b-col cols="12">
                                        <p v-if="single.lokasi !== ''">{{ single.lokasi }}</p>
                                    </b-col>
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
                            <span>{{ humanizeFormat(single.created_at) }}</span>
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
    name: 'detail-pengaduan',
    data () {
        return {
            totalRows: 0,
            perPage: 10,
            currentPage: 1,
            isBusy: false,
            isBusyCreate: false,
            single: null,
            commentText: null,
            from: null,
            comment: [],
        }
    },
    computed: {
        showCommentPage () {
            return this.totalRows > this.perPage && this.comment.length !== this.totalRows
        },
    },
    methods : {
        showModal (item, type) {
            this.totalRows = 0
            this.perPage = 10
            this.currentPage = 1
            this.single = item
            this.getComment('master', item.id)
            this.from = type
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
            axios.get('pengaduan/' + id + '/komentar', {params: payload})
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
            axios.post('pengaduan/' + id + '/komentar', body)
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
            this.$refs.detail.hide()
            this.single = null
            let self = this
            if(this.from !== 'marker') {
                setTimeout(function() {
                    self.$parent.showModal()
                }, 500)
            }
        },
    },
}
</script>