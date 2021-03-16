import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";
Vue.use(Vuex)

const store = new Vuex.Store({
    plugins: [createPersistedState()],
    state: {
        user: null,
        loader: false,
    },
    getters: {
        userInfo: state => state.user,
        loaderStatus: state => state.loader,
    },
    mutations: {
        setUser (state, user){
            state.user = user
        },
        removeUser (state){
            state.user = null
        },
        setLoader (state, value){
            state.loader = value
        },
        removeLoader (state){
            state.loader = false
        }
    }
})

export default store;