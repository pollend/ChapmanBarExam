import request from '@/utils/request';

export function getActiveSession() {
    return request({
        url: Routing.generate('get_active_session'),
        method: 'get'
    });
}

export function getSession(id) {
    return request({
        url: Routing.generate('get_session',{session_id:id}),
        method: 'get'
    })
}

export  function getQuestions(quiz_id, page) {
    return request({
        url: Routing.generate('get_quiz_questions', {quiz_id: quiz_id, page: page}),
        method: 'get'
    });
}

export function getResponses(session_id,page) {
    return request({
        url: Routing.generate('get_quiz_session_questions', {session_id: session_id, page: page}),
        method: 'get'
    })
}

export function postResponse(session_id,page,payload) {
    return request({
        url: Routing.generate('post_quiz_session_questions', {session_id: session_id, page: page}),
        method: 'post',
        data: payload
    })
}