let loginBtn = document.getElementById("loginBtn");

loginBtn.onclick = function () {
    this.disabled = true;
    let xmlhttp = getXmlHttpObject();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState === 4) {

            console.log(this.status);
            console.log(this.responseText);
            if(this.status === 200) {
                let responseObj = JSON.parse(this.responseText);
                window.localStorage.setItem('jwt', responseObj['jwt']);

                window.location.replace("temp.html");
            } else {

            }


        }

    };
    xmlhttp.open("GET", "../login.php?username=" +document.getElementById('username').value
        +"&password="+document.getElementById('password').value, true);
    xmlhttp.send(null);
};


