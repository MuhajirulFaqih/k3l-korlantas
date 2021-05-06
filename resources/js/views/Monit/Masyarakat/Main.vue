<template>
    <b-modal ref="detail"
            hide-footer centered
            modal-class="e-modal e-modal-sm"
            title-tag="h4"
            @hide="hideModal"
            title="Detail Masyarakat">
	  	<div class="d-block" v-if="single !== null">
            <b-row class="mt-4">
                <b-col cols="4">Nik</b-col>
                <b-col cols="8">
                    <span v-if="single.user.nrp != null">{{ single.user.nrp }}</span>
                    <span v-else>-</span>
                </b-col>
            </b-row>
            <b-row class="mt-4">
                <b-col cols="4">Nama</b-col>
                <b-col cols="8">
                    <span v-if="single.user.nama != null">{{ single.user.nama }}</span>
                    <span v-else>-</span>
                </b-col>
            </b-row>
            <b-row class="mt-4">
                <b-col cols="4">No Telepon</b-col>
                <b-col cols="8">
                    <span v-if="single.user.no_telp != null">{{ single.user.no_telp }}</span>
                    <span v-else>-</span>
                </b-col>
            </b-row>
            <b-row class="mt-4">
                <b-col cols="4">Alamat</b-col>
                <b-col cols="8">
                    <span v-if="single.user.alamat != null">{{ single.user.alamat }}</span>
                    <span v-else>-</span>
                </b-col>
            </b-row>
	  	</div>
    </b-modal>
</template>

<script>
export default {
    name: 'masyarakat',
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