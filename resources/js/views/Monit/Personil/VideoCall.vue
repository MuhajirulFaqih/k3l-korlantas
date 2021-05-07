<template>
    <div>
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
                            <div class="e-call-waiting" v-if="videoCall.inCall == false && this.call == true">
                                <div class="d-inline-block"><ph-hourglass-medium class="phospor"/></div> Memanggil...
                            </div>
                            <video ref="videoOutput" id="videoOutput" autoplay class="e-person"></video>
                            <div class="e-person-other">
                                <video ref="videoOutputOtherThumb1" id="videoInput" autoplay class="e-person-other-thumb"></video>
                                <!-- <video ref="videoOutputOtherThumb2" id="videoInput2" autoplay class="e-person-other-thumb"></video>
                                <video ref="videoOutputOtherThumb3" id="videoInput3" autoplay class="e-person-other-thumb"></video> -->
                            </div>
                            <button class="btn e-btn e-btn-danger e-call-close" @click="closeCall"><ph-phone-slash class="phospor"/></button>
                        </div>
                    </b-col>
                    <b-col cols="3"></b-col>
                </b-row>
            </div>
        </b-modal>
        <audio :src="ringBackTone" ref="rbt" loop></audio>
    </div>
</template>

<script>
import { format, formatISO, parseISO } from 'date-fns'
import { debounce, flattenDepth, values } from 'lodash'
import kurentoUtils from 'kurento-utils'
import id from 'date-fns/locale/id'
import moment from 'moment'
moment.locale('id')
export default {
    name: 'video-call-personil',
    data () {
        return {
            isBusy: false,
            single: null,
            ringBackTone: ringBackTone,
            call: false,
            videoCall: {
                ws: null,
                echoChannel: null,
                calling: null,
                inCall: false,
                currentCallId: null,
                webRtcPeer: null,
                videoOutput: null,
                videoInput: null
            },
        }
    },
    methods : {
        showModal (item) {
            this.single = item
            this.outGoing(item)
            this.$refs.videoCall.show()
        },
        humanizeFormat (value) {
            return moment(value).fromNow()
        },
        callReady(data) {
            console.log('callReady', data)
            if (data.data.id_personil == this.videoCall.calling.id) {
                this.callPersonil(this.videoCall.calling)
            }
        },
        onCallReady(message) {
            console.log('callReady', message)
            if (message.to === this.videoCall.calling.nrp)
                this.callPersonil(this.videoCall.calling)
        },
        outGoing (personil) {
            this.call = true
            this.videoCall.calling = personil
            this.initVideoCall()
            this.$refs.rbt.play()
            var self = this
            setTimeout(function() {
                if(self.videoCall.inCall == false && this.call == true) {
                    self.stop(true);
                    self.$refs.rbt.pause()
                    self.$refs.rbt.currentTime = 0
                    self.$toast.error('Panggilan tidak dijawab')
                }
            }, 40000)
        },

        endCalling () {
            this.call = false
            this.reject()
            this.videoCall.calling = null
            this.$refs.rbt.pause()
            this.$refs.rbt.currentTime = 0
        },
        requestCall(personil){
            var payload = {
                id_personil : personil.id
            }
            axios.post('call/request', payload)
            .then((response) => {
                this.videoCall.currentCallId = response.data.id
                this.videoCall.calling = personil
            })
            //this.callPersonil(personil)
        },
        initVideoCall(){
            this.videoCall.ws = new WebSocket(wssVc)
            this.videoCall.ws.onmessage = this.onWsMessage
            this.videoCall.ws.onopen = this.onWsOpen
            this.videoCall.ws.onerror = this.onWsError
            this.videoCall.ws.onclose = this.onWsClose
        },
        incomingCall(message) { },
        startCommunication(message){
            this.webRtcPeer.processAnswer(message.sdpAnswer)
        },
        onWsOpen() {
            console.log('ws open')
            this.registerVideoCall()
        },
        onWsClose() {
            console.log('ws Close')
        },
        onWsError(message) {
            console.log('ws error', message)
        },
        onWsMessage(message) {
            var parsedMessage = JSON.parse(message.data)
            console.log(parsedMessage.id, parsedMessage)
            switch (parsedMessage.id) {
                case 'registerResponse':
                    console.log('registerResponse', parsedMessage)
                    if (parsedMessage.response === 'accepted')
                        this.requestCall(this.single)
                    else
                        this.stop(true)
                    break;
                case 'callResponse':
                    this.callResponse(parsedMessage)
                    break;
                case 'incomingCall':
                    this.incomingCall(parsedMessage)
                    break;
                case 'startCommunication':
                    this.startCommunication(parsedMessage)
                    break;
                case 'stopCommunication':
                    this.stop(true)
                    break;
                case 'iceCandidate':
                    this.videoCall.webRtcPeer.addIceCandidate(parsedMessage.candidate)
                    break;
                case 'callReady':
                    this.onCallReady(parsedMessage)
                    break;
                case 'callReject':
                    this.onCallReject(parsedMessage)
                    break;
                default:
                    console.log('Message', parsedMessage)
                    break;
            }
        },
        registerVideoCall(){
            //Todo change name from response user
            var message = {
                id: 'register',
                name: this.$parent.$parent.$parent.$parent.user.username
            }

            this.sendMessage(message)
        },
        onCallReject(message){
            console.log('onCallReject', message)
            this.stop(true)
        },
        reject(){
            console.log("Sending call reject");
            if (this.videoCall.calling != null){
                var data = {
                    id: 'callReject',
                    to: this.videoCall.calling.nrp
                }

                console.log(this.videoCall.calling)
                this.sendMessage(data)
            }
        },
        sendMessage(message){
            var jsonMessage = JSON.stringify(message)
            this.videoCall.ws.send(jsonMessage)
        },
        onIceCandidate (candidate){
            var message = {
                id: 'onIceCandidate',
                candidate: candidate
            }
            this.sendMessage(message)
        },
        stop(message) {
            this.endCalling()
            this.updateCallStatus("end")
            if (this.videoCall.ws != null){
                if(this.videoCall.webRtcPeer){
                    this.videoCall.webRtcPeer.dispose()
                    this.videoCall.webRtcPeer = null
                    if (!message){
                        var message = {
                            id: 'stop'
                        }
                        this.sendMessage(message)
                    }
                }

                this.videoCall.ws.close()
                this.videoCall.ws = null
                this.videoCall.currentCallId = null
                this.videoCall.inCall = false
            }
        },
        updateCallStatus(status){
            if (!this.videoCall.inCall)
                return

            axios.post('call/update', { id: this.videoCall.currentCallId, status: status })
                .then((d) => { })
                .catch((err) => { })
        },
        callResponse(message){
            if(message.response != 'accepted'){
                console.info('Call not accepted by peer. Closing call')
                var errorMessage = message.message ? message.message : 'Unknown reason for call rejection.'
                console.log(errorMessage);
                this.stop(true);
            } else {
                this.videoCall.inCall = true
                this.updateCallStatus("start")
                this.videoCall.webRtcPeer.processAnswer(message.sdpAnswer)
            }

            this.$refs.rbt.pause()
            this.$refs.rbt.currentTime = 0
        },
        callPersonil(personil){
            var options = {
                localVideo: document.getElementById('videoInput'),
                remoteVideo: document.getElementById('videoOutput'),
                onicecandidate: this.onIceCandidate
            }
            var self = this;

            this.videoCall.webRtcPeer = kurentoUtils.WebRtcPeer.WebRtcPeerSendrecv(options, function (error) {
                if(error){
                    //Todo notifikasi
                    console.log('error callPersonil webRtcPeerSendrecv', error)
                    //self.call = false
                }

                //console.log(this)
                this.generateOffer(function (err, offerSdp) {
                    if(err){
                        //Todo Notifikasi
                        console.log('error callPersonil generateOffer', err)
                        //self.call = false
                    }

                    // Todo Change 'from' fetch in userInfo
                    var message = {
                        id: 'call',
                        from: self.user.username,
                        to: personil.nrp,
                        sdpOffer: offerSdp
                    }
                    self.sendMessage(message)
                })
            })
        },
        closeCall () {
            this.stop()
            this.$refs.videoCall.hide()
        }
    },
}
</script>