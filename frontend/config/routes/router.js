import { createRouter, createWebHistory , createWebHashHistory} from 'vue-router'
import groupRoute from './routes/group'
import quranRoute from './routes/quran'
import authRoute from './routes/auth'
import infaqRoute from './routes/infaq'
import defaultRoute from './routes/default'
import sholatRoute from './routes/sholat'
import persyarikatanRoute from './routes/persyarikatan'
import kajianRoute from './routes/kajian'
import dataRoute from './routes/data'
import facilityRoute from './routes/facility'
import psbRoute from './routes/psb'
// Vue router
const routes = new createRouter({
  history: createWebHistory(),
  routes: [
    ...groupRoute,
    ...defaultRoute,
    ...infaqRoute,
    ...authRoute,
    ...quranRoute,
    ...dataRoute,
    ...kajianRoute,
    ...persyarikatanRoute,
    ...psbRoute,
    ...facilityRoute,
    ...sholatRoute,
	],
  scrollBehavior: function(to, from, savedPosition) {
    // console.log(savedPosition)
    if (savedPosition) {
      return savedPosition
    } else {
      if (to.name == 'main')
        return
      return { top: 0 }
    }
  },
})

routes.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  try {
    await authStore.checkUser().then(() => {
      console.log('User check completed')
    }).catch((err) => {
      console.error('User check failed', err)
    })
    const loggedUser = authStore.loggedUser
    // var title = to.meta.pageTitle
    // if (title) {
    //   var pageTitle = title
    // } else {
    //   var pageTitle = '<b>Layanan Penjadwalan</b>'
    // }
    // var pageSubTitle = to.meta.pageSubTitle

    // store.dispatch('changePageTitle', pageTitle)
    // store.dispatch('changePageSubTitle', pageSubTitle)
    
    // If login, dont enter login and default=
    // console.log(to)
    if (loggedUser.role != '' && ['login','default'].includes(to.name)) {
      next({name:'dashboard'})
    } else if (to.matched.some(record => record.meta.requiresAuth)) {
      if (loggedUser.role == '') {
        // window.alert('Silahkan login terlebih dahulu')
        next({
          name: 'login',
          query: { nextUrl: to.fullPath }
        })
      } else { if (to.meta.allowedRoles) {
          // console.log(to.meta.allowedRoles, loggedUser.role, !to.meta.allowedRoles.includes(loggedUser.role));
          if (!to.meta.allowedRoles.includes(loggedUser.role)) {
            let name = 'unauthorized'
            // console.log(name)
            if (to.meta.redirect)
              name = to.meta.redirect
            // console.log(name)
            next({
              name: name,
            })
          } 
          else {
            next()
          }
        } 
        else {
          next()
        }
      }
      // next()
    } else if (to.matched.some(record => record.meta.guest)) {
      next()
    } else {
      next()
    }
    // next()
  } catch (err) {
    console.error('Navigation error:', err)
    return next({ name: 'login' }) // Fallback if checkUser fails
  }
})

export default routes