import { createRouter, createWebHistory , createWebHashHistory} from 'vue-router'
const school = import.meta.env.VITE_SCHOOL
const {
  listApps
} = await import(`@/config/schools/${school}.js`)

const moduleRoutes = import.meta.glob('@/modules/**/routes.js', { eager: true });
// Vue router
let allRoutes = []
// 2. Gabungkan rute dari setiap modul ke dalam array allRoutes
Object.keys(moduleRoutes).forEach((path) => {
  const routeModule = moduleRoutes[path].default;
  let app = routeModule[0]?.meta?.app
  // console.log('app', app, 'listApps', listApps, app && listApps && listApps.includes(app))
  if (app && listApps && !listApps.includes(app)) {
    return;
  }
  if (Array.isArray(routeModule)) {
    allRoutes = [...allRoutes, ...routeModule];
  }
  // console.log('allRoutes', allRoutes)
});

console.log(allRoutes)
const router = new createRouter({
  history: createWebHistory(),
  routes: allRoutes,
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

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  // Simpan route tujuan ke store
  authStore.setRoute(to)

  try {
    // 1. Pastikan pengecekan user selesai sebelum lanjut ke logika bawah
    // Gunakan await langsung, tidak perlu .then() di dalam async function agar rapi
    await authStore.checkUser()
    console.log('User check completed')

    const loggedUser = authStore.loggedUser
    const isAuthenticated = authStore.role != ''

    // 2. Logika Pengecekan Auth
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth || record.meta.allowedRoles?.length > 0)
    console.log('requiresAuth:', requiresAuth, 'isAuthenticated:', isAuthenticated, 'loggedUser:', loggedUser)
    if (requiresAuth) {
      if (!isAuthenticated) {
        // Belum login, arahkan ke halaman default/login
        return next({
          name: 'dashboard',
          query: { nextUrl: to.fullPath }
        })
      } 
      
      // Sudah login, cek Role
      const app = to.meta.app
      if (app && to.meta.allowedRoles) {
        const role = loggedUser?.app_roles?.[app] ?? loggedUser?.app_roles?.all
        
        if (to.meta.allowedRoles.includes(role)) {
          return next()
        } else {
          const redirectName = to.meta.redirect || 'unauthorized'
          return next({ name: redirectName })
        }
      }
      
      // Jika butuh auth tapi tidak ada batasan role spesifik
      return next()

    } else {
      // 3. Logika untuk halaman publik (tidak butuh auth)
      // Jika sudah login tapi mencoba akses halaman login ('default'), lempar ke dashboard
      if (to.name === 'default' && isAuthenticated) {
        return next({ name: 'dashboard' })
      }
      return next()
    }

  } catch (err) {
    console.error('Navigation error atau User check failed:', err)
    // Jika gagal total (misal API error), lempar ke halaman default
    return next({ name: 'dashboard' })
  }
})

export default router