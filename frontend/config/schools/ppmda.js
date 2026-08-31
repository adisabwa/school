
export const baseUrlProd = '/ppmda/'
let env = import.meta.env ?? process.env
// console.log(env)
let baseUrl = env.MODE === 'production'
                ? env.VITE_BASE_URL_PROD  
                : env.VITE_BASE_URL;
                
// console.log('Base URL:', baseUrl, env)
export const topElementBackground = `${baseUrl}assets/images/top.png`;
export const top3ElementBackground = `${baseUrl}assets/images/top-3.png`;
export const logoDefault = `${baseUrl}assets/images/logo-kecil.png`;

export const schoolName = 'Pondok Pesantren Muhammadiyah Darul Arqom'
export const schoolNameShort = 'Darul Arqom'
export const schoolLogoTitle = 'PPM Darul Arqom'
export const schoolLogoSubTitle = 'Pagersari Patean'
export const appName = 'Sistem Informasi PPM Darul Arqom'
export const appNameShort = 'SI-PPMDA'
export const theme = 'main-template-emerald'
export const prefixTable = env.MODE === 'production' ? env.VITE_PREFIX_TABLE : 'sch_'

export const hideMenus = {
  'data' : [
    'kamar-list',
    'jurusan-list',
  ]
}


export const coordinate = [
  [
    {lat: -7.103014598182673, lng: 110.0767855113817},
    {lat: -7.102771226935774, lng: 110.07847216492648},
    {lat: -7.104180216539008, lng: 110.07885510698473},
    {lat: -7.1043296560799, lng: 110.07731474137883},
    {lat: -7.105042690128721, lng: 110.07736637314612},
    {lat: -7.1053586453430615, lng: 110.07662630630381},
    {lat: -7.105072575449675, lng: 110.07558935119675},
    {lat: -7.104457742832655, lng: 110.07576146387171},
    {lat: -7.104286957157516, lng: 110.07610137927216},
    {lat: -7.103902687796568, lng: 110.07631651589249},
    {lat: -7.103783138155965, lng: 110.07666503493893},
  ],
  [
    { lat: -7.1051327839129215, lng: 110.07572052139739,},
    { lat: -7.1048802831370494, lng: 110.07347351337121},
    { lat: -7.104262633812688, lng: 110.07357921272013},
    { lat: -7.104550095887631, lng: 110.07459310589216},
    { lat: -7.103458528628053, lng: 110.07460485194048},
    { lat: -7.10345852999506, lng: 110.07533688755548},
    { lat: -7.103023456670131, lng: 110.07561874061763},
    { lat: -7.103073955925419, lng: 110.07605717859519},
    { lat: -7.103986832446698, lng: 110.07621376443952},
    { lat: -7.104142215988794, lng: 110.07576358198735},
  ]
]