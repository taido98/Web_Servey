


let submitBtn = document.getElementById('submitBtn');
submitBtn.onclick = function () {
    this.disabled = true;
    let xmlhttp = getXmlHttpObject();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState === 4) {

            console.log(this.status);
            console.log(this.responseText);
            if (this.status === 200) {
                // let responseObj = JSON.parse(this.responseText);
                // window.localStorage.setItem('jwt', responseObj['jwt']);
                //
                // window.location.replace("../admin.html");
            } else {

            }


        }

    };
    let file = document.getElementById('fileUpload').files[0];
    if(file) {
        let data = new FormData();
        data.append('jwt', window.localStorage.getItem('jwt'));
        data.append('file', file);
        xmlhttp.open("POST", "/admin/students/add", true);
        xmlhttp.send(data);
    }

};



let submitBtnTeacher = document.getElementById('submitBtnTeacher');
submitBtnTeacher.onclick = function () {
    this.disabled = true;
    let xmlhttp = getXmlHttpObject();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState === 4) {

            console.log(this.status);
            console.log(this.responseText);
            if (this.status === 200) {
                // let responseObj = JSON.parse(this.responseText);
                // window.localStorage.setItem('jwt', responseObj['jwt']);
                //
                // window.location.replace("../admin.html");
            } else {

            }


        }

    };
    let file = document.getElementById('fileUploadTeacher').files[0];
    if(file) {
        let data = new FormData();
        data.append('jwt', window.localStorage.getItem('jwt'));
        data.append('file', file);
        xmlhttp.open("POST", "/admin/teachers/add", true);
        xmlhttp.send(data);
    }

};

let submitBtnClass = document.getElementById('submitBtnClass');
submitBtnClass.onclick = function () {
    this.disabled = true;
    let xmlhttp = getXmlHttpObject();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState === 4) {

            console.log(this.status);
            console.log(this.responseText);
            if (this.status === 200) {
                // let responseObj = JSON.parse(this.responseText);
                // window.localStorage.setItem('jwt', responseObj['jwt']);
                //
                // window.location.replace("../admin.html");
            } else {

            }


        }

    };
    let file = document.getElementById('fileUploadClass').files[0];
    if(file) {
        let data = new FormData();
        data.append('jwt', window.localStorage.getItem('jwt'));
        data.append('file', file);
        xmlhttp.open("POST", "/admin/class/add", true);
        xmlhttp.send(data);
    }

};