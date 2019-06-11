import request from '@/utils/request';


export function getQuizAccess(class_id) {
    return request({
        url: Routing.generate('get_classroom_quiz_access', {'class_id': class_id}),
        method: 'get'
    });
}

export function patchQuizAccess(class_id,access_id,payload) {
    return request({
        url: Routing.generate('patch_classroom_quiz_access', {'class_id': class_id,'access_id':access_id}),
        method: 'patch',
        data:payload
    });
}