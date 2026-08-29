import { APP_ROUTES } from '@2/shared/router/manifest';
import { router as sharedRouter } from '@2/shared/index'; // Router yang di-pass ke shared/index.js
import { siteUrl } from '@2/shared/config/url'

export const defaultPage = () => {
    window.location.href = siteUrl;
}

export const goTo = (routePath, params) => {
    let target = null;
    console.log(routePath)
    // Jika input adalah string (misal: 'admin.siswa')
    if (typeof routePath === 'string') {
        target = getObjectValueByPath(APP_ROUTES, routePath)
    } else {
        // Jika input sudah berupa objek
        target = routePath;
    }

    console.log(target)
    if (!target || !target.path) {
        console.error(`Route "${routePath}" tidak ditemukan di manifest!`);
        return;
    }

    const currentPath = window.location.pathname;
    let path = currentPath.split('/p/')[1];
    console.log(path)
    let keys = findPathbyValue(APP_ROUTES,path) ?? '.'
    
    let isSameApp = keys.split('.')[0] == routePath.split('.')[0]
    console.log(isSameApp, target)
    if (isSameApp && sharedRouter) {
        sharedRouter.push({name: target.name, params: params});
    } else {
        window.location.href = siteUrl + 'p/' +target.path + '?=' + toQueryString(params);
    }
};