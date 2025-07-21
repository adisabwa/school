import { createRouter, createWebHistory , createWebHashHistory} from 'vue-router'
import defaultRoute from './routes/default'
import authRoute from './routes/auth'
import dataRoute from './routes/data'
import psbRoute from './routes/psb'
import savingRoute from './routes/saving'
// Vue router
const routes = new createRouter({
  history: createWebHistory(),
  routes: [
    ...savingRoute,
    ...psbRoute,
    ...dataRoute,
    ...authRoute,
    ...defaultRoute,
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
    if (loggedUser.role != '' && ['login'].includes(to.name)) {
      next({name:'default'})
    } else if (to.matched.some(record => record.meta.requiresAuth)) {
      if (loggedUser.role == '') {
        // window.alert('Silahkan login terlebih dahulu')
        next({
          name: 'login',
          query: { nextUrl: to.fullPath }
        })
      } else { if (to.meta.allowedRoles) {
          console.log(to.meta.allowedRoles, loggedUser.role, !to.meta.allowedRoles.includes(loggedUser.role));
          if (loggedUser.role == 'super-admin') {
            next()
          }
          else if (!to.meta.allowedRoles?.includes(loggedUser.role)) {
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