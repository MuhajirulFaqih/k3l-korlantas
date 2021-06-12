<template>
    <video autoplay class="d-block w-100 h-100"></video>
</template>

<script>
    export default {
        name: "IOSOvVideo",
        props: {
            streamManager: Object
        },
        mounted() {
            this.streamManager.addVideoElement(this.$el)
        }
    }
</script>

<style scoped>

</style>
