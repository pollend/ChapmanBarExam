const state = {
    showSidebar: true
};

const mutations = {
    TOGGLE_SIDEBAR: (state) => {
        state.showSidebar = !state.showSidebar;
    }
};

const actions = {
    toggleSidebar({ commit }) {
        commit('TOGGLE_SIDEBAR');
    },
};

const getters = {
    toggle_sidebar: state => state.showSidebar
};

export default {
    namespaced: true,
    state,
    mutations,
    getters,
    actions,
};

