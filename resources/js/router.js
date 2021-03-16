import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store'

import Login from '@/views/Login/Main'
import Monit from '@/views/Monit/Main'
import Dashboard from '@/views/Monit/Dashboard/Main'

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
		          name: 'Dashboard',
		          component: Dashboard
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

	// axios.get(baseUrl + '/auth/gatekeeper')
    // .then(({ data }) => {

	//     if (to.matched.some(route => route.meta.requiresAuth && data.status == 'logout')) {
	//         next({ name: 'Login' })
	//     } 
	//     else if (to.path === '/login' && data.status == 'login') {
	//     	// redirect ke dashboard jika sudah login
	//         next({ name: 'Dashboard' })
	//     }
	//     else {
	// 		next()
	//     }
    // })
    next();
	
})

export default router