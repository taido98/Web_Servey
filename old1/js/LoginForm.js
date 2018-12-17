let invalidLable = document.getElementById("invalid");
invalidLable.style.display = "none";


let loginBtn = document.getElementById("loginBtn");

loginBtn.onclick = function () {
    this.disabled = true;
    let xmlhttp = getXmlHttpObject();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState === 4) {

            console.log(this.status);
            console.log(this.responseText);
            if (this.status === 200) {
                let responseObj = JSON.parse(this.responseText);
                if(responseObj['ok'] === true) {
                    window.localStorage.setItem('jwt', responseObj['jwt']);
                    window.location.replace(responseObj['route']);
                } else {
                    console.log("failed login");
                    invalidLable.style.display = "block";
                    loginBtn.disabled = false;
                }
            }
        }

    };
    let data = new FormData();
    data.append('username', document.getElementById('username').value);
    data.append('password', document.getElementById('password').value);
    xmlhttp.open("POST", "/login", true);
    xmlhttp.send(data);
};


