<template>
    <div>
        <b-container class="front">
                <div class="logo-login">
                    <hr class="blue-line-small"/>
                    <hr class="blue-line-small bottom"/>
                    <div class="logo-box">
                        <div class="logo-image">
                            <b-img :src="`${baseUrl}/assets/logo.png`" class="w-100"/>
                        </div>
                    </div>
                </div>
                <b-row class="bg-login">
                    <b-col cols="12" lg="4" offset-lg="1" class="p-0 col-image">
                        <div class="overlay"></div>
                    </b-col>
                    <b-col cols="12" lg="6" class="col-content">
                        <b-row>
                            <b-col cols="12" lg="8" offset-lg="2">
                                <h2>Login</h2>
                                <h6>Silahkan login dengan akun anda</h6>
                                <b-form @submit.prevent="login">
                                    <b-row>
                                        <b-col cols="12">
                                            <b-form-group>
                                                <b-form-input v-model="username" placeholder="Username" class="e-form" autocomplete="off"/>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col cols="12">
                                            <b-form-group class="e-password">
                                                <div class="position-relative">
                                                    <b-form-input :type="typePassword ? 'password' : 'text'" v-model.trim="password" placeholder="Password" class="e-form"/>
                                                    <ph-eye :class="`phospor ${typePassword ? 'active' : ''}`" @click="typePassword = !typePassword" />
                                                    <ph-eye-closed :class="`phospor ${!typePassword ? 'active' : ''}`" @click="typePassword = !typePassword" />
                                                </div>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col cols="12">
                                            <button class="e-btn e-btn-primary btn btn-block" :disabled="isBusy" type="submit">Login</button>
                                        </b-col>
                                    </b-row>
                                </b-form>
                                <center class="copy">{{ instansi }} | {{ getYear() }}</center>
                            </b-col>
                        </b-row>
                    </b-col>
                    <hr class="blue-line"/>
                    <hr class="blue-line bottom"/>
                </b-row>
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'login',
        data() {
            return {
                isBusy: false,
                instansi: instansi,
                baseUrl: baseUrl,
                username: null,
                password: null,
                typePassword: true,
            }
        },
        methods: {
            login () {
                this.isBusy = true
                axios.post('user/auth-admin', {
					username: this.username,
					password: this.password,
					client_id: clientId,
					client_secret: clientSecret,
					grant_type: 'password'
				})
				.then(({data}) => {
                    this.isBusy = false
                    localStorage.setItem('token', data.access_token)
                    this.$router.push({ name: 'Monit' })
                    this.$toast.success('Anda berhasil login')
				})
				.catch(error => {
                    this.isBusy = false
                	this.$toast.error('Username dan password anda ditolak')
                    this.loginError = true
                });
            },
            getYear() {
                return new Date().getFullYear()
            }
        }
    }
</script>