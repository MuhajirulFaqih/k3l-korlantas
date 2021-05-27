<template>
  <div class="wrapper admin">
    <Sidebar ref="sidebar"/>
    <div id="content">
        <div class="top">
            <button 
            type="button" 
            id="sidebarCollapse"
            v-on:click="toggleSidebar"
            v-bind:class="{ active: isActive }" 
            class="navbar-btn">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="control-right">
                <span class="author">
                    <span class="d-inline-block mr-3"><ph-user class="phospor"/> {{ namaUser }} </span>
                    <span class="d-inline-block mr-3"><router-link to="/"><ph-monitor class="phospor"/> Monit</router-link></span>
                    <span class="d-inline-block mr-3"><a href="javascript:void(0)" @click="showModalUbahPassword"><ph-key class="phospor"/> Ubah Password</a></span>
                </span>
                <a href="#" @click="triggerLogout" title="Logout">
                    <ph-sign-out class="phospor"/>
                </a>
            </div>
        </div>
        <b-container fluid class="body pt-3 pl-4" v-bind:class="{ active: isActive }">
            <router-view></router-view>
        </b-container>
    </div>
    <b-modal ref="modalUbahPassword" hide-footer title="Ubah Password">
        <b-col cols="12">
            <b-form-group
                horizontal
                :label-cols="4"
                breakpoint="md"
                label="Username">
                <b-form-input type="text" id="username-input" :value="username" />
            </b-form-group>
            <b-form-group
                horizontal
                :label-cols="4"
                breakpoint="md"
                label="Password lama">
                <b-form-input type="password" v-model="singlePassword.password_lama" />
            </b-form-group>
            <b-form-group
                horizontal
                :label-cols="4"
                breakpoint="md"
                label="Password baru">
                <b-form-input type="password" v-model="singlePassword.password_baru" />
            </b-form-group>
            <b-form-group
                horizontal
                :label-cols="4"
                breakpoint="md"
                label="Konfirmasi password baru">
                <b-form-input type="password" v-model="singlePassword.konfirmasi_password_baru" />
            </b-form-group>
            <hr/>
            <center class="mb-2">
                <b-button variant="primary" sm @click="ubahPassword">Ubah Password</b-button>
            </center>
        </b-col>
    </b-modal>
  </div>
</template>

<script>
    import Sidebar from './Sidebar'
    import { debounce, flattenDeep, values } from 'lodash'

    export default {
        name: 'frame',
        components: {
            Sidebar
        },
        data () {
            return {
                isActive: false,
                singlePassword: {
                    username: '',
                    password_lama: '',
                    password_baru: '',
                    konfirmasi_password_baru: '',
                }
            }
        },
        methods: {
            toggleSidebar () {
                this.isActive = !this.isActive
                this.$refs.sidebar.isActive = this.isActive
            },
            showModalUbahPassword () {
                this.$refs.modalUbahPassword.show()
            },
            ubahPassword () {
                this.singlePassword.username = document.getElementById("username-input").value
                axios.post(baseUrl + '/api/user/ubah-password-admin', this.singlePassword , {
                    headers: { Authorization: sessionStorage.getItem('token'), }
                })
                .then(({data}) => {
                    this.$noty.success('Data personil berhasil diubah')
                    setTimeout(function(){
                        location.reload()
                    }, 3000)
                })
                .catch(({response}) => {
                    if (response.status === 422) {
                        this.$noty.error(flattenDeep(values(response.data.errors)).join('<br>'))
                    }
                })
            },
            triggerLogout() {
                this.$parent.logout()
            }
        },
        computed: {
            namaUser (){
                return this.$store.getters.userInfo !== null ? (this.$store.getters.userInfo.pemilik ? this.$store.getters.userInfo.pemilik.nama : '') : ''
            },
            username (){
                return this.$store.getters.userInfo !== null ? (this.$store.getters.userInfo.pemilik ? this.$store.getters.userInfo.username : '') : ''
            },
        },
        created() {
            this.singlePassword.username = this.username;
        },
        mounted () { 
            var bodyTag = document.getElementsByTagName('body')[0]
            bodyTag.classList.add('administrator')
        }
    }
</script>