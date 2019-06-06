import axios from 'axios';
import { Message } from 'element-ui';
import store from "@/store";

// Create axios instance
const service = axios.create({
  baseURL: process.env.MIX_BASE_API,
  timeout: 10000, // Request timeout
});

// Request intercepter
service.interceptors.request.use(
  async config => {
      const refreshToken = store.state.user.refresh_token;
      if (store.state.user.refresh_token !== null && await store.dispatch('user/isTokenValid') === false) {
       try {
           console.log("refreshed token");
           await store.dispatch('user/refresh');
           await new Promise(resolve => setTimeout(resolve, 2000));
       }
       catch (e) {
           console.log("failed to refresh token");
       }
      }


      const token = store.state.user.token;
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
