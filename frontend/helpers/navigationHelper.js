import router from '@/config/router';
import { siteUrl } from '@/config/url'

export const goTo = (routePath, params) => {
    sharedRouter.push({name: target.name, params: params});
};