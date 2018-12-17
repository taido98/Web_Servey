
var showMessage = function () {
    this.MessageType = {'Error':0, "Success": 2};
    this.show = function (message, messageType) {
        let responseMess = document.getElementById('responseMessage');
        switch (messageType) {
            case this.MessageType.Success:
                responseMess.innerText = 'Success';
                responseMess.style.display = 'inline';
                responseMess.style.color = 'green';
                break;
            case this.MessageType.Error:
                responseMess.innerText = 'Error';
                responseMess.style.display = 'inline';
                responseMess.style.color = 'red';
                break;
        }
        setTimeout(function () {
            responseMess.style.display = 'none';
        }, 2000)
    };
    this.ShowMessageSuccess = function () {
        this.show('Success', this.MessageType.Success);
    };
    this.ShowMessageError = function () {
        this.show('Error', this.MessageType.Error);
    };
};

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
            let showMessageObj = new showMessage();
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
                for (let i = 0; i < table.body.rowsTable.length; ++i) {
                    table.body.rowsTable[i].onclick = function () {
                        request('POST',
                            {
                                'jwt': window.localStorage.getItem('jwt'),
                                'id': this.idRequest
                            },
                            '/admin/student/delete', function (response) {
                                let showMessageObj = new showMessage();
                                if (response['ok'] === true) {
                                    showMessageObj.show('Success', showMessageObj.MessageType.Success);

                                    console.log('delete row');
                                    document.getElementById('rowBody' + i).outerHTML = '';

                                } else {
                                    showMessageObj.show('Error', showMessageObj.MessageType.Error);
                                }
                            })
                    }
                }
            } else {
                showMessageObj.show('Error', showMessageObj.MessageType.Error);
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

document.getElementById('getTeachers').onclick = function () {
    let dataPost = {'jwt': window.localStorage.getItem('jwt')};
    request('POST', dataPost,
        '/admin/teachers/getall', function (response) {
            if (response['ok'] === 'true') {
                mainTable.innerHTML = '';
                let table = new Table(mainTable, 'table table-hover',
                    'title', 'sub', ['Delete'], 'idTeacher');

                table.generate(response['data'],
                    ['<td class="ABC"><a href="#" class="" id="delete'],
                    ['"><i class="fas fa-check"></i></a>\n' +
                        '<a href="#" class="delete"><i class="fas fa-trash"></i></a></td>']);
                for (let i = 0; i < table.body.rowsTable.length; ++i) {
                    let deleteI = table.body.rowsTable[i];

                    deleteI.onclick = function () {

                        request('POST',
                            {
                                'jwt': window.localStorage.getItem('jwt'),
                                'id': this.idRequest
                            },
                            '/admin/teacher/delete', function (response) {
                                let responseMess = document.getElementById('responseMessage');
                                if (response['ok'] === true) {
                                    console.log('delete row ' + i);
                                    table.deleteRow(this.index);
                                    responseMess.innerHTML = 'success';
                                    responseMess.style.display = 'inline';
                                    responseMess.style.color = 'green';
                                    setTimeout(function () {
                                        document.getElementById('responseMessage').style.display = 'none';
                                    }, 2000);
                                    console.log('success');
                                } else {
                                    responseMess.innerHTML = 'error';
                                    responseMess.style.display = 'inline';
                                    responseMess.style.color = 'red';
                                    console.log('error');

                                }
                            }.bind(this))
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

let windowOverLay = document.getElementById('window-overlay');
// windowOverLay.onclick = function() {
//     console.log("click");
//     this.style.display = 'none';
// };

let addNews = document.getElementsByClassName('addNew');
for (let i = 0; i < addNews.length; ++i) {
    addNews[i].onclick = function () {
        document.getElementById('fileUpload').value = '';
        windowOverLay.style.display = "flex";
        document.getElementById('closeCard').onclick = function () {
            document.getElementById('window-overlay').style.display = 'none';

        };
        document.getElementById('submit').onclick = function () {
            // this.disabled = true;
            let xmlhttp = getXmlHttpObject();
            xmlhttp.onreadystatechange = function () {
                let showMessageObj = new showMessage();
                if (this.readyState === 4) {
                    console.log(this.status);
                    console.log(this.responseText);
                    if (this.status === 200) {

                        let responseObj = JSON.parse(this.responseText);
                        if (responseObj['ok'] === true) {
                            showMessageObj.show('Success', showMessageObj.MessageType.Success);

                        }
                        // window.localStorage.setItem('jwt', responseObj['jwt']);
                        //
                        // window.location.replace("../admin.html");
                    } else {
                        showMessageObj.show('Error', showMessageObj.MessageType.Error);
                    }
                } else {
                    showMessageObj.ShowMessageError();
                }

            };
            let file = document.getElementById('fileUpload').files[0];
            if (file) {
                let data = new FormData();
                data.append('jwt', window.localStorage.getItem('jwt'));
                data.append('file', file);
                xmlhttp.open("POST", "/admin/" + addNews[i].id + "/add", true);
                xmlhttp.send(data);


            }
        }
    }
}
// let file = document.getElementById('fileUpload').files[0];
// if (file) {
//     let data = new FormData();
//     data.append('jwt', window.localStorage.getItem('jwt'));
//     data.append('file', file);
//     xmlhttp.open("POST", "/admin/students/add", true);
//     xmlhttp.send(data);
// }


