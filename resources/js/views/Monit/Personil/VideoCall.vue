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
                            <div class="e-call-waiting" v-if="inCall == false && this.call == true">
                                <div class="d-inline-block">
                                    <ph-hourglass-medium class="phospor"/>
                                </div>
                                Memanggil...
                            </div>
                            <user-video :stream-manager="mainStreamManager" video-class="e-person"/>
                            <video ref="videoOutput" id="videoOutput" autoplay class="e-person"></video>
                            <div class="e-person-other" id="video-container">
                                <user-video :stream-manager="publisher"
                                            @click.native="updateMainVideoStreamer(publisher)"
                                            video-class="e-person-other-thumb"/>
                                <user-video v-for="sub in subscribers" :key="sub.stream.connection.connectionId"
                                            :stream-manager="sub" @click.native="updateMainVideoStreamer(sub)"/><!--
                                <video ref="videoOutputOtherThumb1" id="videoInput" autoplay
                                       class="e-person-other-thumb"></video>-->
                                <!-- <video ref="videoOutputOtherThumb2" id="videoInput2" autoplay class="e-person-other-thumb"></video>
                                <video ref="videoOutputOtherThumb3" id="videoInput3" autoplay class="e-person-other-thumb"></video> -->
                            </div>
                            <button class="btn e-btn e-btn-danger e-call-close" @click="closeCall">
                                <ph-phone-slash class="phospor"/>
                            </button>
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
    import {format, formatISO, parseISO} from 'date-fns'
    import {debounce, flattenDepth, values} from 'lodash'
    import {OpenVidu} from 'openvidu-browser'
    import UserVideo from './UserVideo'
    import id from 'date-fns/locale/id'
    import moment from 'moment'

    moment.locale('id')
    export default {
        name: 'video-call-personil',
        data() {
            return {
                isBusy: false,
                single: null,
                ringBackTone: ringBackTone,
                call: false,
                OV: undefined,
                session: undefined,
                mainStreamManager: null,
                publisher: null,
                subscribers: [],
                inCall: false
            }
        },
        components: {
            UserVideo
        },
        methods: {
            showModal (item) {
                this.single = item
                let self = this
                setTimeout(function() {
                    self.joinSession()
                    self.$refs.videoCall.show()
                }, 500)
            },
            joinSession() {
                this.OV = new OpenVidu()
                this.OV.enableProdMode()
                this.session = this.OV.initSession()

                this.session.on('streamCreated', ({stream}) => {
                    const subscriber = this.session.subscribe(stream)
                    this.subscribers.push(subscriber)
                })

                this.session.on('streamDestroyed', ({stream}) => {
                    const index = this.subscriber.indexOf(stream.streamManager, 0)
                    if (index >= 0) {
                        this.subscribers.splice(index, 1)
                    }
                })

                this.getToken().then(token => {
                    this.session.connect(token, {clientData: JSON.stringify({  })})
                        .then(() => {
                            let publisher = this.OV.initPublisher(undefined, {
                                audioSource: undefined,
                                videoSource: undefined,
                                publishAudio: true,
                                publishVideo: true,
                                resolution: '640x480',
                                frameRate: 30,
                                insertMode: 'APPEND',
                                mirror: false
                            })

                            this.mainStreamManager = publisher
                            this.publisher = publisher

                            this.session.publish(this.publisher)
                        })

                        this.callPersonil(this.single.nrp)
                    .catch(error => {
                        console.log("Error get connection :", error.code, error.message)
                    })
                })

                window.addEventListener('beforeunload', this.leaveSession)
            },
            callPersonil(nrp){
                axios.post('call/notify-personil', {
                    nrp: nrp
                })
                .then((data) => {

                })
            },
            leaveSession() {
                if (this.session) this.session.disconnect()

                this.session = undefined
                this.mainStreamManager = undefined
                this.publisher = undefined
                this.subscribers = []
                this.OV = undefined
                this.$refs.videoCall.hide()

                window.removeEventListener('beforeunload', this.leaveSession)
            },
            closeCall(){
                this.leaveSession()
            },
            updateMainVideoStreamer(stream) {
                if (this.mainStreamManager === stream) return
                this.mainStreamManager = stream
            },
            getToken() {
                return new Promise((resolve, reject) => {
                    axios.post(baseUrl + '/api/call/admin')
                        .then(response => response.data)
                        .then(data => resolve(data.token))
                        .catch(error => reject(error.response))
                })
            }
        },
    }
</script>
