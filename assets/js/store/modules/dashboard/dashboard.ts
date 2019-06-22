import {RootState} from "../../index";
import {ActionTree, GetterTree, Module, MutationTree} from "vuex";
import {classroom} from "./classroom/classroom";

export  interface DashboardState {
    leftPanel: boolean,
}


const TOGGLE_PANEL_LEFT_PANEL = "TOGGLE_PANEL_LEFT_PANEL";

const mutations: MutationTree<DashboardState> = {
    [TOGGLE_PANEL_LEFT_PANEL]: (state,panel:boolean) => {
        state.leftPanel = !state.leftPanel;
    }
};

const getters: GetterTree<DashboardState, RootState> = {
    toggleLeftPanel: state => state.leftPanel
};

const actions: ActionTree<DashboardState, RootState> = {
    toggleLeftPanel(context){
        context.commit(TOGGLE_PANEL_LEFT_PANEL);
    }
};

export const dashboard: Module<DashboardState,RootState> = {
    namespaced:true,
    state: {
        leftPanel: false
    },
    modules: {
        classroom
    },
    actions,
    getters,
    mutations
};