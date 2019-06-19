import {Module} from "vuex";
import {RootState} from "../../index";
import {userQuizSession} from './user-quiz-session'
import {report} from "./report";

export  interface AppState {

}

export const app: Module<AppState,RootState> = {
    namespaced:true,
    modules: {
        'user-quiz-session': userQuizSession,
        'report': report,
    }
};