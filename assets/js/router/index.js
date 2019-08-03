import NProgress from 'nprogress';
import Vue from 'vue';
import Router from 'vue-router';
import {routePermissions} from './permission';
import {routeQuizSession} from "./quiz-session";
/* Layout */
import AppLayout from './../layout/app/index';
import DashboardLayout from './../layout/dashboard/index';
import {ROLE_ADMIN,ROLE_USER} from '../utils/role'
/* Router for modules */

Vue.use(Router);

export const routes = [
  { path: '/redirect', component: AppLayout,
    children: [
      {
        path: '/redirect/:path*',
        component: () => import('./../views/redirect/index.vue'),
      },
    ],
  },
  { path: '/login', component: () => import('./../views/login/index'), name: 'app.login' , beforeEnter: async (to, from, next) => {
    if(await routePermissions(to,from,next) === true)
      next();
  }},
  { path: '/auth-redirect', component: () => import('./../views/login/AuthRedirect')},
  { path: '/404', redirect: { name: 'Page404' }, component: () => import('./../views/errors/404')},
  { path: '/401', component: () => import('./../views/errors/401') },
  { path: '/', component: AppLayout, children: [
      { path: '', meta: { roles: [ROLE_USER] }, component: () => import('@/views/home'), name: 'app.home'},
      { path: '/access/:quiz_access_id', meta: { roles: [ROLE_USER] }, name: 'app.exam.start', component: () => import('../views/exams/start')},
      { path: '/quiz/page/:page', meta: { roles: [ROLE_USER] }, name: 'app.session.page', component: () => import('../views/exams/show')},
      { path: '/report', meta: {roles: [ROLE_USER]}, name: 'app.report', component: () => import('../views/report/index')},
      { path: '/report/:report_id/', meta: {roles: [ROLE_USER]}, component:() => import('../views/report/container'), children:[
          { path: '', meta: {roles: [ROLE_USER]}, name: 'app.report.show', component: () => import('../views/report/show')},
          { path: 'breakdown', meta: {roles: [ROLE_USER]}, name: 'app.report.breakdown', component: () => import('../views/report/breakdown')},
      ]}
    ],
    beforeEnter: async (to, from, next) => {
      NProgress.start()
      if(await routePermissions(to,from,next) === true){
        if(await routeQuizSession(to,from,next) === true){
          next();
        }
      }
      NProgress.done();
    }
  },
  { path: '/dashboard', component: DashboardLayout, children: [
      {path: '', meta: {roles: [ROLE_ADMIN]}, component: () => import('./../views/home'), name: 'dashboard.home'},
      // classes ---------------------------------------------------------------------------------------------------------------------------------------
      {path: '/class/create', meta: {roles: [ROLE_ADMIN]}, component: () => import('./../views/dashboard/classes/create'), name: 'dashboard.classes.create'},
      {path: '/class', meta: {roles: [ROLE_ADMIN]}, component: () => import('./../views/dashboard/classes'), name: 'dashboard.classes'},
      {path: '/class/:class_id/', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/classes/container'), children: [
          {path: 'report',name: 'dashboard.class.report', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/classes/exam/index')},
          {path: 'report/:report_id/', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/classes/exam/container'), children: [
              {path: 'distribution',name: 'dashboard.class.report.score_distribution', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/classes/exam/score-distribution')},
              {path: 'standard',name: 'dashboard.class.report.item_analysis', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/classes/exam/standard-item')},
          ]},
          {path: '',name: 'dashboard.class', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/classes/show')},
          {path: 'users',name: 'dashboard.class.user', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/classes/user')},
          {path: 'whitelist',name: 'dashboard.class.whitelist', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/classes/whitelist')}
      ]},
      {path: '/class/:class_id/quiz/:quiz_id', meta: {roles: [ROLE_ADMIN]}, name: 'dashboard.class.quiz', component: () => import('../views/report/show')},
      {path: '/class/:class_id/quiz/:quiz_id/report', meta: {roles: [ROLE_ADMIN]}, name: 'dashboard.class.quiz.report', component: () => import('@/views/report/show')},
      {path: '/class/add', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/home'), name: 'dashboard.class.add'},
      // exams  ---------------------------------------------------------------------------------------------------------------------------------------
      {path: '/exam', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/exams'), name: 'dashboard.exam'},
      {path: '/exam/:quiz_id/show', meta: {roles: [ROLE_ADMIN]}, component: () => import('../views/dashboard/exams/show'), name: 'dashboard.exam.show'},


      ],
  beforeEnter: async (to, from, next) => {
      if(await routePermissions(to,from,next) === true){
          next();
      }
  }}
];


const createRouter = () => new Router({
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
export default router;
