const prodDomain = 'https://codev-app.my.id/';
const devDomain = 'http://simak.test/';

const baseUrl = process.env.NODE_ENV === 'production' 
                ? import.meta.env.VITE_BASE_URL_PROD 
                : import.meta.env.VITE_BASE_URL;

const baseUrlFull = process.env.NODE_ENV === 'production' 
                ? prodDomain + baseUrl
                : devDomain + baseUrl;

const siteUrl = baseUrl + 'index.php/';
const siteUrlFull = baseUrlFull + 'index.php/';
const defaultRoute = 'dashboard'

export { baseUrl };
export { baseUrlFull };
export { siteUrl };
export { siteUrlFull };
export { defaultRoute };

export default { 
  baseUrl,
  baseUrlFull, 
  siteUrl,
  siteUrlFull,
  defaultRoute,
};