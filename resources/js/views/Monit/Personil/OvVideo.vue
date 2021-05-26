<template>
    <video autoplay class="e-person-other-thumb"/>
</template>

<script>
    export default {
        name: "OvVideo",
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
