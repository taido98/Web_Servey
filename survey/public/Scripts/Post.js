function PostBtn(btnId, dataObject, linkApi) {
    this.btnId = btnId;
    this.dataObject = dataObject;
    this.linkApi = linkApi;
    this.post = function () {
        let deleteStudentBtn = document.getElementById(this.btnId);
        deleteStudentBtn.onclick = function () {
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

            let data = new FormData();
            for(let key in this.dataObject) {
                if(this.dataObject.hasOwnProperty(key)) {
                    data.append(key, this.dataObject[key]);
                }
            }
            console.log(this.linkApi);
            xmlhttp.open("POST", this.linkApi, true);
            xmlhttp.send(data);
        }.bind(this);
    }
}