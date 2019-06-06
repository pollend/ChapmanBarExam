import Vue from 'vue';
import Router from 'vue-router';
import {routePermissions} from './permission';
import {routeQuizSession} from "./quiz-session";
/* Layout */
import Layout from '@/layout/dashboard/index';
import AppLayout from '@/layout/app';
/* Router for modules */

Vue.use(Router);

export const routes = [
  { path: '/redirect', component: Layout, hidden: true,
    children: [
      {
        path: '/redirect/:path*',
        component: () => import('@/views/redirect'),
      },
    ],
  },
  { path: '/login', component: () => import('@/views/login/index'), hidden: true , beforeEnter: async (to, from, next) => {
    if(await routePermissions(to,from,next) === true)
      next();
  }},
  { path: '/auth-redirect', component: () => import('@/views/login/AuthRedirect')},
  { path: '/404', redirect: { name: 'Page404' }, component: () => import('@/views/errors/404')},
  { path: '/401', component: () => import('@/views/errors/401') },
  { path: '/', component: AppLayout, children: [
      { path: '', meta: { roles: ['user'] }, component: () => import('@/views/home'), name: 'app.home'},
      { path: '/class/:class_id/exam/:quiz_id/start', meta: { roles: ['user'] }, name: 'app.exam.start', component: () => import('@/views/exams/start')},
      { path: '/quiz/page/:page', meta: { roles: ['user'] }, name: 'app.session.page', component: () => import('@/views/exams/show')},
      { path: '/reports', meta: {roles: ['user']}, name: 'app.reports', component: () => import('@/views/report/index')},
    ],
    beforeEnter: async (to, from, next) => {
      if(await routePermissions(to,from,next) === true){
        if(await routeQuizSession(to,from,next) === true){
          next();
        }
      }
    }
  }
];

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: routes,
});

const router = createRouter();

// router.beforeEach(async (to, from, next) => {
//   if(await routePermissions(to,from,next) === true){
//     if(await routeQuizSession(to,from,next)){
//       next();
//     }
//   }
// });
//
// router.afterEach(() => {
// });



export default router;
