<template>
	<div>
		<b-row>
	    	<b-col cols="2" md="2">
				<b-row>
	    			<b-col cols="8"><h4>Pengaturan</h4></b-col>
	    			<b-col cols="4">
	    				<!-- <b-button variant="primary" size="sm">
							<icon name="plus"/> Tambah
						</b-button> -->
	    			</b-col>
	    		</b-row>
			</b-col>
	    	<b-col cols="6" md="6">
		        <!-- <b-pagination
		          align="right"
		          :total-rows="totalRows"
		          v-model="currentPage"
		          :per-page="perPage" /> -->
		    </b-col>
		    <b-col cols="4" md="4">
		    	<!-- <b-form-input
		        	align="right"
		          	v-model="filterDebounced"
		          	placeholder="Cari Nama Pengaturan..."/> -->
		    </b-col>
		</b-row>

		<div class="position-relative">
			<b-form-group
				:label-cols="3"
				breakpoint="md"
				description="Default password personil dan bhabin"
				label="Default password">
				<b-input-group>
					<b-form-input v-model="default_password" placeholder="password"></b-form-input>
					<b-btn slot="append" @click="ubahDefaultPassword" variant="info">Simpan</b-btn>
				</b-input-group>
			</b-form-group>
			<b-form-group
				:label-cols="3"
				breakpoint="md"
				description="Default banner/header foto grid"
				label="Default banner/header foto grid dengan rasio 5:1">
				<b-form-file @change="bannerChange" placeholder="Banner" accept="image/*"></b-form-file>
				<div class="src-banner">
					<img :src="srcBanner" v-if="srcBanner != null" />
					<b-spinner variant="primary" v-if="spinnerBanner != null"></b-spinner>
				</div>
			</b-form-group>
			<b-form-group
				:label-cols="3"
				breakpoint="md"
				description="Default Pdf Sispammako"
				label="Default pdf sispammako">
				
				<b-form-file @change="pdfSispamChange" placeholder="Sispammako" accept="application/pdf"></b-form-file>
				<b-spinner variant="primary" v-if="spinnerPdfSispam != null"></b-spinner>
				<iframe :src="srcPdfSispammako" v-if="srcPdfSispammako != null" />
			</b-form-group>

			<b-form-group
					:label-cols="3"
					breakpoint="md"
                    v-if="visiMisi == '1'"
					description="Visi Misi"
					label="Pdf Visi & Misi">

				<b-form-file @change="pdfVisiMisiChange" placeholder="Visi Misi" accept="application/pdf"></b-form-file>
				<b-spinner variant="primary" v-if="spinnerPdfVisiMisi != null"></b-spinner>
				<iframe :src="srcPdfVisiMisi" v-if="srcPdfVisiMisi != null" />
			</b-form-group>

            <b-form-group
                    :label-cols="3"
                    breakpoint="md"
                    v-if="visiMisi == '1'"
                    description="Kebijakan Kapolres"
                    label="Pdf Kebijakan Kapolres">

                <b-form-file @change="pdfKebijakanKapolresChange" placeholder="Kebijakan Kapolres" accept="application/pdf"></b-form-file>
				<b-spinner variant="primary" v-if="spinnerPdfKebijakanKapolres != null" ></b-spinner>
                <iframe :src="srcPdfKebijakanKapolres" v-if="srcPdfKebijakanKapolres != null" />
            </b-form-group>

            <b-form-group
                    :label-cols="3"
                    breakpoint="md"
                    v-if="visiMisi == '1'"
                    description="Program Kapolres"
                    label="Pdf Program Kapolres">

                <b-form-file @change="pdfProgramKapolresChange" placeholder="Program Kapolres" accept="application/pdf"></b-form-file>
				<b-spinner variant="primary" v-if="spinnerPdfProgramKapolres != null" ></b-spinner>
                <iframe :src="srcPdfProgramKapolres" v-if="srcPdfProgramKapolres != null" />
            </b-form-group>
			
			<!-- <b-table responsive
	               ref="tabelPengaturan"
	               :busy.sync="isBusy"
	               :fields="tableColumns"
	               :current-page="currentPage"
	               :per-page="perPage"
	               :filter="filter"
	               :sort-by.sync="sortBy"
	               :sort-desc.sync="sortDesc">

		        <template slot="index" slot-scope="data">
		          	{{ ((currentPage - 1) * 10) + data.index + 1 }}
		        </template>
			    <template slot="aksi" slot-scope="row">
			    	
			    </template>
		    </b-table>

		    <div class="loading" v-show="isBusy">
		        <spinner :speed=".4" :size="30" />
		    </div> -->
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
			    visiMisi: visiMisi,
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
