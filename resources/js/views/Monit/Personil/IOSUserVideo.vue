<template>
    <div :class="videoClass" v-if="streamManager">
        <div class="e-call-ios-square-options">{{ clientData.nama }}</div>
        <IOSOvVideo :streamManager="streamManager"/>
    </div>
</template>

<script>
    import IOSOvVideo from "./IOSOvVideo";
    export default {
        name: "IOSUserVideo",
        components: {
            IOSOvVideo
        },
        props: {
            streamManager: Object,
            videoClass: String
        },
        computed: {
            clientData() {
                const { clientData } = this.getConnectionData()
                return clientData
            }
        },
        methods: {
            getConnectionData(){
                const { connection } = this.streamManager.stream
                return JSON.parse(connection.data)
            }
        }
    }
</script>

<style scoped>

</style>
