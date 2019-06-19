import store from '../store';
import 'nprogress/nprogress.css';

import {NavigationGuard} from "vue-router";
import QuizSession from "../entity/quiz-session"; // progress bar style

export const routeQuizSession: NavigationGuard = async (to,from,next) => {

    try {
        const session: QuizSession = await store.dispatch('app/user-quiz-session/check');
        if (session && (to.name !== 'app.session.page' || (to.name === 'app.session.page' && +to.params.page !== session.currentPage))){
            next({name: 'app.session.page', params: {'page': ''+session.currentPage}});
            return false;
        }
    } catch (e) {
        console.log(e);
        if (to.name === 'app.session.page'){
            next({name: 'app.home'});
        }
    }
    return true;
};

