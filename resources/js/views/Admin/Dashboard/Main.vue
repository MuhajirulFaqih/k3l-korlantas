<template>
	<div>
		<b-row>
			<b-col cols="4">
				<b-col cols="12 mb-4" @click="$router.push({ name: 'Personil' })">
					<b-card no-body header-class="bg-primary pt-5 pb-4" bg-variant="primary">
						<div slot="header">
							<b-row>		
								<b-col cols="8">
									<h1>{{ personil }}</h1>
									<span>Personil</span>
								</b-col>
								<b-col cols="4 text-center">
									<ph-user-circle class="phospor" size="70"/>
								</b-col>
							</b-row>
						</div>
					</b-card>
				</b-col>
				<b-col cols="12 mb-4" @click="$router.push({ name: 'Masyarakat' })">
					<b-card no-body header-class="bg-success pt-5 pb-4" bg-variant="success">
						<div slot="header">
							<b-row>		
								<b-col cols="8">
									<h1>{{ masyarakat }}</h1>
									<span>Masyarakat</span>
								</b-col>
								<b-col cols="4 text-center">
									<ph-users class="phospor" size="70"/>
								</b-col>
							</b-row>
						</div>
					</b-card>
				</b-col>
				<b-col cols="12 mb-4" @click="$router.push({ name: 'Kesatuan' })">
					<b-card no-body header-class="bg-danger pt-5 pb-4" bg-variant="danger">
						<div slot="header">
							<b-row>		
								<b-col cols="8">
									<h1>{{ kesatuan }}</h1>
									<span>Kesatuan</span>
								</b-col>
								<b-col cols="4 text-center">
									<ph-circles-three class="phospor" size="70"/>
								</b-col>
							</b-row>
						</div>
					</b-card>
				</b-col>
			</b-col>
			<b-col cols="8">
				
			</b-col>
		</b-row>
		<br/>
		<b-row>
			<b-col cols="12">
				<!-- <b-card header="Informasi Terbaru"
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
	        	</b-card> -->
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
			masyarakat: '',
			kesatuan: '',
		}
	},
	methods: {
		fetchData () {
			axios.get('dashboard')
			.then(({ data : { data } }) => {
				this.personil = data.personil	
				this.masyarakat = data.masyarakat
				this.kesatuan = data.kesatuan
			})
		},
	},
	mounted () {
		this.fetchData()
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