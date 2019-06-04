import Vue from 'vue';
import Vuex from 'vuex';
import createLogger from 'vuex/dist/logger';

import user from './modules/user';
import app from './modules/app';
import settings from './modules/settings';
import tagsView from './modules/tags-view';
import quizSession from './modules/quiz-session';
import getters from './getters';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
  modules: {
    user,
    app,
    settings,
    tagsView,
    'quiz-session': quizSession
  },
  getters,
  strict: debug,
  plugins: debug ? [createLogger()] : [],
});
