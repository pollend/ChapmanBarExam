import {Module} from "vuex";
import {RootState} from "../../../index";
import {show} from './show'
export  interface ClassroomState {

}

export const classroom: Module<ClassroomState,RootState> = {
    namespaced: true,
    modules: {
        show
    }
};