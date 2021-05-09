<template>
    <div>
        <div class="page-loader" v-if="isLoading">
            <!-- <spinner :speed=".8" :size="30" /> -->
        </div>
        <router-view></router-view>
        <div class="screen"><span>Aplikasi hanya diperuntukkan untuk desktop yang lebih besar</span></div>
    </div>
</template>

<script>
export default {
    name: 'app',
    data: () => {
        return {
        }
    },
    computed: {
        isLoading: function() {
            return this.$store.getters.loaderStatus
        }
    },
    methods: {
        logout () {
            // this.$router.push({ name: 'Login' })
            axios.get('user/logout-admin')
            .then(({ data }) => {
                this.$store.commit('removeUser')
                delete axios.defaults.headers.common['Authorization']
                localStorage.removeItem('token')
                location.reload()
            })
        },
    },
}
</script>