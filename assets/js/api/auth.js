import request from '@/utils/request';

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
