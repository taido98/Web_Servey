var request = function (method, data, route, callBack) {
    let xmlhttp = getXmlHttpObject();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState === 4) {

            console.log(this.status);
            console.log(this.responseText);
            if (this.status === 200) {
                let responseObj = JSON.parse(this.responseText);
                if(callBack) {
                    callBack(responseObj);
                }
                // if(responseObj['ok'] === "true") {
                //     getAllClasses();
                //
                // } else {
                //     window.location.replace(responseObj['route']);
                // }
            }
        }

    };
    switch (method) {
        case 'GET':
            throw new Error("NotImplement");
            break;
        case 'POST':
            let formData = new FormData();
            for (let key in data) {
                if(data.hasOwnProperty(key)) {
                    formData.append(key, data[key]);
                }

            }
            xmlhttp.open("POST", route, true);
            xmlhttp.send(formData);
            break;
    }
};