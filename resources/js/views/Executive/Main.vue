<template>
    <div class="executive">
        <router-view ref="main"></router-view>
    </div>
</template>
<script>

export default {
    name: 'executive',
    data () {
        return {

        }
    },
    methods: {
        triggerLogout() {
            this.$parent.logout()
        }
    },
    mounted () {
        document.body.classList.add("e-monit")
    }
}
</script>