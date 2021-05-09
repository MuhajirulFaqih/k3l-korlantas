<template>
    <b-modal ref="detail"
            hide-footer centered
            modal-class="e-modal e-modal-sm"
            title-tag="h4"
            @hide="hideModal"
            title="Detail Lokasi">
	  	<div class="d-block" v-if="single !== null">
            <b-row class="mt-4">
                <b-col cols="12"><h3>{{ single.nama_tempat }}</h3></b-col>
            </b-row>
            <b-row class="mt-4">
                <b-col cols="4">Lokasi</b-col>
                <b-col cols="8">{{ single.lokasi }}</b-col>
            </b-row>
            <b-row class="mt-4">
                <b-col cols="4">Jajaran</b-col>
                <b-col cols="8">{{ single.jajaran == null ? instansi : single.jajaran.kesatuan }}</b-col>
            </b-row>
	  	</div>
    </b-modal>
</template>

<script>
export default {
    name: 'lokasi-vital',
    data () {
        return {
            single: null
        }
    },
    methods: {
        detail (item) {
            this.single = item
            this.$refs.detail.show()
        },
        hideModal() {
            this.single = null
        },
    }
}
</script>