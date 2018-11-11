this.disabled = true;
let xmlhttp = getXmlHttpObject();
xmlhttp.onreadystatechange = function () {
    if (this.readyState === 4) {

        console.log("student page");
        console.log(this.status);
        console.log(this.responseText);
        if (this.status === 200) {
            let responseObj = JSON.parse(this.responseText);
            // console.log(this.responseText);
        } else {

        }


    }

};

xmlhttp.open("GET", "../student.php?jwt=" +window.localStorage.getItem('jwt')
    +"&username=16020030", true);
xmlhttp.send(null);
