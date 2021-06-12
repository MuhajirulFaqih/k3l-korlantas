<template>
    <div :class="lastSquareClass">
        <!--<div class="e-call-ios-add-contact" @click="addSubscribers">
            <ph-user-circle-plus class="phospor"/>
        </div>-->
        <div class="e-call-ios-video">
            <IOSUserVideo :video-class="publisherClass" :stream-manager="publisher"/>
            <IOSUserVideo v-for="sub in subscribers" :key="sub.stream.connection.connectionId" :video-class="subscriberClass" :stream-manager="sub"/>
        </div>
        <div class="e-call-ios-action">
            <ph-camera class="phospor e-call-ios-icon e-call-ios-camera" @click="toggleCamera"/>
            <button class="btn e-btn e-btn-danger e-call-ios-close" @click="leaveSession">
                <ph-phone-slash class="phospor"/>
            </button>
            <ph-microphone-slash class="phospor e-call-ios-icon e-call-ios-microphone" v-if="microphone"
                                 @click="toggleMicrophone"/>
            <ph-microphone class="phospor e-call-ios-icon e-call-ios-microphone" v-else @click="toggleMicrophone"/>
        </div>
        <IOSVideoCallAddSubscribers ref="add"/>
    </div>
</template>

<script>
    import IOSVideoCallAddSubscribers from './IOSVideoCallAddSubscribers'
    import IOSUserVideo from "./IOSUserVideo";
    import {OpenVidu} from 'openvidu-browser'
    import LaravelEcho from "laravel-echo"

    export default {
        name: 'ios-video-call',
        data() {
            return {
                subscribers: [],
                publisher: null,
                id_admin: null,
                microphone: true,
                session: undefined,
                outgoing: true,
                incoming: false,
                OV: undefined,
                sessionId: null,
                isFrontCamera: false,
                single: null,
                token: null,
                echo: undefined
            }
        },
        components: {IOSVideoCallAddSubscribers, IOSUserVideo},
        computed: {
            publisherClass() {
                return this.subscribers.length == 0 ? this.subscribers.length == 0 ? 'e-call-ios-big' : 'e-call-ios-thumb' :
                    (this.subscribers.length < 4 ? 'e-call-ios-square e-call-ios-square-50' :
                        'e-call-ios-square e-call-ios-square-33')
            },
            subscriberClass() {
                return this.subscribers.length == 1 ? 'e-call-ios-big' :
                    (this.subscribers.length < 4 ? 'e-call-ios-square e-call-ios-square-50' :
                        'e-call-ios-square e-call-ios-square-33')
            },
            lastSquareClass() {
                return this.subscribers.length % 2 == 0 ? 'e-call-ios e-call-ios-flex-last' : 'e-call-ios'
            }
        },
        methods: {
            toggleMicrophone() {
                this.publisher.publishAudio(this.microphone)
                this.microphone = !this.microphone
            },
            toggleCamera() {
                this.OV.getDevices().then(devices => {
                    var videoDevices = devices.filter(device => device.kind === 'videoInput')

                    if (videoDevices && videoDevices.length > 1){
                        var newPublisher = this.OV.initPublisher(undefined, {
                            audioSource: undefined,
                            videoSource: this.isFrontCamera ? videoDevices[1].deviceId : videoDevices[0].deviceId,
                            publishAudio: true,
                            publishVideo: true,
                            resolution: '640x480',
                            frameRate: 30,
                            insertMode: 'APPEND',
                            mirror: this.isFrontCamera
                        })

                        this.isFrontCamera = !this.isFrontCamera

                        this.session.unpublish(this.publisher)

                        this.publisher = newPublisher

                        this.session.publisher(this.publisher)
                    }
                })
            },
            joinSession(token) {
                this.OV = new OpenVidu()
                this.OV.enableProdMode()
                this.session = this.OV.initSession()

                let self = this

                this.session.on('streamCreated', ({stream}) => {
                    const subscriber = this.session.subscribe(stream)
                    self.subscribers.push(subscriber)
                })

                this.session.on('streamDestroyed', ({stream}) => {
                    const index = self.subscribers.indexOf(stream.streamManager, 0)
                    if (index >= 0)
                        this.subscribers.slice(index, 1)
                })

                this.session.on('sessionDisconnected', ({reason}) => {
                    if (reason == 'sessionClosedByServer')
                        this.leaveSession()
                })

                var clientData = {
                    username: this.single.username,
                    jenis_pemilik: this.single.jenis_pemilik,
                    nama: this.single.jenis_pemilik == 'personil' ? this.single.pemilik.pangkat + ' ' + this.single.pemilik.nama : this.single.pemilik.nama,
                    jabatan: this.single.jenis_pemilik == 'personil' ? this.single.pemilik.jabatan : 'Admin',
                    kesatuan: this.single.jenis_pemilik == 'admin' ? 'Administartor' : this.single.pemilik.kesatuan
                }

                if (token){
                    this.session.connect(token, {clientData: JSON.stringify(clientData)})
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

                            this.publisher = publisher

                            this.session.publish(this.publisher)
                        })
                        .catch(error => {
                            console.error("Error getting connection")
                        })
                } else {
                    this.getToken().then(token => {
                        this.session.connect(token, {clientData: JSON.stringify(clientData)})
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

                                this.publisher = publisher

                                this.session.publish(this.publisher)
                            })
                            .catch(error => {
                                console.error("Error getting connection")
                            })
                    })
                }

                window.addEventListener("beforeunload", this.leaveSession)
            },
            leaveSession(){
                if (this.session) this.session.disconnect()

                this.session = undefined
                this.publisher = undefined
                this.subscribers = []
                this.incoming =  false
                this.sessionId = null
                this.OV = undefined

                window.removeEventListener('beforeunload', this.leaveSession)
            },
            requestCall() {
                axios.post('call/personil', {
                    id_admin: this.id_admin
                }, {
                    headers: {
                        Authorization: this.token
                    }
                })
                    .then(({data}) => {

                    })
            },
            initLaravelEcho(){
                var self = this

                this.echo = new LaravelEcho({
                    broadcaster: "socket.io",
                    host: socketUrl,
                    auth: { headers: { Authorization: this.token }}
                })

                this.echo.private(socketPrefix+":Personil."+this.single.pemilik.id)
                    .listen('.vcon-admin-ready', this.adminReady)
                    .listen('.vcon-admin-rejected', this.adminRejected)
            },
            adminReady({ data }){
                console.log(data)
                this.joinSession(data.token)
            },
            adminRejected(){
                this.leaveSession()
            },
            getToken() {
                return new Promise((resolve, reject) => {
                    axios.post('call/personil', {
                        id_admin: this.id_admin
                        }, {
                            headers : {
                                Authorization: this.token
                            }
                        })
                        .then(response => response.data)
                        .then(data => {
                            this.sessionId = data.sessionId
                            return data
                        })
                        .then(data => resolve(data.token))
                        .catch(error => reject(error.response))
                })
            },
            requestUserInfo() {
                axios.get('user', {
                    headers: {
                        Authorization: this.token
                    }
                })
                    .then(({data: {data}}) => {
                        this.single = data
                        if (this.sessionId) {
                            this.joinSession()
                        } else {
                            this.requestCall()
                            this.initLaravelEcho()
                        }
                    })
                .catch(({response}) => {
                    console.log(response)
                })
            }
        },
        mounted() {
            this.token = 'Bearer ' +this.$route.query.token

            this.incoming = this.$route.query.incoming
            this.id_admin = this.$route.query.id

            this.requestUserInfo()
        }
    }
</script>
