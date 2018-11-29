var getAllClasses = function() {
    let xmlhttp = getXmlHttpObject();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState === 4) {

            console.log(this.status);
            console.log(this.responseText);
            if (this.status === 200) {
                let responseObj = JSON.parse(this.responseText);
                if(responseObj['ok'] === "true") {
                    // get main table
                    var mainTable = document.getElementById('col-8');
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

let xmlhttp = getXmlHttpObject();
xmlhttp.onreadystatechange = function () {
    if (this.readyState === 4) {

        console.log(this.status);
        console.log(this.responseText);
        if (this.status === 200) {
            let responseObj = JSON.parse(this.responseText);
            if(responseObj['ok'] === "true") {
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