import Vue from 'vue';
import Cookies from 'js-cookie';
import ElementUI from 'element-ui';
import App from './App';
import store from './store';
import router from '@/router';
import i18n from './lang'; // Internationalization
import './styles/index.scss'
import * as filters from './filters'; // global filters


Vue.use(ElementUI, {
  size: Cookies.get('size') || 'medium', // set element-ui default size
  i18n: (key, value) => i18n.t(key, value),
});

import VueDataTables from 'vue-data-tables'
Vue.use(VueDataTables);

// register global utility filters.
Object.keys(filters).forEach(key => {
  Vue.filter(key, filters[key]);
});

Vue.config.productionTip = false;

// token refresh heartbeat
async function tokenRefresh( )
{
    if(store.getters['auth/refreshToken'] !== null){
        console.log("refreshed token");
        await store.dispatch('auth/refresh');
    }
    setTimeout(tokenRefresh, 60000);

}
setTimeout(tokenRefresh, 60000);



new Vue({
  el: '#app',
  router,
  store,
  i18n,
  render: h => h(App),
});
