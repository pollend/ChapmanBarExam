import Vue from 'vue';
import Router from 'vue-router';
import {routePermissions} from './permission';
import {routeQuizSession} from "./quiz-session";
/* Layout */
import Layout from '@/layout/dashboard/index';
import AppLayout from '@/layout/app';
import DashboardLayout from '@/layout/dashboard';
import {ROLE_ADMIN,ROLE_USER} from '@/utils/role'
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
      { path: '', meta: { roles: [ROLE_USER] }, component: () => import('@/views/home'), name: 'app.home'},
      { path: '/class/:class_id/exam/:quiz_id/start', meta: { roles: [ROLE_USER] }, name: 'app.exam.start', component: () => import('@/views/exams/start')},
      { path: '/quiz/page/:page', meta: { roles: [ROLE_USER] }, name: 'app.session.page', component: () => import('@/views/exams/show')},
      { path: '/report', meta: {roles: [ROLE_USER]}, name: 'app.report', component: () => import('@/views/report/index')},
      { path: '/report/:report_id/', meta: {roles: [ROLE_USER]}, component:() => import('@/views/report/container'), children:[
          { path: '', meta: {roles: [ROLE_USER]}, name: 'app.report.show', component: () => import('@/views/report/show')},
          { path: 'breakdown', meta: {roles: [ROLE_USER]}, name: 'app.report.breakdown', component: () => import('@/views/report/breakdown')},
      ]}
    ],
    beforeEnter: async (to, from, next) => {
      if(await routePermissions(to,from,next) === true){
        if(await routeQuizSession(to,from,next) === true){
          next();
        }
      }
    }
  },
  { path: '/dashboard', component: DashboardLayout, children: [
      {path: '', meta: {roles: [ROLE_ADMIN]}, component: () => import('@/views/home'), name: 'dashboard.home'},
      // classes ---------------------------------------------------------------------------------------------------------------------------------------
      {path: '/class', meta: {roles: [ROLE_ADMIN]}, component: () => import('@/views/dashboard/classes'), name: 'dashboard.classes'},
      {path: '/class/:class_id', meta: {roles: [ROLE_ADMIN]}, name: 'dashboard.class', component: () => import('@/views/dashboard/classes/show')},
      {path: '/class/:class_id/report', meta: {roles: [ROLE_ADMIN]}, name: 'dashboard.class.report', component: () => import('@/views/report/show')},
      {path: '/class/:class_id/quiz/:quiz_id', meta: {roles: [ROLE_ADMIN]}, name: 'dashboard.class.quiz', component: () => import('@/views/report/show')},
      {path: '/class/:class_id/quiz/:quiz_id/report', meta: {roles: [ROLE_ADMIN]}, name: 'dashboard.class.quiz.report', component: () => import('@/views/report/show')},
      {path: '/class/add', meta: {roles: [ROLE_ADMIN]}, component: () => import('@/views/home'), name: 'dashboard.class.add'},
      // exams  ---------------------------------------------------------------------------------------------------------------------------------------
      {path: '/exam', meta: {roles: [ROLE_ADMIN]}, component: () => import('@/views/dashboard/exams'), name: 'dashboard.exam'},
      {path: '/exam', meta: {roles: [ROLE_ADMIN]}, component: () => import('@/views/dashboard/exams'), name: 'dashboard.exam.show'},


      ],
  beforeEnter: async (to, from, next) => {
      if(await routePermissions(to,from,next) === true){
          next();
      }
  }}
];

const createRouter = () => new Router({
  // mode: 'history', // require service support
  routes: routes,
  scrollBehavior (to, from, savedPosition) {
    if (to.hash) {
        return {
            selector: to.hash
            // , offset: { x: 0, y: 10 }
        }
    }
  }
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
