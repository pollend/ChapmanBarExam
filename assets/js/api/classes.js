import request from '@/utils/request';

export function getClassesByUser(id) {
  return request({
    // eslint-disable-next-line no-undef
    url: Routing.generate('get_classroom_by_owner', { user_id: id }),
    method: 'get',
  });
}

export function postClassroomQuizStart(class_id,quiz_id) {
  return request({
    url: Routing.generate('post_classroom_quiz_start', {class_id: class_id, quiz_id: quiz_id}),
    method: 'post'
  })
}
