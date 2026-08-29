// vue-apps/shared/router/guards.js
import { useAuthStore } from '@2/shared/config/stores/authStore'
import { useDataStore } from '@2/shared/config/stores/dataStore'

export function setupGuards(router) {
  
  router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()
    authStore.setRoute(to)
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
      // console.log(to, authStore.role)
      if (authStore.role != '' && ['default'].includes(to.name)) {
        next({name:'dashboard'})
      } else if (to.matched.some(record => record.meta.requiresAuth)) {
        if (authStore.role == '') {
          // window.alert('Silahkan login terlebih dahulu')
          next({
            name: 'default',
            query: { nextUrl: to.fullPath }
          })
        } else { 
          let app = to.meta.app
          if (app) {
            if (to.meta.allowedRoles) {
              let role = loggedUser.app_roles[app] ?? loggedUser.app_roles.all
              // console.log(app, role)
              if (to.meta.allowedRoles?.includes(role)) {
                next()
              } else {
                let name = 'unauthorized'
                if (to.meta.redirect)
                  name = to.meta.redirect
                next({
                  name:name,
                })
              }
            } else {
              next()
            }
          } else {
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
      return next({ name: 'default' }) // Fallback if checkUser fails
    }
  })

    // Di main.js aplikasi (Admin/Guru)
  router.afterEach((to) => {
    const authStore = useAuthStore()
    authStore.setRoute(to)
    const dataStore = useDataStore()
    if (to.meta.template) {
      dataStore.setTemplate(to.meta.template);// Kirim sinyal ke Navbar Instance
      // console.log('update layout')
      // window.dispatchEvent(new CustomEvent('layout-changed', { 
      //   detail: {
      //     template: to.meta.template 
      //   }
      // }));
    }
  });
}