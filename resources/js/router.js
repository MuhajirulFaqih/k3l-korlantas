import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store'

import Login from '@/views/Login/Main'

import Monit from '@/views/Monit/Main'
import MonitDashboard from '@/views/Monit/Dashboard/Main'

import Admin from '@/views/Admin/Frame'
import AdminDashboard from '@/views/Admin/Dashboard/Main'
import AdminHandyTalky from '@/views/Admin/HandyTalky/Main'
import AdminSlider from '@/views/Admin/Slider/Main'
import AdminKesatuan from '@/views/Admin/Kesatuan/Main'
import AdminMasyarakat from '@/views/Admin/Masyarakat/Main'
import AdminPersonil from '@/views/Admin/Personil/Main'
import AdminPengaturan from '@/views/Admin/Pengaturan/Main'
import AdminLaporanAbsensi from '@/views/Admin/Absensi/Main'

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
                    path: "/personil",
                    name: "Personil | Administrator",
                    component: AdminPersonil
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
                    path: "/laporan-kegiatan",
                    name: "Laporan Kegiatan | Administrator",
                    component: null
                }, {
                    path: "/laporan-kejadian",
                    name: "Laporan Kejadian | Administrator",
                    component: null
                }, {
                    path: "/laporan-absensi",
                    name: "Laporan Absensi | Administrator",
                    component: AdminLaporanAbsensi
                }, {
                    path: "/Slider",
                    name: "Slider | Administrator",
                    component: AdminSlider
                }, {
                    path: "/pengaturan",
                    name: "Pengaturan | Administrator",
                    component: AdminPengaturan
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
