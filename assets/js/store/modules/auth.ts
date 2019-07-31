import {ActionTree, GetterTree, Module, MutationTree} from "vuex";
import {RootState} from '../index';
import service, {OLD_API} from '../../utils/request'
import User from "../../entity/user";
import * as Cookies from "js-cookie";

export interface AuthState {
    user: User | null,
    token: string | null,
    loginError: LoginError | null,
    refreshToken?: string | null,
    ttl: number,
    isLoading: boolean
}

export interface AuthResult {
    refresh_token : string,
    token: string
}

export interface LoginError {
    code: number,
    message: string
}

const AUTH_SET_TOKEN = "AUTH_SET_TOKEN";
const AUTH_SET_REFRESH_TOKEN = "AUTH_SET_REFRESH_TOKEN";
const AUTH_SET_USER = "AUTH_SET_USER";
const AUTH_CLEAR = "AUTH_CLEAR";
const AUTH_SET_LOADING_STATUS = "AUTH_SET_LOADING_STATUS";
const AUTH_SET_LOGIN_ERROR_STATUS = "AUTH_SET_LOGIN_ERROR_STATUS";

export const AUTH_TOKEN = 'AUTH_TOKEN';
export const AUTH_REFRESH_TOKEN = 'AUTH_REFRESH_TOKEN';

const mutations: MutationTree<AuthState> = {
    [AUTH_SET_USER]: (state, user: User) => {
        state.user = user;
    },
    [AUTH_SET_TOKEN]: (state, token: string) => {
        if(token) {
            state.token = token;
            state.ttl = Date.now();
            localStorage.setItem(AUTH_TOKEN, token);
            return true;
        }
        localStorage.removeItem(AUTH_TOKEN);
        return  false;
    },
    [AUTH_SET_REFRESH_TOKEN]: (state, token: string) => {
        if(token) {
            state.refreshToken = token;
            state.ttl = Date.now();
            localStorage.setItem(AUTH_REFRESH_TOKEN, token);
            return  true;
        }
        localStorage.removeItem(AUTH_REFRESH_TOKEN);
        return false;
    },
    [AUTH_CLEAR]: (state) => {
        state.refreshToken = null;
        state.token = null;
        state.user = null;
        localStorage.removeItem(AUTH_TOKEN);
        localStorage.removeItem(AUTH_REFRESH_TOKEN);
    },
    [AUTH_SET_LOADING_STATUS]: (state, isLoading: boolean) => {
        state.isLoading = isLoading;
    },
    [AUTH_SET_LOGIN_ERROR_STATUS]: (state, error: LoginError) => {
        state.loginError = error;
    }
};


const getters: GetterTree<AuthState,RootState> = {
    user: state => state.user,
    token: state => state.token,
    refreshToken: state => state.token,
    isTokenValid: state => (state.ttl ?  Math.abs(Date.now() - state.ttl)  < 1000 * 1200: false),
    isLoading: state => state.isLoading,
    loginError: state => state.loginError,
    roles: state => state.user ? state.user.roles : []
};

const actions: ActionTree<AuthState,RootState> = {
    login(context, payload: any) {
        const {email, password} = payload;
        context.commit(AUTH_SET_LOADING_STATUS,true);
        return new Promise((resolve, reject) => {
            service({
                url: '/api/auth/login',
                method: 'post',
                data: {email: email.trim(), password: password}
            }).then((response) => {
                const result: AuthResult = response.data;
                context.commit(AUTH_SET_TOKEN, result.token);
                context.commit(AUTH_SET_REFRESH_TOKEN, result.refresh_token);
                context.dispatch('me').then((response)=>{
                    context.commit(AUTH_SET_LOADING_STATUS,false);
                    resolve(result);
                }).catch((error) => {
                    console.log(error);
                    reject(error);
                    context.commit(AUTH_SET_LOADING_STATUS,false);
                });
            }).catch((error) => {
                context.commit(AUTH_CLEAR);
                console.log(error);
                context.commit(AUTH_SET_LOADING_STATUS,false);
                if(error.response){
                    context.commit(AUTH_SET_LOGIN_ERROR_STATUS,error.response.data);
                }
                reject(error);
            });
        });
    },
    me(context) {
        return new Promise((resolve, reject) => {
            service({
                url: '/_api/users/me',
                method: 'get'
            }).then((response) => {
                const user: User = response.data;
                context.commit(AUTH_SET_USER, user);
                resolve(user);
            }).catch((error) => {
                console.log(error);
                reject(error);
            })
        });
    },
    logout(context): void{
        context.commit(AUTH_CLEAR);
    },
    refresh(context): any {
        return new Promise((resolve, reject) => {
            service({
                url: '/api/auth/refresh',
                method: 'POST',
                data: {refresh_token: context.state.refreshToken}
            }).then((response) => {
                const result: AuthResult = response.data;
                context.commit(AUTH_SET_TOKEN, result.token);
                context.commit(AUTH_SET_REFRESH_TOKEN, result.refresh_token);
                resolve(result);
            }).catch((error) => {
                context.commit(AUTH_CLEAR);
                console.log(error);
                reject(error);
            })
        });
    }
};

function getToken() {
    let token = Cookies.get(AUTH_TOKEN);
    if(token) {
        Cookies.remove(AUTH_TOKEN);
        localStorage.setItem(AUTH_TOKEN,token);
        return token;
    }
    token = localStorage.getItem(AUTH_TOKEN)
    if(token) {
        return token;
    }
    return  null;
}

function getRefreshToken() {
    let token = Cookies.get(AUTH_REFRESH_TOKEN);
    if(token) {
        Cookies.remove(AUTH_REFRESH_TOKEN);
        localStorage.setItem(AUTH_REFRESH_TOKEN,token);
        return token;
    }
    token = localStorage.getItem(AUTH_REFRESH_TOKEN);
    if(token)
        return token;
    return  null;
}

export const auth: Module<AuthState,RootState> = {
    namespaced:true,
    state:{
        token: getToken(),
        refreshToken: getRefreshToken(),
        ttl: Date.now(),
        isLoading: false,
        loginError:null,
        user: null
    },
    actions,
    getters,
    mutations
};