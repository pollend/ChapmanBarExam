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
  timeout: 10000, // Request timeout
});

// Request intercepter
service.interceptors.request.use(
  async config => {

      if (store.getters['auth/authRefreshToken'] !== null &&  store.getters['auth/authIsTokenValid'] === false) {
       try {
           console.log("refreshed token");
           await store.dispatch('auth/refresh');
           await new Promise(resolve => setTimeout(resolve, 2000));
       }
       catch (e) {
           console.log("failed to refresh token");
       }
      }

      const token = store.getters['auth/authToken'];
      if (token) {
          config.headers['Authorization'] = 'Bearer ' + token; // Set JWT token
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
