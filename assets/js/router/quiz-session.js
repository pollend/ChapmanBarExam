import store from '@/store';
import { Message } from 'element-ui';
import 'nprogress/nprogress.css'; // progress bar style
import { getToken } from '@/utils/auth'; // get token from cookie
import getPageTitle from '@/utils/get-page-title';

export async function routeQuizSession(to, from, next) {
    try {
        const {id} = await store.dispatch('quiz-session/session');
        if (to.name !== 'app.session.page') {
            next({name: 'app.session.page', params: {'page': 0}});
            return false;
        }
    } catch (e) {
    }
    return true;
}