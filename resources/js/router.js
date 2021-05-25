import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store'

import Login from '@/views/Login/Main'

import Monit from '@/views/Monit/Main'
import MonitDashboard from '@/views/Monit/Dashboard/Main'

import Admin from '@/views/Admin/Frame'
import AdminDashboard from '@/views/Admin/Dashboard/Main'
import AdminHandyTalky from '@/views/Admin/HandyTalky/Main'
import AdminInformasi from '@/views/Admin/Informasi/Main'
import AdminKesatuan from '@/views/Admin/Kesatuan/Main'
import AdminGiat from '@/views/Admin/Giat/Main'
import AdminK2yd from '@/views/Admin/K2yd/Main'
import AdminSitkam from '@/views/Admin/Sitkam/Main'
import AdminKunjungan from '@/views/Admin/Kunjungan/Main'
import AdminCegahpermasalahan from '@/views/Admin/Cegahpermasalahan/Main'
import AdminSatgas from '@/views/Admin/Satgas/Main'
import AdminMasyarakat from '@/views/Admin/Masyarakat/Main'
import AdminPersonil from '@/views/Admin/Personil/Main'
import AdminPengaturan from '@/views/Admin/Pengaturan/Main'
import AdminAbsensi from '@/views/Admin/Absensi/Main'
import AdminDanaDesa from '@/views/Admin/DanaDesa/Main'
import AdminDanaSosial from '@/views/Admin/DanaSosial/Main'
import AdminEDisposisi from '@/views/Admin/Edisposisi/Main'

import AdminSambang from '@/views/Admin/LaporanBhabin/Sambang'
import AdminProblem from '@/views/Admin/LaporanBhabin/Problem'
import AdminPertolongan from '@/views/Admin/LaporanBhabin/Pertolongan'
import AdminPembinaan from '@/views/Admin/LaporanBhabin/Pembinaan'
import AdminSiskamling from '@/views/Admin/LaporanBhabin/Siskamling'
import AdminKegiatan from '@/views/Admin/LaporanBhabin/Kegiatan'
import AdminDeteksiDini from '@/views/Admin/LaporanBhabin/DeteksiDini'
import AdminPeneranganSatuan from '@/views/Admin/PeneranganSatuan/Main'

import DemoKegiatan from '@/views/Demo/Kegiatan'
import DemoQuickResponse from '@/views/Demo/QuickResponse'

Vue.use(Router);

const router = new Router({
  	mode: 'history',
  	routes: [
		{
	      	path: '/',
	      	meta: { requiresAuth: true },
	      	component: Monit,
	      	children: [
		        {
                    path: '',
                    name: 'Monit',
                    component: MonitDashboard
		        },
		    ]
		},
		{
	      	path: '/admin',
	      	meta: { requiresAuth: true },
	      	component: Admin,
	      	children: [
		        {
                    path: '/',
                    name: 'Dashboard | Administrator',
                    component: AdminDashboard
                }, {
                    path: "/informasi",
                    name: "Informasi | Administrator",
                    component: AdminInformasi
                }, {
                    path: "/edisposisi",
                    name: "e-Disposisi | Administrator",
                    component: AdminEDisposisi
                }, {
                    path: "/personil",
                    name: "Personil | Administrator",
                    component: AdminPersonil
                }, {
                    path: "/penerangan-satuan",
                    name: "PeneranganSatuan̦ | Administrator",
                    component: AdminPeneranganSatuan
                }, {
                    path: "/kesatuan",
                    name: "Kesatuan | Administrator",
                    component: AdminKesatuan
                }, {
                    path: "/masyarakat",
                    name: "Masyarakat | Administrator",
                    component: AdminMasyarakat
                }, {
                    path: "/handy-talky",
                    name: "HandyTalky | Administrator",
                    component: AdminHandyTalky
                }, {
                    path: "/giat-rutin",
                    name: "Giat | Administrator",
                    component: AdminGiat
                }, {
                    path: "/k2yd",
                    name: "K2yd | Administrator",
                    component: AdminK2yd
                }, {
                    path: "/kunjungan",
                    name: 'Kunjungan | Administrator',
                    component: AdminKunjungan
                }, {
                    path: "/cegah-permasalahan-desa",
                    name: "Cegah Permasalahan Desa | Administrator",
                    component: AdminCegahpermasalahan
                }, {
                    path: "/Sitkam",
                    name: "Sitkam | Administrator",
                    component: AdminSitkam
                }, {
                    path: "/Satgas",
                    name: "Satgas | Administrator",
                    component: AdminSatgas
                }, {
                    path: "/sambang",
                    name: "Sambang | Administrator",
                    component: AdminSambang
                }, {
                    path: "/problem-solving",
                    name: "Problem Solving | Administrator",
                    component: AdminProblem
                }, {
                    path: "/pertolongan-pelayanan",
                    name: "Pertolongan Pelayanan | Administrator",
                    component: AdminPertolongan
                }, {
                    path: "/pertolongan-pelayanan",
                    name: "Pertolongan & Pelayanan Kepolisian | Administrator",
                    component: AdminPertolongan
                }, {
                    path: "/pembinaan-kpm",
                    name: "Pembinaan KPM/Pokdarkamtibmas | Administrator",
                    component: AdminPembinaan
                }, {
                    path: "/kegiatan-desa",
                    name: "Kegiatan Desa | Administrator",
                    component: AdminKegiatan
                }, {
                    path: "/deteksi-dini-produk-li",
                    name: "Deteksi Dini Produk LI | Administrator",
                    component: AdminDeteksiDini
                }, {
                    path: "/pembinaan-siskamling",
                    name: "Pembinaan Siskamling | Administrator",
                    component: AdminSiskamling
                }, {
                    path: "/dana-desa",
                    name: "Dana Desa | Administrator",
                    component: AdminDanaDesa
                }, {
                    path: "/dana-sosial",
                    name: "Dana Sosial | Administrator",
                    component: AdminDanaSosial
                }, {
                    path: "/pengaturan",
                    name: "Pengaturan | Administrator",
                    component: AdminPengaturan
                }, {
                    path: "/absensi",
                    name: "Absensi Personil | Administrator",
                    component: AdminAbsensi
                }
		    ]
		},
		{
			path: '/login',
			name: 'Login',
			component: Login,
        },
        {
			path: '/demo-kegiatan',
			name: 'Demo',
			component: DemoKegiatan,
        },
        {
			path: '/demo-quick-response',
			name: 'Demo Quick Reponse',
			component: DemoQuickResponse,
		}
    ]
});

router.beforeEach((to, from, next) => {
    store.commit('setLoader', true)
    //Definisikan token ke common axios
    axios.defaults.headers.common['Authorization'] = "Bearer " + localStorage.getItem('token')
    //Dapatkan vuex user
    let clientHasUserInfo = (store.getters.userInfo !== null)
    //Ubah judul halaman
    document.title = to.name + ' | ' + appName
    //Jika token tidak kosong maka lanjut proses
    if (localStorage.getItem('token') !== null) {
        //Cek authentikasi user
        axios.get('user')
        .then(({ data: { data }}) => {
            //Definisikan parameter router
            let nextParam = {}
            //Definisikan custom role akses
            var pemilik = 'admin'
            //Jika user tidak ada di vuex maka tambahkan
            if(!clientHasUserInfo) {
                store.commit('setUser', data)
            }
            //Jika mengakses url yang tidak ada
            if (!to.matched.length) {
                nextParam = { path: '/not-found' }
            } 
            //Jika pengguna sudah login tapi mengakses halaman yang disediakan untuk tamu
            else if (to.matched.some(route => route.meta.guest) && localStorage.getItem('token') !== null) {
                if(from.name !== 'Monit') 
                {
                    nextParam = ''
                }
            }
            //Jika pengguna mengakses route khusus role tertentu
            else if (to.matched.some(route => route.meta.role)) {
                nextParam = (to.matched[0].meta.role !== pemilik) ? { path: '/not-found' } : nextParam = { }
            }
            else {
                nextParam = {}
            }
            next(nextParam)
        })
        .catch((error) => {
            console.log(error)
        })
    } else {
        var nextParam
        //Jika mengakses halaman yang membutuhkan login tapi token tidak didapatkan
        if (!to.matched.length) {
            nextParam = { path: '/not-found' }
        }
        else if (to.matched.some(route => route.meta.requiresAuth) && localStorage.getItem('token') == null) {
            nextParam = { path: '/login' }
        } else {
            nextParam = {}
        } 
        next(nextParam)
	}
})

router.afterEach( () => {
    store.commit('removeLoader')
})

export default router
