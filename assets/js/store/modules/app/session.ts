import {ActionTree, GetterTree, Module, MutationTree} from "vuex";
import {AppState} from "./index";

export interface SessionState {
    session: null,
    isLoading: false
}

const mutations: MutationTree<SessionState> = {

};

const getters: GetterTree<SessionState,AppState> = {

};

const actions: ActionTree<SessionState,AppState> = {
};


export const auth: Module<SessionState,AppState> = {
    namespaced:true,

};