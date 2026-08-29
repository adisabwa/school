  import Axios from 'axios';
  import {router} from "@2/shared/index";
  import { siteUrl } from './url';

  const API = Axios.create({
      baseURL: siteUrl,
      headers: {
        'X-Requested-With': 'xmlhttprequest'
      },
    });
  
  API.interceptors.response.use(
    response => response,
    error => {
      if (error.response.status === 401) {
        console.log('error interceptors');
        router.replace({ 
          name: 'default', 
          query: { 
            nextUrl: router.currentRoute.fullPath 
          }
        });
      } else if (error.response.status === 403) {
        router.replace({ 
          name: 'unauthorized' 
        });
      }
      return Promise.reject(error);
    }
  );

  export default API;
