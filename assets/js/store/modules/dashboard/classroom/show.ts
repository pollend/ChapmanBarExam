import {ActionTree, GetterTree, Module, MutationTree} from "vuex";
import Classroom from "../../../../entity/classroom";
import {RootState} from "../../../index";
import service from "../../../../utils/request";


export interface ShowQuizState {
    classroom: Classroom;
    loading: boolean;
}

const SET_CLASS = "SET_CLASS";
const SET_LOADING = "SET_LOADING";

const mutations: MutationTree<ShowQuizState> = {
    [SET_CLASS]: (state,classroom: Classroom) => {
        state.classroom = classroom;
    },
    [SET_LOADING] : (state, loading:boolean) => {
        state.loading = loading;
    }
};

const getters: GetterTree<ShowQuizState,RootState> = {
    classroom: state => state.classroom
};

const actions: ActionTree<ShowQuizState,RootState> = {
    query(context, uid: number) {
        context.commit(SET_LOADING, true);
        return new Promise<Classroom>((resolve, reject) => {
            service({
                url: '/_api/classrooms/' + uid,
                method: 'get'
            }).then((response) => {
                const classroom: Classroom = response.data;
                context.commit(SET_CLASS, classroom);
                context.commit(SET_LOADING, false);
                resolve(classroom);
            }).catch((err) => {
                context.commit(SET_LOADING, false);
                reject(err);
            })
        });
    },
    submit(context,payload:Classroom) {

    }
};

export const show: Module<ShowQuizState,RootState> = {
    namespaced: true,
    state: {
        classroom: null,
        loading:false
    },
    actions,
    getters,
    mutations
};