<template>
    <div v-if="streamManager">
        <!-- Todo Ubah css -->
        <b-dropdown>
            <b-dropdown-item>Bisukan</b-dropdown-item>
            <b-dropdown-item>Keluarkan</b-dropdown-item>
        </b-dropdown>
        <ov-video :stream-manager="streamManager" :video-class="videoClass" :muted="muted"/>
        <div><p>{{ clientData.nama }}</p></div>
    </div>
</template>

<script>
    import OvVideo from './OvVideo'
    export default {
        name: "UserVideo",
        components: {
            OvVideo
        },
        props: {
            streamManager: Object,
            videoClass: String,
            muted: Boolean
        },
        computed: {
            clientData() {
                const { clientData } = this.getConnectionData()
                return clientData
            }
        },
        methods: {
            getConnectionData() {
                const { connection } = this.streamManager.stream
                return JSON.parse(connection.data)
            }
        }
    }
</script>

<style scoped>

</style>
