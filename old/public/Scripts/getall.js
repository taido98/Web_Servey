let dataPost = {'jwt':window.localStorage.getItem('jwt')};
let getAllStudent = new PostBtn('getStudents', dataPost , '/admin/students/getall');
getAllStudent.post();
dataPost = {'jwt':window.localStorage.getItem('jwt')};
let getAllTeacher = new PostBtn('getTeachers', dataPost , '/admin/teachers/getall');
getAllTeacher.post();