import Vue from 'vue';
import Vuex, {Module, StoreOptions} from 'vuex';
import createLogger from 'vuex/dist/logger';
import {auth, AuthState} from './modules/auth'
import {app} from "./modules/app/app";
Vue.use(Vuex);

export interface RootState {
  version: string,
  auth?: AuthState
}
const debug = process.env.NODE_ENV !== 'production';

const store: StoreOptions<RootState> = {
  state:{
    version: '1.0.0'
  },
  modules: {
    auth,
    app
  },
  strict: debug,
  plugins: debug ? [createLogger()] : []
};

export default new Vuex.Store<RootState>(store);


// import user from './modules/user';
// import dashboardSetting from './modules/dashboard-setting';
// import quizSession from './modules/quiz-session';
// Vue.use(Vuex);
//
// const debug = process.env.NODE_ENV !== 'production';
//
// export default new Vuex.Store({
//   modules: {
//     'user': user,
//     'dashboard-setting': dashboardSetting,
//     'quiz-session': quizSession
//   },
//   strict: debug,
//   plugins: debug ? [createLogger()] : [],
// });
