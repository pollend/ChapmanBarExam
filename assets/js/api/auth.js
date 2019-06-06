import request from '@/utils/request';
import axios from "axios";
import store from '@/store'

export function login(email, password) {
  return request({
    url: '/api/auth/login',
    method: 'post',
    data: {email: email, password: password},
  });
}

export function getInfo() {
  return request({
    url: 'api/v1/auth/me',
    method: 'get',
  });
}

export function logout() {
  return request({
    url: 'api/auth/logout',
    method: 'post',
  });
}

const service = axios.create({
  baseURL: process.env.MIX_BASE_API,
  timeout: 10000, // Request timeout
});
service.interceptors.request.use(
    async config => {
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

export function refreshToken(token) {
  return service({
    url: '/api/auth/refresh',
    method: 'POST',
    data: {refresh_token: token}
  });
}
