import { DateTime } from 'luxon';

import Cookies from 'js-cookie';
import {getInfo, login, refreshToken} from "@/api/auth";

const TokenKey = 'Token';
const TokenKeyTTL = 'Token-ttl';
const RefreshToken = 'Refresh-token';


const state = {
  id: null,
  token: Cookies.get(TokenKey),
  refresh_token: Cookies.get(RefreshToken),
  username: '',
  avatar: '',
  introduction: '',
  ttl: Cookies.get(TokenKeyTTL),
  roles: [],
  permissions: [],
};

const mutations = {
  SET_USER: (state,user) => {
      state.id = user.id;
      state.username = user.username;
      state.roles = user.roles;
      state.permissions = user.permissions;
  },
  SET_TOKEN: (state,token) => {
      state.token = token
      if(state.token  === null) {
          Cookies.remove(TokenKeyTTL);
          Cookies.remove(TokenKey);
          state.ttl = null;
      }else{
          state.ttl = DateTime.local().toISO();
          Cookies.set(TokenKey,state.token);
          Cookies.set(TokenKeyTTL,state.ttl);
      }

  },
  SET_REFRESH_TOKEN: (state,token) => {
    state.refresh_token = token;
    if(state.refresh_token === null){
        Cookies.remove(RefreshToken);
    } else {
        Cookies.set(RefreshToken,state.refresh_token);
    }
  },
  CLEAR_USER: (state) => {
      state.id = null;
      state.username = '';
      state.roles = [];
      state.permissions = [];
  }
};

// getters
const getters = {
    username: state => state.username,
    roles: state => state.roles,
    id: state => state.id
};

const actions = {
  // user login
  login({ commit }, userInfo) {
    const { email, password } = userInfo;
    return new Promise((resolve, reject) => {
      login(email.trim(), password )
        .then(response => {
          const {token, refresh_token} = response.data;
          commit('SET_TOKEN', token);
          commit('SET_REFRESH_TOKEN',refresh_token);
          resolve();
        })
        .catch(error => {
          reject(error);
        });
    });
  },
  isTokenValid({commit,state}){
    if(state.ttl == null)
      return null;
    console.log(DateTime.local().diff(DateTime.fromISO(state.ttl),'seconds').toObject());
    if(DateTime.local().diff(DateTime.fromISO(state.ttl),'seconds').toObject()['seconds'] > 1200)
        return false;
    return true;
  },
  refresh({commit, state}){
      return new Promise((resolve, reject) => {
          refreshToken(state.refresh_token).then(response => {
              const {token, refresh_token} = response.data;
              commit('SET_TOKEN', token);
              commit('SET_REFRESH_TOKEN', refresh_token);
              resolve();
          }).catch(error => {
              // clearRefreshToken();
              commit('SET_REFRESH_TOKEN', null);
              commit('SET_TOKEN', null);
              reject(error);
          });
      });
  },
  // get user info
  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      getInfo()
        .then(response => {
          const {user} = response.data;
          commit('SET_USER', user);
          resolve(user);
        })
        .catch(error => {
          commit('CLEAR_USER');
          reject(error);
        });
    });
  },

  // user logout
  logout({ commit, state }) {
      commit('SET_TOKEN', null);
      commit('SET_REFRESH_TOKEN', null);
      commit('CLEAR_USER');
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
