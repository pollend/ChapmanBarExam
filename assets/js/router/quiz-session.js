import store from '@/store';
import 'nprogress/nprogress.css'; // progress bar style

export async function routeQuizSession(to, from, next) {
    try {
        const {id,currentPage} = await store.dispatch('quiz-session/session');
        if (to.name !== 'app.session.page' || (to.name === 'app.session.page' && to.params.page !== currentPage)){
            next({name: 'app.session.page', params: {'page': currentPage}});
            return false;
        }
    } catch (e) {
        console.log(e);
        if (to.name === 'app.session.page'){
            next({name: 'app.home'});
        }
    }
    return true;
}