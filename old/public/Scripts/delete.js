let data = {'jwt':window.localStorage.getItem('jwt'),
'id':'15021483'};
let delStudent = new PostBtn('deleteStudent', data , '/admin/student/delete');
delStudent.post();


data = {'jwt':window.localStorage.getItem('jwt'),
    'id':'33333'};
let delTeacher = new PostBtn('deleteTeacher', data , '/admin/teacher/delete');
delTeacher.post();