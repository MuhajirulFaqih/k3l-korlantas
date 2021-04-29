<template>
  	<div>
    	<b-row>
      		<b-col cols="3" md="3">
        		<b-row>
		          	<b-col cols="auto">
		            	<h4>Dana Desa</h4>
		          	</b-col>
        		</b-row>
      		</b-col>
  		</b-row>
  		<b-row>
  			<b-col cols="2" md="2">
  				<model-select
  					@input="setKabupaten"
	              	v-model="kabupaten"
	              	:options="kabupatenOptions"
	              	placeholder="Pilih kabupaten"
	            />
  			</b-col>
  			<b-col cols="2" md="2">
  				<model-select
  					@input="setKecamatan"
	              	v-model="kecamatan"
	              	:options="kecamatanOptions"
	              	placeholder="Pilih kecamatan"
	            />
  			</b-col>
  			<b-col cols="2" md="2">
  				<model-select
	              	@input="setDesa"
	              	v-model="desa"
	              	:options="desaOptions"
	              	placeholder="Pilih desa"
	            />
  			</b-col>
  			<b-col cols="2" md="2">
  				<model-select
  					@input="setTipeDanaDesa"
	              	v-model="tipeDanaDesa"
	              	:options="tipeDanaDesaOptions"
	              	placeholder="Pilih tahun"
	            />
  			</b-col>
  			<b-col cols="3" md="3">
  				<model-select
  					v-if="tipeDanaDesa == '2'"
	              	@input="setTahunAnggaran"
	              	v-model="tahunAnggaran"
	              	:options="tahunAnggaranOptions"
	              	placeholder="Pilih tahun anggaran"
	            />
  			</b-col>
  			<b-col cols="1" md="1">
            	<button class="btn btn-primary float-right" @click="printData" :disabled="!formReady">
              		<span v-if="processPrint">Proses...</span>
                    <span v-else>Cetak</span>
            	</button>
		    </b-col>
  		</b-row>
  		<br/>
  		<b-row>
  			<b-col cols="12">
	  			<b-tabs pills>
			    	<b-tab title="PENDAPATAN" active>
			    		<Pendapatan ref="pendapatan" />
			    	</b-tab>
			    	<b-tab title="BELANJA BIDANG">
			    		<BelanjaBidang ref="belanja" />
			    	</b-tab>
			    	<b-tab title="RINCIAN BELANJA">
			    		<RincianBelanja ref="rincian" />
			    	</b-tab>
			  	</b-tabs>
  			</b-col>
	  	</b-row>
 	</div>
</template>
<script>
	import DatePicker from "vue2-datepicker";
	import { debounce, flattenDeep, values } from "lodash";
	import { format, formatISO, parseISO } from 'date-fns'
	import { ModelSelect, MultiSelect } from "vue-search-select";
	import Pendapatan from './Dana/Pendapatan'
	import BelanjaBidang from './Dana/BelanjaBidang'
	import RincianBelanja from './Dana/RincianBelanja'
	import id from 'date-fns/locale/id'

	export default {
	  	name: "dana-desa",
	  	components: {
		    ModelSelect, MultiSelect, DatePicker, 
		    Pendapatan, BelanjaBidang, RincianBelanja,
		},
		data() {
			return {
				tipeDanaDesa: "",
				tipeDanaDesaOptions: [
		        	{ text: "Semua Tahun", value: 1 },
		        	{ text: "Tahun Tertentu", value: 2 },
		      	],
		      	kabupaten: null,
		      	kabupatenOptions: [],
		      	kecamatan: null,
		      	kecamatanOptions: [],
		      	desa: null,
		      	desaOptions: [],
		      	tahunAnggaran: null,
		      	tahunAnggaranOptions: [],
				processPrint: false,
			}
		},
		computed: {
			formReady () {
				return this.kabupaten != '' && this.kecamatan != '' && this.desa != '' && this.processPrint == false && (this.tipeDanaDesa == '1' || (this.tipeDanaDesa == '2' && this.tahunAnggaran != ''))
			}
		},
		methods: {
			printData () {
				var data = {
					desa: this.desa,
					tipe: this.tipeDanaDesa,
					tahun: this.tahunAnggaran,
				}
				this.processPrint = true
				axios({
		        	method: "POST",
		          	url: "report/export-dana-desa",
		          	data: data,
		          	responseType: "blob"
		        })
		        .then(response => {
			        let url = window.URL.createObjectURL(new Blob([response.data], { type: 'data:application/vnd.ms-excel' }));
					let filename = response.headers['content-disposition'].split('=')[1].replace(/^\"+|\"+$/g, '')
					let link = document.createElement('a');
					link.href = url;
					link.setAttribute('download', filename);
					document.body.appendChild(link);
					link.click();
			        this.processPrint = false
		        })
		        .catch(({ response: { status, data: { errors } } }) => {
		          	this.processPrint = false
		        });
			},
			fetchData () {
				var d = new Date()
  				var y = d.getFullYear()
  				for (var i = 2019; i <= y; i++) {
  					this.tahunAnggaranOptions.push({ value: i, text: i })
  				}
			},
			fetchKabupaten () {
				axios.get('kabupaten-provinsi').then(({data : {kabupaten} }) => {
					this.kabupatenOptions = kabupaten.map((val) => {
						return {value: val.id_kab, text: val.nama}
					})
				})
			},
			setKabupaten (kabupaten) {
				this.desa = null
				this.desaOptions = []
				this.tipeDanaDesa = null
				this.tahunAnggaran = null
				axios.get('kecamatan/' + kabupaten)
				.then(({data : {kecamatan} }) => {
					this.kecamatan = null
					this.kecamatanOptions = kecamatan.map((val) => {
						return {value: val.id_kec, text: val.nama}
					})
					this.refreshTable()
				})
			},
			setKecamatan (kecamatan) {
				this.desa = null
				this.desaOptions = []
				axios.get('kelurahan/' + kecamatan)
				.then(({data : {kelurahan} }) => {
					this.tipeDanaDesa = null
					this.tahunAnggaran = null
					this.desaOptions = kelurahan.map((val) => {
						return {value: val.id_kel, text: val.nama}
					})
					this.refreshTable()
				})
			},
			setDesa (desa) {
				this.refreshTable()
			},
			setTipeDanaDesa (tipe) {
				this.tahunAnggaran = null
				this.refreshTable()
			},
			setTahunAnggaran () {
				this.refreshTable()
			},
			refreshTable () {
				var params = {
					desa: this.desa,
					tipe: this.tipeDanaDesa,
					tahun: this.tahunAnggaran,
				}
				this.$refs.pendapatan.fetchData(params)
				this.$refs.belanja.fetchData(params)
				this.$refs.rincian.fetchData(params)
			}
		},
		mounted() {
		    this.fetchKabupaten()
		    this.fetchData()
		},
	}
</script>
<style scoped>
.ui.selection.dropdown {
  background: #868e96;
  color: #fff;
}
.default {
  color: #fff;
}
</style>