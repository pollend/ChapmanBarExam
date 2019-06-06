import request from '@/utils/request';

export function getReports(user_id) {
    return request({
        url: Routing.generate('get_reports',{user_id: user_id}),
        method: 'get'
    });
}