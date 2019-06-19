import store from '../store';

export const ROLE_ADMIN = 'ROLE_ADMIN';
export const ROLE_USER = 'ROLE_USER';


/**
 * @param {Array} value
 * @returns {Boolean}
 * @example see @/views/permission/Directive.vue
 */
export default function checkRole(value: string[] | string) {
  if (value && value instanceof Array && value.length > 0) {
    const roles = store.getters && store.getters.roles;
    const requiredRoles = value;

    const hasRole = roles.some((role : string)=> {
      return requiredRoles.includes(role);
    });

    return hasRole;
  } else {
    console.error(`Need roles! Like v-role="['admin','editor']"`);
    return false;
  }
}
