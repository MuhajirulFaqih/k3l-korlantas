<template>
	<div>
		<b-row>
	    	<b-col cols="2" md="2">
				<b-row>
	    			<b-col cols="8"><h4>Pengaturan</h4></b-col>
	    		</b-row>
			</b-col>
		</b-row>

		<div class="position-relative">
			<b-row>
				<b-col cols="6">
					<b-form-group
						:label-cols="3"
						breakpoint="md"
						description="Default password user"
						label="Default password">
						<b-input-group>
							<b-form-input v-model="default_password" placeholder="password"></b-form-input>
							<b-btn slot="append" @click="ubahDefaultPassword" variant="primary">Simpan</b-btn>
						</b-input-group>
					</b-form-group>
				</b-col>
			</b-row>
	  	</div>
	</div>
</template>
<script>
	import format from 'date-fns/format'
	import { debounce } from 'lodash'

	const dateFnsBahasa = {
	    locale: require('date-fns/locale/id')
	}

	export default {
		name: 'pengaturan',
		data () {
			return {
			    visiMisi: null,
				srcBanner: null,
				srcPdfSispammako: null,
				spinnerPdfSispam: null,
				spinnerBanner: null,
				default_password: null,

                spinnerPdfVisiMisi: null,
                srcPdfVisiMisi: null,

                spinnerPdfKebijakanKapolres: null,
                srcPdfKebijakanKapolres: null,


                spinnerPdfProgramKapolres: null,
                srcPdfProgramKapolres: null
			}
		},
		methods: {
			bannerChange (e){
				var file = e.target.files[0]
				this.srcBanner = URL.createObjectURL(file)
				var formData = new FormData()
				formData.append("file", file, file.name) 
				var promise = axios.post("pengaturan/banner_grid", formData, {
					headers: {
						'Content-Type': 'multipart/form-data',
						'Accept': 'application/json'
					},
					onUploadProgress: function (progressEvent) {
						this.spinnerBanner = "Mengunggah "+ (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
					}.bind(this)
				})
				.then((data) => {
					this.$toast.success("Banner berhasil diubah")
					this.spinnerBanner = null
				})
				.catch((response) => {
					this.$toast.error("Banner gagal diubah")
					this.spinnerBanner = null
				})
			},

            pdfProgramKapolresChange (e){
			    var file = e.target.files[0]
                this.srcPdfProgramKapolres = URL.createObjectURL(file)
                var formData = new FormData()
                formData.append("file", file, file.name)

                var promise = axios.post("pengaturan/pdf_program_kapolres", formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        },
                        onUploadProgress: function (progressEvent) {
                            this.spinnerPdfProgramKapolres = "Mengunggah "+ (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
                        }.bind(this)
                    })
                    .then((data) => {
                        this.$toast.success("Program kapolres berhasil diubah")
                        this.spinnerPdfProgramKapolres = null
                    })
                    .catch((err) => {
                        this.$toast.error("Program kapolres gagal diubah")
                        this.spinnerPdfProgramKapolres = null
                    })
            },

            pdfKebijakanKapolresChange (e){
                var file = e.target.files[0]
                this.srcPdfKebijakanKapolres = URL.createObjectURL(file)
                var formData = new FormData()
                formData.append("file", file, file.name)

                var promise = axios.post("pengaturan/pdf_kebijakan_kapolres", formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        },
                        onUploadProgress: function (progressEvent) {
                            this.spinnerPdfKebijakanKapolres = "Mengunggah "+ (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
                        }.bind(this)
                    })
                    .then((data) => {
                        this.$toast.success("Kebijakan kapolres berhasil diubah")
                        this.spinnerPdfKebijakanKapolres = null
                    })
                    .catch((err) => {
                        this.$toast.error("Program kapolres gagal diubah")
                        this.spinnerPdfKebijakanKapolres = null
                    })
            },

            pdfVisiMisiChange (e){
                var file = e.target.files[0]
                this.srcPdfVisiMisi = URL.createObjectURL(file)
                var formData = new FormData()
                formData.append("file", file, file.name)

                var promise = axios.post("pengaturan/pdf_visi_misi", formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        },
                        onUploadProgress: function (progressEvent) {
                            this.spinnerPdfVisiMisi = "Mengunggah "+ (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
                        }.bind(this)
                    })
                    .then((data) => {
                        this.$toast.success("Kebijakan kapolres berhasil diubah")
                        this.spinnerPdfVisiMisi = null
                    })
                    .catch((err) => {
                        this.$toast.error("Program kapolres gagal diubah")
                        this.spinnerPdfVisiMisi = null
                    })
            },

			pdfSispamChange (e){
				var file = e.target.files[0]
				this.srcPdfSispammako = URL.createObjectURL(file)
				var formData = new FormData()
				formData.append("file", file, file.name) 
				var promise = axios.post("pengaturan/pdf_sispammako", formData, {
					headers: {
						'Content-Type': 'multipart/form-data',
						'Authorization': sessionStorage.getItem('token'),
						'Accept': 'application/json'
					},
					onUploadProgress: function (progressEvent) {
						this.spinnerPdfSispam = "Mengunggah "+ (parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total)))
					}.bind(this)
				})
				.then((data) => {
					this.$toast.success("Banner berhasil diubah")
					this.spinnerPdfSispam = null
				})
				.catch((response) => {
					this.$toast.error("Banner gagal diubah")
					this.spinnerPdfSispam = null
				})
			},
			ubahDefaultPassword(){
				axios.post('pengaturan/default_password', {
					password: this.default_password
				})
				.then(({data}) => {
					if ('success' in data)
						this.$toast.success('Password berhasil di ubah')
					else
						this.$toast.error('Password gagal di ubah')
				})
				.catch((err) => {
					this.$toast.error('Password gagal di ubah')
				})
			},
			progressBannerChange (progressEvent){
				console.log(progressEvent)
			},
			progressPdfSispamChange (progressEvent){

			},
			loadPengaturan () {
				axios.get("pengaturan")
				.then(({data}) => {
					data.forEach((o) => {
						switch(o.nama){
							case 'default_password':
								this.default_password = o.nilai
								break
							case 'default_banner_grid':
								this.srcBanner = baseUrl+'/api/upload/'+o.nilai
								break
							case 'default_pdf_sispammako':
								this.srcPdfSispammako = baseUrl+'/api/upload/'+o.nilai
								break
                            case 'pdf_visi_misi':
                                this.srcPdfVisiMisi = o.nilai === '' ? null : baseUrl+'/api/upload/'+o.nilai
                                break
                            case 'pdf_kebijakan_kapolres':
                                this.srcPdfKebijakanKapolres = o.nilai === '' ? null :  baseUrl+'/api/upload/'+o.nilai
                                break;
                            case 'pdf_program_kapolres':
                                this.srcPdfProgramKapolres = o.nilai === '' ? null :  baseUrl+'/api/upload/'+o.nilai
						}
					})
				})
		    }    
		},
		watch: {
			filterDebounced (newFilter) {
		        this.debounceFilter()
		    },
		},
		mounted() {
			this.loadPengaturan()
		},
	}
</script>

<style scoped>
img {
	width: 100%;
	margin-top: 10px;
}

iframe {
	width: 100%;
	height: 500px;
    margin-top: 15px;
}

.src-banner {
	width: 100%;
}

.spinner {
	margin: auto;
}
</style>
