<template>
	<div>
		<audio :src="base + '/audio/begal.wav'" ref="begal" loop></audio>
		<audio :src="base + '/audio/kebakaran.mp3'" ref="kebakaran" loop></audio>
		<audio :src="base + '/audio/lakalantas.wav'" ref="lakalantas" loop></audio>
		<audio :src="base + '/audio/pembunuhan.wav'" ref="pembunuhan" loop></audio>
		<audio :src="base + '/audio/pemerasan.wav'" ref="pemerasan" loop></audio>
		<audio :src="base + '/audio/pemerkosaan.wav'" ref="pemerkosaan" loop></audio>
		<audio :src="base + '/audio/penculikan.wav'" ref="penculikan" loop></audio>
		<audio :src="base + '/audio/pencurian-motor.mp3'" ref="curanmor" loop></audio>
		<audio :src="base + '/audio/penganiayaan.mp3'" ref="penganiayaan" loop></audio>
		<audio :src="base + '/audio/pengerusakan.mp3'" ref="pengerusakan" loop></audio>
		<audio :src="base + '/audio/perampasan.mp3'" ref="perampasan" loop></audio>
		<audio :src="base + '/audio/perampokan.mp3'" ref="perampokan" loop></audio>
		<audio :src="base + '/audio/perjudian.mp3'" ref="perjudian" loop></audio>
		<audio :src="base + '/audio/senpi.mp3'" ref="senpi" loop></audio>
	</div>
</template>
<script>
	export default {
		name: 'audio-kejadian',
		data () {
			return {
				base: baseUrl,
			}
		},
		methods: {
			playAudio (jenis) {
				this.$refs[jenis].play()
			},
			stopAudio () {
				for (var ref in this.$refs) {
					this.$refs[ref].pause()
					this.$refs[ref].currentTime = 0
				}
			}
		}
	}
</script>