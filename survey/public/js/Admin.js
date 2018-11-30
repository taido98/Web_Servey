var mainTable = document.getElementById('col-8');
var getAllClasses = function () {
    let xmlhttp = getXmlHttpObject();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState === 4) {

            console.log(this.status);
            console.log(this.responseText);
            if (this.status === 200) {
                let responseObj = JSON.parse(this.responseText);
                if (responseObj['ok'] === "true") {
                    // get main table
                    mainTable.innerHTML = '';
                    let table = new Table(mainTable, 'table table-hover',
                        'title', 'sub');

                    table.generate(responseObj['data']);
                }
            }
        }

    };
    let data = new FormData();
    data.append('jwt', window.localStorage.getItem('jwt'));
    xmlhttp.open("POST", "/admin/classes/getall", true);
    xmlhttp.send(data);
};
var getStudents = function () {
    let dataPost = {'jwt': window.localStorage.getItem('jwt')};
    request('POST', dataPost,
        '/admin/students/getall', function (response) {
            if (response['ok'] === 'true') {
                mainTable.innerHTML = '';
                let table = new Table(mainTable, 'table table-hover',
                    'title', 'sub', ['Edit', 'Delete'], 'idStudent');

                table.generate(response['data'],
                    ['<td class="ABC"><a href="#" class="" id="edit',
                        '<td class="ABC"><a href="#" class="" id="delete'],
                    ['"><i class="fas fa-check"></i></a>\n' +
                    '<a href="#" class="edit"><i class="fas fa-trash"></i></a></td>',
                        '"><i class="fas fa-check"></i></a>\n' +
                        '<a href="#" class="delete"><i class="fas fa-trash"></i></a></td>']);
                for (let i = 0; i < response['data'].length; ++i) {
                    document.getElementById('delete' + i).onclick = function () {
                        request('POST',
                            {
                                'jwt': window.localStorage.getItem('jwt'),
                                'id': table.rows[i]
                            },
                            '/admin/student/delete', function (response) {
                                if (response['ok'] === true) {
                                    console.log('delete row');
                                    document.getElementById('rowBody'+i).outerHTML = '';

                                }
                            })
                    }
                }
            }
        })
};
let xmlhttp = getXmlHttpObject();
xmlhttp.onreadystatechange = function () {
    if (this.readyState === 4) {

        console.log(this.status);
        console.log(this.responseText);
        if (this.status === 200) {
            let responseObj = JSON.parse(this.responseText);
            if (responseObj['ok'] === "true") {
                getAllClasses();

            } else {
                window.location.replace(responseObj['route']);
            }
        }
    }

};
let data = new FormData();
data.append('jwt', window.localStorage.getItem('jwt'));
xmlhttp.open("POST", "/admin/getProfile", true);
xmlhttp.send(data);


let logout = document.getElementById("logout");
logout.href = '/logout?jwt=' + window.localStorage.getItem('jwt');
// logout.onclick = function () {
//     request('POST', {'jwt':window.localStorage.getItem('jwt')},
//         'logout', function (response) {
//             if(response['ok'] === 'true') {
//                 window.location.replace(response['route']);
//             }
//         })
// };

document.getElementById('getTeachers').onclick = function (event) {
    let dataPost = {'jwt': window.localStorage.getItem('jwt')};
    request('POST', dataPost,
        '/admin/teachers/getall', function (response) {
            if (response['ok'] === 'true') {
                mainTable.innerHTML = '';
                let table = new Table(mainTable, 'table table-hover',
                    'title', 'sub', ['Edit', 'Delete'], 'idTeacher');

                table.generate(response['data'],
                    ['<td class="ABC"><a href="#" class="" id="edit',
                        '<td class="ABC"><a href="#" class="" id="delete'],
                    ['"><i class="fas fa-check"></i></a>\n' +
                    '<a href="#" class="edit"><i class="fas fa-trash"></i></a></td>',
                        '"><i class="fas fa-check"></i></a>\n' +
                        '<a href="#" class="delete"><i class="fas fa-trash"></i></a></td>']);
                for (let i = 0; i < response['data'].length; ++i) {
                    document.getElementById('delete' + i).onclick = function () {
                        request('POST',
                            {
                                'jwt': window.localStorage.getItem('jwt'),
                                'id': table.rows[i]
                            },
                            '/admin/teacher/delete', function (response) {
                                if (response['ok'] === 'true') {
                                    if (table.body.length > 0) {
                                        table.body.deleteRow(i);
                                    }
                                }
                            })
                    }
                }
            }
        })
};
document.getElementById('getClasses').onclick = function () {
    getAllClasses();


};
document.getElementById('getStudents').onclick = function () {
    getStudents();
};


