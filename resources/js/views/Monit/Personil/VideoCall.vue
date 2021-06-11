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
                            <div class="e-call-waiting" v-if="incoming">
                                <div class="d-inline-block">
                                    <ph-hourglass-medium class="phospor"/>
                                </div>
                                Panggilan masuk. Dari {{ single.nama }}
                            </div>
                            <user-video :stream-manager="mainStreamManager" video-class="e-person"/>
                            <div class="e-person-other" id="video-container">
                                <user-video :stream-manager="publisher"
                                            @click.native="updateMainVideoStreamer(publisher)"
                                            video-class="e-person-other-thumb"/>
                                <user-video v-for="sub in subscribers" :key="sub.stream.connection.connectionId"
                                            :stream-manager="sub" video-class="e-person-other-thumb" @click.native="updateMainVideoStreamer(sub)"/>
                            </div>
                            <button class="btn e-btn e-btn-success e-call-close" @click="answerCall" v-if="incoming && !answered">
                                <ph-phone-slash class="phospor"/>
                            </button>
                            <button class="btn e-btn e-btn-danger e-call-close" @click="closeCall" v-if="!incoming && answered">
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
                sessionId: null,
                timer: null,
                incoming: false,
                answered: false,
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
            incomingCall (item){
                this.single = item
                this.incoming = true
                let self = this
                setTimeout( function () {
                    self.runIncoming()
                    self.$refs.videoCall.show()
                }, 500)
            },
            runIncoming(){
                this.$refs.rbt.play()
                this.timer = setTimeout(() => {
                    this.sendRejectPersonil()
                    this.leaveSession()
                }, 40000)
            },
            joinSession() {
                this.OV = new OpenVidu()
                this.OV.enableProdMode()
                this.session = this.OV.initSession()

                let self = this

                if (this.timer){
                    clearTimeout(this.timer)
                }

                this.session.on('streamCreated', ({stream}) => {
                    if (this.timer != null){
                        clearTimeout(this.timer)
                        this.incoming = false
                        this.$refs.rbt.pause()
                    }

                    const subscriber = this.session.subscribe(stream)
                    self.subscribers.push(subscriber)
                })

                this.session.on('streamDestroyed', ({stream}) => {
                    const index = self.subscribers.indexOf(stream.streamManager, 0)
                    if (index >= 0) {
                        this.subscribers.splice(index, 1)
                    }

                    if (self.subscribers.length == 0){
                        self.timer = setTimeout(() => {
                            self.leaveSession()
                            self.timer = null
                        }, 10000)
                    }
                })

                this.session.on('sessionDisconnected', ({ reason }) => {
                    if (reason == 'sessionClosedByServer')
                        this.leaveSession()
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

                            if (!this.incoming)
                                this.callPersonil()
                            else
                                this.sendSocketPersonil()

                            this.timer = setTimeout(() => {
                                this.leaveSession()
                                this.timer = null
                            }, 30000)
                        })
                        .catch(error => {
                            console.log("Error get connection :", error.code, error.message)
                        })
                })

                window.addEventListener('beforeunload', this.leaveSession)
            },
            sendSocketPersonil(){
                axios.post('call/notify-personil', {
                    nrp: this.single.nrp,
                    session_id: this.sessionId,
                    ready: true,
                }).then((data) => {

                })
            },
            callPersonil(){
                axios.post('call/notify-personil', {
                    nrp: this.single.nrp,
                    session_id: this.sessionId
                })
                .then((data) => {

                })
            },
            sendRejectPersonil() {
                axios.post("call/notify-personil", {
                    nrp: this.single.nrp,
                    rejected: true
                })
                .then((data) => {

                })
            },
            leaveSession() {
                if (this.timer != null){
                    clearTimeout(this.timer)
                    this.timer = null
                }

                if (this.session) this.session.disconnect()

                if (this.sessionId != null)
                    this.endSession()
                this.$refs.rbt.pause()
                this.incoming = false
                this.session = undefined
                this.mainStreamManager = undefined
                this.publisher = undefined
                this.subscribers = []
                this.sessionId = null
                this.OV = undefined
                this.$refs.videoCall.hide()

                window.removeEventListener('beforeunload', this.leaveSession)
            },
            endSession(){
                axios.delete('call/end-session/'+ this.sessionId)
                    .then(({ data }) => {

                    })
            },
            answerCall(){
                this.$refs.rbt.pause()
                clearTimeout(this.timer)
                this.joinSession()
                this.answered = true
            },
            closeCall(){
                this.leaveSession()
            },
            updateMainVideoStreamer(stream) {
                if (this.mainStreamManager == stream) return
                this.mainStreamManager = stream
            },
            getToken() {
                return new Promise((resolve, reject) => {
                    axios.post(baseUrl + '/api/call/admin')
                        .then(response => response.data)
                        .then(data => {
                            this.sessionId = data.sessionId
                            return data
                        })
                        .then(data => resolve(data.token))
                        .catch(error => reject(error.response))
                })
            }
        },
    }
</script>
