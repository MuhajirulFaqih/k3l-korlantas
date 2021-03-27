<template>
    <b-modal ref="videoCall"
        hide-footer centered
        modal-class="e-modal e-modal-mg"
        title-tag="h4"
        no-close-on-backdrop
        no-close-on-esc
        hide-header-close
        title="Video Call">
	  	<div class="d-block" v-if="single !== null">
            <b-row>
                <b-col cols="9">
                    <div class="e-call">
                        <video ref="videoOutput" id="videoOutput" autoplay class="e-person"></video>
                        <div class="e-person-other">
                            <video ref="videoOutputOtherThumb1" id="videoOutputOtherThumb1" autoplay class="e-person-other-thumb"></video>
                            <video ref="videoOutputOtherThumb2" id="videoOutputOtherThumb2" autoplay class="e-person-other-thumb"></video>
                            <video ref="videoOutputOtherThumb3" id="videoOutputOtherThumb3" autoplay class="e-person-other-thumb"></video>
                        </div>
                        <button class="btn e-btn e-btn-danger e-call-close" @click="closeCall"><ph-phone-slash class="phospor"/></button>
                    </div>
                </b-col>
                <b-col cols="3"></b-col>
            </b-row>
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
    name: 'video-call-personil',
    data () {
        return {
            isBusy: false,
            single: null
        }
    },
    methods : {
        showModal (item) {
            this.single = item
            this.$refs.videoCall.show()
        },
        humanizeFormat (value) {
            return moment(value).fromNow()
        },
        closeCall () {
            this.$refs.videoCall.hide()
        }
    },
}
</script>