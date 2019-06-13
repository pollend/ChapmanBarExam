import { getActiveSession } from '@/api/quiz-session';
import router from '@/router';

const state = {
    session_id: null,
    quiz_id: null,
    classroom: null,
    current_page: 0,
    quiz: null,
};

const mutations = {
    GET_SESSION: (state, payload) => {
        const {id, classroom, quiz,currentPage } = payload;
        state.session_id = id;
        state.quiz_id = quiz.id;
        state.current_page = currentPage;
        state.classroom = classroom;
        state.quiz = quiz;
    },

};

const getters = {
    session_id: state => state.session_id,
    session_quiz_id: state => state.quiz_id
};

const actions = {
    session({commit, state}) {
        return new Promise((resolve, reject) => {
            getActiveSession().then(response => {
                const {session} = response.data;
                commit("GET_SESSION", session);
                resolve(session);

            }).catch(error => {
                reject(error);
            });
        });
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
};

