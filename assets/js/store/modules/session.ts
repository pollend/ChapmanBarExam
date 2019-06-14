import {ActionTree, GetterTree, Module, MutationTree} from "vuex";
import {RootState} from "../index";
import {AUTH_REFRESH_TOKEN, AUTH_TOKEN, AuthState} from "./auth";
import User from "../../entity/user";


export interface SessionState {
    session: null,
    isLoading: false
}

const mutations: MutationTree<SessionState> = {

};

const getters: GetterTree<SessionState,RootState> = {

};

const actions: ActionTree<SessionState,RootState> = {
};


export const auth: Module<SessionState,RootState> = {
    namespaced:true,

};