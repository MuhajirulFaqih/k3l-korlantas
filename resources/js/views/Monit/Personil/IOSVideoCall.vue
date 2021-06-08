<template>
    <div :class="lastSquareClass">
        <div class="e-call-ios-add-contact" @click="addSubscribers">
            <ph-user-circle-plus class="phospor"/>
        </div>
        <div class="e-call-ios-video">
            <div :class="publisherClass">
                <div class="e-call-ios-square-options">Person</div>
                <video src="http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4" controls autoplay loop class="d-block w-100 h-100"></video>
            </div>
            <div v-for="(sub, key) in subscribers" :key="key" :class="subscriberClass">
                <div class="e-call-ios-square-options">Person <ph-x-circle class="phospor" @click="takeOut"/></div>
                <video src="http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4" controls autoplay class="d-block w-100 h-100"></video>
            </div>
        </div>
        <div class="e-call-ios-action">
            <ph-camera class="phospor e-call-ios-icon e-call-ios-camera" @click="toggleCamera"/>
            <button class="btn e-btn e-btn-danger e-call-ios-close">
                <ph-phone-slash class="phospor"/>
            </button>
            <ph-microphone-slash class="phospor e-call-ios-icon e-call-ios-microphone" v-if="microphone" @click="toggleMicrophone"/>
            <ph-microphone class="phospor e-call-ios-icon e-call-ios-microphone" v-else @click="toggleMicrophone"/>
        </div>
        <IOSVideoCallAddSubscribers ref="add"/>
    </div>
</template>

<script>
import IOSVideoCallAddSubscribers from './IOSVideoCallAddSubscribers'
export default {
    name: 'ios-video-call',
    data () {
        return {
            subscribers: [ 0 ],
            publisher: 1,
            microphone: true,
        }
    },
    components: { IOSVideoCallAddSubscribers },
    computed: {
        publisherClass () {
            return this.subscribers.length == 1 ? 'e-call-ios-thumb' : 
                (this.subscribers.length < 4 ? 'e-call-ios-square e-call-ios-square-50' : 
                'e-call-ios-square e-call-ios-square-33')
        },
        subscriberClass () {
            return this.subscribers.length == 1 ? 'e-call-ios-big' : 
                (this.subscribers.length < 4 ? 'e-call-ios-square e-call-ios-square-50' : 
                'e-call-ios-square e-call-ios-square-33')
        },
        lastSquareClass () {
            return this.subscribers.length % 2 == 0 ? 'e-call-ios e-call-ios-flex-last' : 'e-call-ios'
        }
    },
    methods: {
        toggleMicrophone () {
            this.microphone = !this.microphone
        },
        toggleCamera () {
            this.toggleCamera
        },
        takeOut () {
            this.subscribers++
        },
        addSubscribers () {
            this.$refs.add.showModal()
        }
    }
}
</script>