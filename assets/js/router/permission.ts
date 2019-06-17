import store from '../store';
import { Message } from 'element-ui';
import 'nprogress/nprogress.css';
import {NavigationGuard, RouteRecord} from "vue-router"; // progress bar style

function canAccess(roles: string[], permissions: string[], routes: RouteRecord[]) {
  return routes.every((route) => {
    if (route.meta) {
      let hasRole = true;
      let hasPermission = true;
      if (route.meta.roles || route.meta.permissions) {
        // If it has meta.roles or meta.permissions, accessible = hasRole || permission
        hasRole = false;
        hasPermission = false;
        if (route.meta.roles) {
          hasRole = roles.some(role => route.meta.roles.includes(role));
        }

        if (route.meta.permissions) {
          hasPermission = permissions.some(permission => route.meta.permissions.includes(permission));
        }
      }
      return hasRole || hasPermission;
    }
  });
}

export const routePermissions: NavigationGuard = async (to, from, next) => {
  // set page title
  // document.title = getPageTitle(to.meta.title);
  // try to access page with no credentials
  if (canAccess([], [], to.matched)) {
    return true;
  } else {
    // check if user has token
    const hasToken = store.state.auth.token;
    if (hasToken) {
      // try to check user with the server
      try {
        if(!store.getters['auth/user'])
          await store.dispatch('auth/me')
        const roles = store.getters['auth/roles'];
        if (canAccess(roles, [], to.matched)) {
          return true;
        } else {
          next(`/login?redirect=${to.path}`);
        }
      } catch (e) {
        // remove token and go to login page to re-login
        await store.dispatch('user/logout');
        Message.error(e || 'Has Error');
        next(`/login?redirect=${to.path}`);
      }
    } else {
      next(`/login?redirect=${to.path}`);
    }
  }
  return false;
}