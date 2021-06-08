<template>
    <b-modal ref="add"
            hide-footer centered
            title-tag="h4"
            modal-class="e-modal-ios"
            @hide="hideModal"
            title="Pilih personil">
	  	<div class="d-block">
            <b-row class="mb-4">
                <b-col cols="8">Personil (423443)</b-col>
                <b-col cols="4 text-right text-success"><ph-phone-call class="phospor"/></b-col>
            </b-row>
            <b-row class="mb-4">
                <b-col cols="8">Personil (423442)</b-col>
                <b-col cols="4 text-right text-success"><ph-phone-call class="phospor"/></b-col>
            </b-row>
            <b-row class="mb-4">
                <b-col cols="8">Personil (423441)</b-col>
                <b-col cols="4 text-right text-success"><ph-phone-call class="phospor"/></b-col>
            </b-row>
            <b-row class="mb-4">
                <b-col cols="8">Personil (423440)</b-col>
                <b-col cols="4 text-right text-success"><ph-phone-call class="phospor"/></b-col>
            </b-row>
	  	</div>
    </b-modal>
</template>

<script>
export default {
    name: 'add-subscriber',
    data () {
        return {
            data: null
        }
    },
    methods: {
        showModal () {
            this.$refs.add.show()
        },
        hideModal() {
            this.data = null
        },
    }
}
</script>