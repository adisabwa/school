import { createStore } from 'vuex'
import configModule from './modulesOld/config';
import filterModule from './modulesOld/filter';
import authModule from './modulesOld/auth';
import penggunaModule from './modulesOld/pengguna';
import dataModule from './modulesOld/data';

export default createStore({
  modules: {
    config: configModule,
    auth: authModule,
    filter: filterModule,
    pengguna: penggunaModule,
    data: dataModule,
  },
});
