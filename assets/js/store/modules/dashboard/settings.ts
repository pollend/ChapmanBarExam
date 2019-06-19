import {ActionTree, GetterTree, Module, MutationTree} from "vuex";
import {RootState} from "../../index";


export interface DashboardSetting {
    leftPanel: boolean,
}

const TOGGLE_PANEL_LEFT_PANEL = "TOGGLE_PANEL_LEFT_PANEL";

const mutations: MutationTree<DashboardSetting> = {
    [TOGGLE_PANEL_LEFT_PANEL]: (state,panel:boolean) => {
        state.leftPanel = !state.leftPanel;
    }
};

const getters: GetterTree<DashboardSetting, RootState> = {
    toggleLeftPanel: state => state.leftPanel
};

const actions: ActionTree<DashboardSetting, RootState> = {
    toggleLeftPanel(context){
        context.commit(TOGGLE_PANEL_LEFT_PANEL);
    }
};

export const settings:Module<DashboardSetting,RootState> = {
    namespaced: true,
    state: {
        leftPanel: false
    },
    actions,
    getters,
    mutations
};