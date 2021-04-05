import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store'

import Login from '@/views/Login/Main'
import Monit from '@/views/Monit/Main'
import MonitDashboard from '@/views/Monit/Dashboard/Main'
import Executive from '@/views/Executive/Main'
import ExecutiveDashboard from '@/views/Executive/Dashboard/Main'

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
	      	path: '/executive',
	      	meta: { requiresAuth: true },
	      	component: Executive,
	      	children: [
		        {
		          path: '',
		          name: 'Executive',
		          component: ExecutiveDashboard
		        },
		    ]
		},
		{
			path: '/login',
			name: 'Login',
			component: Login,
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
