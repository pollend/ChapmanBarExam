import {AppState} from "../app/app";
import {RootState} from "../../index";
import {Module} from "vuex";
import {settings} from './settings'

export  interface DashboardState {

}

export const dashboard: Module<DashboardState,RootState> = {
    namespaced:true,
    modules: {
        settings
    }
};