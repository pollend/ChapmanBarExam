import {RootState} from "../../index";
import {ActionTree, GetterTree, Module, MutationTree} from "vuex";

export interface ReportState {
    report: any,
    isLoading: boolean
}

const mutations: MutationTree<ReportState> = {

};

const getters: GetterTree<ReportState,RootState> = {

};

const actions: ActionTree<ReportState,RootState> = {

};

export const report: Module<ReportState,RootState> = {
    namespaced: true,
    mutations,
    getters,
    actions
};