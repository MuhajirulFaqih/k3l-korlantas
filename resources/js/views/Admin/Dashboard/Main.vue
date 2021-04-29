<template>
	<div>
		<b-row>
			<b-col cols="3" @click="$router.push({ name: 'Personil' })">
				<b-card no-body header-class="bg-primary" bg-variant="primary">
					<div slot="header">
						<b-row>		
							<b-col cols="8">
								<h2>{{ personil }}</h2>
								<span>Personil</span>
							</b-col>
							<b-col cols="4">
								<ph-user-circle class="phospor" size="60"/>
							</b-col>
						</b-row>
					</div>
		        </b-card>
			</b-col>
			<b-col cols="3" @click="$router.push({ name: 'Personil' })">
				<b-card no-body header-class="bg-primary" bg-variant="primary">
					<div slot="header">
						<b-row>
							<b-col cols="8">
								<h2>{{ personil_login }}</h2>
								<span>Personil Login</span>
							</b-col>
							<b-col cols="4">
								<ph-user-square class="phospor" size="60"/>
							</b-col>
						</b-row>
					</div>
				</b-card>
			</b-col>
			<b-col cols="3" @click="$router.push({ name: 'Masyarakat' })">
				<b-card no-body header-class="bg-success" bg-variant="success">
					<div slot="header">
						<b-row>		
							<b-col cols="8">
								<h2>{{ masyarakat }}</h2>
								<span>Masyarakat</span>
							</b-col>
							<b-col cols="4">
								<ph-users class="phospor" size="60"/>
							</b-col>
						</b-row>
					</div>
		        </b-card>
			</b-col>
			<b-col cols="3" @click="$router.push({ name: 'Kesatuan' })">
				<b-card no-body header-class="bg-danger" bg-variant="danger">
					<div slot="header">
						<b-row>		
							<b-col cols="8">
								<h2>{{ kesatuan }}</h2>
								<span>Kesatuan</span>
							</b-col>
							<b-col cols="4">
								<ph-circles-three class="phospor" size="60"/>
							</b-col>
						</b-row>
					</div>
		        </b-card>
			</b-col>
		</b-row>
		<br/>
		<b-row>
			<b-col cols="8">
				<div v-if="personilKegiatan">
					<b-card header="Personil Pengirim Kegiatan Terbanyak"
						header-tag="h4"
						header-class="bg-primary">
						<div>
							<center>
								<date-picker
									v-model="rentangTanggal"
									@input="onInputRentangTgl"
									range
									:shortcuts="shortcuts"
									lang="id"
									placeholder="Pilih rentang tanggal"
									format="DD-MM-YYYY"
								></date-picker>
							</center>
							<hr/>
						</div>
						<div class="beauty-scroll h-info2">
							<b-list-group>
								<b-list-group-item 
										:key="index"
										v-for="(i, index) in kegiatanPersonil">
											<span class="float-right">{{ i.jumlah }} kegiatan</span>
											<p justify class="text-white">
												{{ i.nrp }} - {{ i.pangkat }} {{ i.nama }}
											</p>
											{{ i.jabatan }}
								</b-list-group-item>
							</b-list-group>
						</div>
					</b-card>
				</div>
				<video v-else loop autoplay playsinline class="e-bg-main e-shadow-main w-100" ref="video" :src="video" type="video/mp4" />
			</b-col>
			<b-col cols="4">
				<b-card header="Informasi Terbaru"
	                header-tag="h4"
	                header-class="bg-primary"
					class="e-bg-main e-shadow-main"
	                body-class="h-info beauty-scroll">
		            <div>
		            	<b-list-group>
							<b-list-group-item :key="index" v-for="(i, index) in informasi" class="e-bg-main">
										<small><b class="text-white">{{ toDateString(i.created_at) }}</b></small>
										<br/>
										<p justify class="text-white">{{ i.informasi }}</p>
							</b-list-group-item>
						</b-list-group>
		            </div>
	        	</b-card>
			</b-col>
		</b-row>
	</div>
</template>
<script type="text/javascript">
import { format, formatISO, parseISO } from 'date-fns'
import id from 'date-fns/locale/id'
import DatePicker from "vue2-datepicker"
export default {
	name: 'Dashboard',
	components: { DatePicker },
	data () {
		return {
			personil: '',
			personil_login: '',
			masyarakat: '',
			kesatuan: '',
			informasi: [],
			kegiatanPersonil: [],
			video: null,
			rentangTanggal: null,
			shortcuts: [{
				test: "Today",
				onclick: () => {
					this.time3 = [new Date(), new Date()];
				}
			}],
			timePickerOptions: {
				start: "00:00",
				step: "00:30",
				end: "23:30"
			},
			personilKegiatan: personilKegiatan,
		}
	},
	methods: {
		fetchData () {
			axios.get('dashboard')
			.then(({ data : { data } }) => {
				this.personil = data.personil	
				this.masyarakat = data.masyarakat
				this.kesatuan = data.kesatuan
				this.personil_login = data.personil_login
				this.informasi = data.informasi.data
			})
		},
		toDateString (tanggal) {
			return format(parseISO(tanggal), 'd MMMM yyyy HH:mm:ss', {locale: id})
		},
		onInputRentangTgl(val) {
			var payload = {
				rentang: this.rentangTanggal,
			}
			axios.get("dashboard/personil-kegiatan", { params: payload })
			.then((data) => {
				this.kegiatanPersonil = data.data
			})
			.catch(({ response }) => {
				console.log('error')
			});
		},
	},
	mounted () {
		this.fetchData()
		if(personilKegiatan) { }
		else {
			window.addEventListener('load', async () => {
			let video = document.querySelector('video[autoplay]');
				try {
					await video.play();
				} catch (err) {
					video.controls = true;
				}
			});
		}
	}
}
</script>
<style scoped="">
	.h-info {
		height: 380px;
	}
	.h-info2 {
		height: 275px;
	}
	.card {
		cursor: pointer;
	}
</style>