<template>
    <b-modal ref="detail"
            hide-footer centered
            modal-class="e-modal e-modal-md"
            :no-close-on-backdrop="isBusy"
            :no-close-on-esc="isBusy"
            :hide-header-close="isBusy"
            @hide="hideModal"
            title-tag="h4"
            title="Detail Hotspot">
	  	<div class="d-block pt-5" v-if="single !== null">
            <b-row class="mb-2">
                <b-col cols="4">Satellite</b-col>
                <b-col cols="1">:</b-col>
                <b-col cols="7">{{ single.satellite }}</b-col>
            </b-row>
            <b-row class="mb-2">
                <b-col cols="4">Tingkat kepercayaan</b-col>
                <b-col cols="1">:</b-col>
                <b-col cols="7">{{ getTk(single.tk) }}</b-col>
            </b-row>
            <b-row class="mb-2">
                <b-col cols="4">Sumber</b-col>
                <b-col cols="1">:</b-col>
                <b-col cols="7">{{ single.sumber }}</b-col>
            </b-row>
            <b-row v-if="hotspot !== null" class="mb-2">
                <b-col cols="4">Lokasi</b-col>
                <b-col cols="1">:</b-col>
                <b-col cols="7">{{ hotspot.loc }}</b-col>
            </b-row>
            <b-row v-if="hotspot !== null" class="mb-2">
                <b-col cols="4">Waktu update</b-col>
                <b-col cols="1">:</b-col>
                <b-col cols="7">{{ dateFormat(hotspot.time) }}</b-col>
            </b-row>
	  	</div>
     </b-modal>
</template>

<script>
import id from 'date-fns/locale/id'
import { format, formatISO, parseISO } from 'date-fns'
export default {
    name: 'hotspot',
    data () {
        return {
            isBusy: false,
            single: null,
            hotspot: null,
        }
    },
    methods: {
        getTk (tk) {
            return tk == 7 ? 'Rendah' : (tk == 8 ? 'Sedang' : 'Tinggi')
        },
        dateFormat(item) {
            let value = new Date(item)
            return format(parseISO(formatISO(value, { representation: 'complete' })), 'd MMMM yyyy HH:mm:ss', {locale: id}) 
        },
        detail (item) {
            this.getDetailHotspot(item)
            this.$refs.detail.show()
        },
        getDetailHotspot (item) {
            this.isBusy = true
            axios.get('titik-api/detail/' + item.id)
            .then(({ data }) => {
                this.single = item
                this.hotspot = data
                this.isBusy = false
            })
        },
        hideModal () {
            this.single = null
            this.hotspot = null
        }
    }
}
</script>