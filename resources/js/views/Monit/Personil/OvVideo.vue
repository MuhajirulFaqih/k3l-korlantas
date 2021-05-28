<template>
    <video autoplay :class="videoClass"/>
</template>

<script>
    export default {
        name: "OvVideo",
        props: {
            streamManager: Object,
            videoClass: String
        },
        mounted() {
            this.streamManager.addVideoElement(this.$el)
        }
    }
</script>

<style scoped>
    video {
        object-fit: cover;
    }
</style>
