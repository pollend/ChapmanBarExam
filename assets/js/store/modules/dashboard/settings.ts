import {ActionTree, GetterTree, Module, MutationTree} from "vuex";
import {RootState} from "../../index";


export interface DashboardSettingState {
    leftPanel: boolean,
}

const TOGGLE_PANEL_LEFT_PANEL = "TOGGLE_PANEL_LEFT_PANEL";

const mutations: MutationTree<DashboardSettingState> = {
    [TOGGLE_PANEL_LEFT_PANEL]: (state,panel:boolean) => {
        state.leftPanel = !state.leftPanel;
    }
};

const getters: GetterTree<DashboardSettingState, RootState> = {
    toggleLeftPanel: state => state.leftPanel
};

const actions: ActionTree<DashboardSettingState, RootState> = {
    toggleLeftPanel(context){
        context.commit(TOGGLE_PANEL_LEFT_PANEL);
    }
};

export const settings:Module<DashboardSettingState,RootState> = {
    namespaced: true,
    state: {
        leftPanel: false
    },
    actions,
    getters,
    mutations
};