import store from '@/store';
import 'nprogress/nprogress.css'; // progress bar style

export async function routeQuizSession(to, from, next) {
    try {
        const {id,current_page} = await store.dispatch('quiz-session/session');
        if (to.name !== 'app.session.page' || (to.name === 'app.session.page' && to.params.page !== current_page)){
            next({name: 'app.session.page', params: {'page': current_page}});
            return false;
        }
    } catch (e) {
        if (to.name === 'app.session.page'){
            next({name: 'app.home'});
        }
    }
    return true;
}