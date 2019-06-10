import Vue from 'vue';
import Vuex from 'vuex';
import createLogger from 'vuex/dist/logger';

import user from './modules/user';
import dashboardSetting from './modules/dashboard-setting';
import quizSession from './modules/quiz-session';
Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
  modules: {
    'user': user,
    'dashboard-setting': dashboardSetting,
    'quiz-session': quizSession
  },
  strict: debug,
  plugins: debug ? [createLogger()] : [],
});
