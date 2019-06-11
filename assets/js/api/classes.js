import request from '@/utils/request';
export function getClassesByUser(id) {
  return request({
    // eslint-disable-next-line no-undef
    url: Routing.generate('get_classroom_by_owner', { user_id: id }),
    method: 'get',
  });
}

export function getClassesDatatable(query) {
  return request({
    // eslint-disable-next-line no-undef
    url: Routing.generate('get_classrooms_datatable'),
    method: 'post',
    data: query
  });
}

export function getClass(class_id) {
  return request({
    // eslint-disable-next-line no-undef
    url: Routing.generate('get_classroom', {class_id: class_id}),
    method: 'get'
  });
}

export function patchClass(class_id,data) {
  return request({
    url: Routing.generate('patch_classroom', {class_id: class_id}),
    method: 'patch',
    data: data
  });
}

export function postClassroomQuizStart(class_id,quiz_id) {
  return request({
    url: Routing.generate('post_classroom_quiz_start', {class_id: class_id, quiz_id: quiz_id}),
    method: 'post'
  })
}
