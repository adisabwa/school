
export const baseUrlProd = '/mihamsa/'
let env = import.meta.env ?? process.env
// console.log(env)
let baseUrl = env.MODE === 'production'
                ? env.VITE_BASE_URL_PROD  
                : env.VITE_BASE_URL;
                
// console.log('Base URL:', baseUrl, env)
export const topElementBackground = `${baseUrl}assets/images/top-mi.png`;
export const top3ElementBackground = `${baseUrl}assets/images/top-mi-3.png`;
export const logoDefault = `${baseUrl}assets/images/logo-mi-kecil.png`;
export const listApps = [
  'dashboard',
  'presensi',
  'data',
]
export const schoolName = 'MI Muhammadiyah Pagersari'
export const schoolNameShort = 'MIHAMSA'
export const schoolLogoTitle = 'MI Muhammadiyah'
export const schoolLogoSubTitle = 'Pagersari Patean'
export const appName = 'Sistem Informasi MI Muhammadiyah Pagersari'
export const appNameShort = 'SI-MIHAMSA'
export const theme = 'main-template-sky'
export const prefixTable = env.MODE === 'production' ? env.VITE_PREFIX_TABLE : 'sch_'

export const hideMenus = {
  'data' : [
    'kamar-list',
    'jurusan-list',
  ]
}

export const coordinate = [
  [
    {lat : -7.103848122080526, lng: 110.07704200421142},
    {lat : -7.103572440859424, lng: 110.07717314648848},
    {lat : -7.103507373278212, lng: 110.07794791906895},
    {lat : -7.103998805216512, lng: 110.07798415600226},
    {lat : -7.10407928364189, lng: 110.0772490704228},
  ]
]