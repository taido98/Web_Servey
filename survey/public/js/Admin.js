function hideNotNeed() {
    document.getElementById('tabInfor').style.display = 'none';

}
var conformDeleForm = new FormOverlap(document.getElementById('tabDelete'),
    document.getElementById('yes'), document.getElementById('no'));
var showMessage = function () {
    this.MessageType = {'Error': 0, "Success": 2};
    this.show = function (message, messageType) {
        let responseMess = document.getElementById('responseMessage');
        switch (messageType) {
            case this.MessageType.Success:
                responseMess.innerText = 'Success';
                responseMess.style.display = 'inline';
                responseMess.style.color = 'green';
                break;
            case this.MessageType.Error:

                console.log('error');
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
    this.ShowMessageWaiting = function () {
        this.show('Waiting', this.MessageType.Success);
    }
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
                        'title', 'sub',['edit', 'delete','view'],'idClass');

                    table.generate(responseObj['data'],
                        ['<td class="items"><img id="edit',
                            '<td class="items"><img id="delete',
                            '<td class="items"><img id="view'],
                        ['" src="img/edit.png" ' +
                            'style="cursor: pointer;"></td>',
                            '" src="img/trash.gif" style="cursor: pointer;"></td>',
                            '" src="img/infor.jpg" style="cursor: pointer; height: 16px; width:16px"></td>']);

                    // event function
                    for (let i = 0; i < table.body.rowsTable.length; ++i) {
                        let deleteI = table.body.rowsTable[i];
                        document.getElementById('view' + i).onclick = function () {
                            request('POST',
                                {'jwt': window.localStorage.getItem('jwt'),
                                'id': deleteI.idRequest},
                                '/admin/class/getresult',
                                function (response) {
                                console.log(response['ok'] === 'true');
                                    if (response['ok'] === 'true') {

                                        // title
                                        let data = response['data'];
                                        document.getElementById('className').innerText = data['class']['nameSubject'];
                                        document.getElementById('idClass').innerText = data['class']['idClass'];
                                        document.getElementById('teacherName').innerText = data['class']['teacher'];
                                        document.getElementById('totalDoneStudents').innerText = 'Số sinh viên tham gia đánh giá: '+
                                            data['class']['numberStudentDone'];

                                        setTimeout(function () {
                                            document.getElementById('responseMessage').style.display = 'none';
                                        }, 2000);
                                        console.log('success');
                                        document.getElementById('tabInfor').style.display = 'block';

                                        let newTabel = new NewTable(document.getElementById('tableParent'),
                                            'table table-hover',
                                            'title', 'sub',
                                        );
                                        newTabel.generate(convertToTableData(response['data']))
                                    }
                                }.bind(this))
                        };

                        document.getElementById('delete' + i).onclick = function () {
                            let showMess = new showMessage();
                            showMess.ShowMessageWaiting();
                            request('POST',
                                {'jwt': window.localStorage.getItem('jwt'),
                                    'id': deleteI.idRequest},
                                '/admin/class/delete',
                                function (response) {
                                    console.log(response['ok'] === true);
                                    if (response['ok'] === true) {
                                        table.deleteRow(deleteI.index);
                                        setTimeout(function () {
                                            document.getElementById('responseMessage').style.display = 'none';
                                        }, 2000);
                                        console.log('success');
                                    }
                                }.bind(this))
                        }
                    }
                } else {

                }

            } else {

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
                    'title', 'sub', ['Delete'], 'idStudent');
                console.log(response['data']);
                table.generate(response['data'],
                    ['<td class="items"><img id="delete'],
                    ['" src="img/trash.gif" ' +
                    'style="cursor: pointer;"></td>']);
                for (let i = 0; i < table.body.rowsTable.length; ++i) {
                    let d = table.body.rowsTable[i]
                    document.getElementById('delete'+i).onclick = function () {
                        console.log('on onclick');
                        conformDeleForm.setVisible(true);
                        conformDeleForm.setOnClickYes(function () {
                            request('POST',
                                {
                                    'jwt': window.localStorage.getItem('jwt'),
                                    'id': d.idRequest
                                },
                                '/admin/student/delete', function (response) {
                                    let showMess = new showMessage();
                                    if (response['ok'] === true) {
                                        showMess.show('Success', showMessageObj.MessageType.Success);

                                        console.log('delete row');
                                        document.getElementById('rowBody' + i).outerHTML = '';

                                    } else {
                                        showMess.show('Error', showMessageObj.MessageType.Error);
                                    }
                                });
                            conformDeleForm.setVisible(false);
                        }.bind(this));
                        conformDeleForm.setOnClickNo(function () {
                            conformDeleForm.setVisible(false);
                        });
                        //
                    }
                }
            } else {
                showMessageObj.show('Error', showMessageObj.MessageType.Error);
            }
        })
};
var getCriterias = function() {
    let dataPost = {'jwt': window.localStorage.getItem('jwt')};
    request('POST', dataPost,
        '/admin/criterias/getall', function (response) {
            let showMessageObj = new showMessage();
            if (response['ok'] === 'true') {
                mainTable.innerHTML = '';
                let table = new Table(mainTable, 'table table-hover',
                    'title', 'sub', ['Delete'], 'id');
                console.log(response['data']);
                table.generate(response['data'],
                    ['<td class="items"><img id="delete'],
                    ['" src="img/trash.gif" ' +
                    'style="cursor: pointer;"></td>']);
                for (let i = 0; i < table.body.rowsTable.length; ++i) {
                    table.body.rowsTable[i].onclick = function () {
                        console.log('on onclick');
                        conformDeleForm.setVisible(true);
                        conformDeleForm.setOnClickYes(function () {
                            request('POST',
                                {
                                    'jwt': window.localStorage.getItem('jwt'),
                                    'id': this.idRequest
                                },
                                '/admin/criteria/delete', function (response) {
                                    let showMess = new showMessage();
                                    if (response['ok'] === true) {
                                        showMess.show('Success', showMessageObj.MessageType.Success);

                                        console.log('delete row');
                                        document.getElementById('rowBody' + i).outerHTML = '';

                                    } else {
                                        showMess.show('Error', showMessageObj.MessageType.Error);
                                    }
                                });
                            conformDeleForm.setVisible(false);
                        }.bind(this));
                        conformDeleForm.setOnClickNo(function () {
                            conformDeleForm.setVisible(false);
                        });
                        //
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
    hideNotNeed();
    let dataPost = {'jwt': window.localStorage.getItem('jwt')};
    request('POST', dataPost,
        '/admin/teachers/getall', function (response) {
            let showMessageObj = new showMessage();
            if (response['ok'] === 'true') {
                mainTable.innerHTML = '';
                let table = new Table(mainTable, 'table table-hover',
                    'title', 'sub', ['Delete'], 'idTeacher');

                table.generate(response['data'],
                    ['<td class="items"><img id="delete'],
                    ['" src="img/trash.gif" ' +
                    'style="cursor: pointer;"></td>']);
                for (let i = 0; i < table.body.rowsTable.length; ++i) {
                    let deleteI = table.body.rowsTable[i];

                    deleteI.onclick = function () {
                        conformDeleForm.setVisible(true);
                        conformDeleForm.setOnClickYes(function () {
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
                                        responseMess.ShowMessageSuccess();
                                        setTimeout(function () {
                                            document.getElementById('responseMessage').style.display = 'none';
                                        }, 2000);
                                        console.log('success');
                                    } else {
                                        responseMess.ShowMessageError();
                                    }
                                }.bind(this));
                            conformDeleForm.setVisible(false);
                        }.bind(this));
                        conformDeleForm.setOnClickNo(function () {
                            conformDeleForm.setVisible(false);
                        });
                    }
                }
            } else {
                showMessageObj.ShowMessageError();
            }
        })
};
document.getElementById('getClasses').onclick = function () {
    hideNotNeed();
    getAllClasses();
};

document.getElementById('getStudents').onclick = function () {
    hideNotNeed();
    getStudents();
};
document.getElementById('getCriterias').onclick = function () {
    hideNotNeed();
    getCriterias();
};

let windowOverLay = document.getElementById('addNewForm');
// windowOverLay.onclick = function() {
//     console.log("click");
//     this.style.display = 'none';
// };

let addNews = document.getElementsByClassName('addNew');
for (let i = 0; i < addNews.length; ++i) {
    addNews[i].onclick = function () {
        hideNotNeed();
        windowOverLay.style.display = "flex";
        document.getElementById('fileUpload').value = '';

        document.getElementById('closeCard').onclick = function () {
            document.getElementById('addNewForm').style.display = 'none';

        };
        document.getElementById('submit').onclick = function () {
            // this.disabled = true;
            document.getElementById('addNewForm').style.display = 'none';
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
                            switch (addNews[i].id) {
                                case 'teachers':
                                    getAllTeacher();
                                    break;
                                case 'students':
                                    getStudents();
                                    break;
                                case 'class':
                                    getAllClasses();
                                    break;
                            }
                        }
                        // window.localStorage.setItem('jwt', responseObj['jwt']);
                        //
                        // window.location.replace("../admin.html");
                    } else {
                        showMessageObj.show('Error', showMessageObj.MessageType.Error);
                    }
                } else {
                    showMessageObj.ShowMessageWaiting();
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


/**
 *
 *  hide tab show info of a class
 */
var infor = document.getElementById("tabInfor");
window.onclick = function(event) {
    hideNotNeed();
    if(event.target === infor) {
        infor.style.display = "none";
    }
};

