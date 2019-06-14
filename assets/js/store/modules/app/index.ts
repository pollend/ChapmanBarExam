import {Module} from "vuex";
import {RootState} from "../../index";

export  interface AppState {

}

export const app: Module<AppState,RootState> = {
    namespaced:true,
    modules: {
    }
};