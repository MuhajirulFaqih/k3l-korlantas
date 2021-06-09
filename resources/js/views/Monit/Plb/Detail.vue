<template>
    <div>
        <b-modal ref="detail"
            hide-footer centered
            modal-class="e-modal e-modal-sm"
            title-tag="h4"
            @hide="hideModal"
            title="Detail PLB">
            <div class="d-block" v-if="single !== null">
                <div >
                    <b-row class="mb-2">
                        <b-col cols="4">Pengirim</b-col>
                        <b-col cols="8">{{ single.user.nama }}</b-col>
                    </b-row>
                    <b-row class="mb-2">
                        <b-col cols="4">Kesatuan tujuan</b-col>
                        <b-col cols="8">{{ single.tujuan.kesatuan }}</b-col>
                    </b-row>
                    <b-row class="mb-2">
                        <b-col cols="4">Keterangan</b-col>
                        <b-col cols="8">{{ single.keterangan }}</b-col>
                    </b-row>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { format, formatISO, parseISO } from 'date-fns'
import { debounce, flattenDepth, values } from 'lodash'
import id from 'date-fns/locale/id'
import moment from 'moment'
moment.locale('id')
export default {
    name: 'plb',
    data () {
        return {
            single: null,
        }
    },
    methods: {
        showModal (item) {
            this.single = item
            this.$refs.detail.show()
        },
        hideModal() {
            this.single = null
        },
    }
}
</script>