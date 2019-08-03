import axios from 'axios';
import { Message } from 'element-ui';
import store, {RootState} from "../store";
import {Module} from "vuex";
import {AuthState} from "../store/modules/auth";

export const OLD_API = '/api/';
export const _API = '/_api/';

// Create axios instance
const service = axios.create({
  baseURL: process.env.MIX_BASE_API,
  timeout: 0, // Request timeout
});


const MAX_REQUESTS_COUNT = 5
const INTERVAL_MS = 10
let PENDING_REQUESTS = 0
// Request intercepter
service.interceptors.request.use(
  async config => {

      if (store.getters['auth/refreshToken'] !== null &&  store.getters['auth/isTokenValid'] === false) {
       try {
           console.log("refreshed token");
           await store.dispatch('auth/refresh');
           await new Promise(resolve => setTimeout(resolve, 2000));
       }
       catch (e) {
           console.log("failed to refresh token");
       }
      }

      const token = store.getters['auth/token'];
      if (token) {
          config.headers['X-Authorization'] = 'Bearer ' + token; // Set JWT token
      }

      return config;
  },
  error => {
    // Do something with request error
    console.log(error); // for debug
    Promise.reject(error);
  }
);

export default service;
