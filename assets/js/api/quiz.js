import request from '@/utils/request';


export function getQuizDatatable(query) {
    return request({
        // eslint-disable-next-line no-undef
        url: Routing.generate('get_quiz_datatable'),
        method: 'post',
        data: query
    });
}