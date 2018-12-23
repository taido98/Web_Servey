// update profile behavior
var updateProfile = null;
document.getElementById('updateProfile').onclick = function () {
    updateProfile = StudentUpdateProfile;
    updateProfile.showPopup();
    // updateProfile.update();
};

// list class object
var listClass = document.getElementById('demo');
var mainContent = document.getElementById('mainContent');
var profile = null;
var classes = null;
var appendix = null;
var Row = function (idDB, ratioButton, defaultValue) {
    this.idDB = idDB;
    var that = this;
    this.value = defaultValue;
    this.ratioButtons = ratioButton;
    if (this.value >= 1) {
        this.ratioButtons[this.value - 1].checked = true;

    }
    for (let i = 0; i < this.ratioButtons.length; ++i) {
        this.ratioButtons[i].idValue = i + 1;
    }
    for (let i = 0; i < this.ratioButtons.length; ++i) {
        this.ratioButtons[i].onchange = function () {
            reset();
            this.checked = true;
            that.value = this.idValue;
        }
    }
    var reset = function () {
        for (let i = 0; i < that.ratioButtons.length; ++i) {
            that.ratioButtons[i].checked = false;
        }
    };
    this.getValue = function () {
        return this.value;
    };

};

/**
 *
 * @param parent
 * @param contentData
 * @param appendix
 * @param className
 * @constructor
 */
var SurveyForm = function (parent, contentData, appendix, className, classId) {
    this.parent = parent;
    this.contentData = contentData;
    this.appendix = appendix;
    this.className = className;
    this.classId = classId;
    this.titile = null;
    this.backButton = null;
    this.submitButton = null;
    this.body = null;
    this.table = document.createElement("TABLE");
    this.rows = [];
    var that = this;
    this.generate = function () {
        createTitle();
        createBody();
        createBackButton();
        createSubmitBtn();
        this.submitButton.onclick = function () {

            try {
                let formData = JSON.stringify(that.getData());
                console.log(formData);
                let data = {
                    'jwt': window.localStorage.getItem('jwt'),
                    'idClass': that.classId,
                    'survey_form': formData
                };
                request('POST', data, '/student/submit_survey_form', function (response) {
                    let mess = new Message('message');
                    if(response['ok'] === 'true') {
                        console.log('success');
                        mess.ShowMessageSuccess();
                    } else {
                        mess.ShowMessageError();
                    }
                });
            } catch (e) {
                let mess = new Message('message');
                mess.ShowMessageError();
            }

        };
    };
    var createBackButton = function () {

        that.backButton = createElement('button', 'btn');
        that.backButton.type = 'button';
        that.backButton.style = 'float: left;\n' +
            'padding-left: 30px;\n' +
            'padding-right: 35px;\n' +
            'padding-top: 10px;\n' +
            'padding-bottom: 10px;\n' +
            'background-color: #00FF33;';
        that.backButton.innerHTML = '<i class="fa fa-fw fa-angle-double-left"></i> Back';
        that.parent.appendChild(that.backButton);
        that.backButton.onclick = function () {
            console.log('onclick back button');
        }
    };
    var createSubmitBtn = function () {
        that.submitButton = createElement('button', 'btn btn-success');
        that.submitButton.type = 'button';
        that.submitButton.innerHTML = 'Lưu lại ý kiến đánh giá';
        that.submitButton.style = 'color: #fff;\n' +
            'background-color: #5cb85c;\n' +
            'border-color: #4cae4c;' +
            '    display: inline-block;\n' +
            '    padding: 6px 12px;\n' +
            '    margin-left: 100px;\n' +
            '    margin-bottom: 0;\n' +
            '    font-size: 14px;\n' +
            '    font-weight: 400;\n' +
            '    line-height: 1.42857143;\n' +
            '    text-align: center;\n' +
            '    white-space: nowrap;\n' +
            '    vertical-align: middle;\n' +
            '    -ms-touch-action: manipulation;\n' +
            '    touch-action: manipulation;\n' +
            '    cursor: pointer;\n' +
            '    -webkit-user-select: none;\n' +
            '    -moz-user-select: none;\n' +
            '    -ms-user-select: none;\n' +
            '    user-select: none;\n' +
            '    background-image: none;\n' +
            '    border: 1px solid transparent;\n' +
            '        border-top-color: transparent;\n' +
            '        border-right-color: transparent;\n' +
            '        border-bottom-color: transparent;\n' +
            '        border-left-color: transparent;\n' +
            '    border-radius: 4px;\n' +
            'position: center;';

        that.parent.appendChild(that.submitButton);
        that.submitButton.onclick = function () {
            console.log('onclick submit button');
        }

    };
    var createTitle = function () {
        that.titile = createElement('p', 'name_subject');
        that.titile.innerText = that.className;
        that.titile.style = 'font-size: 30px;font-weight: bold;text-align: center;'
        that.parent.appendChild(that.titile);
    };
    var createBody = function () {


        // insert header
        let header = that.table.createTHead();
        let row = header.insertRow(0);
        row.insertCell(0).outerHTML = '<th class="number col-sm-1">#</th>';
        row.insertCell(1).outerHTML = '<th class="number col-sm-1">Name</th>';
        row.insertCell(2).outerHTML = '<th class="number col-sm-1">1</th>';
        row.insertCell(3).outerHTML = '<th class="number col-sm-1">2</th>';
        row.insertCell(4).outerHTML = '<th class="number col-sm-1">3</th>';
        row.insertCell(5).outerHTML = '<th class="number col-sm-1">4</th>';
        row.insertCell(6).outerHTML = '<th class="number col-sm-1">5</th>';

        // insert body;

        that.body = that.table.createTBody();
        let i = 0;
        for (let v in that.contentData) {
            if (that.contentData.hasOwnProperty(v)) {
                let row = that.body.insertRow(i);
                row.insertCell(0).outerHTML = '<th>' + i + '</th>'
                row.insertCell(1).outerHTML = '<th class="content_servey"><p class="" style="align-content: center">' + appendix[v] + '</p></th>';
                for (let j = 1; j <= 5; ++j) {
                    let cell = row.insertCell(j + 1);
                    cell.outerHTML = '<th class=""><label><input id="radio' + i + (j - 1) + '" type="radio"></label></th>';
                }
                ++i;
            }
        }
        that.parent.appendChild(that.table);
        i = 0;
        for (let v in that.contentData) {
            if (that.contentData.hasOwnProperty(v)) {
                let ratios = [];
                for (let j = 0; j < 5; ++j) {
                    ratios.push(document.getElementById('radio' + i + j));
                }
                ++i;
                let row = new Row(v, ratios, that.contentData[v]);
                that.rows.push(row);
            }
        }
    };
    this.getData = function () {
        let survey_form = {};
        let l = this.rows.length;
        for (let i = 0; i < l; ++i) {
            let ret = this.rows[i].getValue();
            if (ret >= 1) {
                survey_form[this.rows[i].idDB] = ret;
            } else {
                throw new Error('');
            }
        }
        return survey_form;
    }
};


let dataPost = {'jwt': window.localStorage.getItem('jwt')};
request('POST', dataPost, 'student/getAll', function (response) {
    if (response['ok'] === 'true') {
        profile = response['data']['profile'];
        classes = response['data']['classes'];
        appendix = response['data']['appendix'];


        // init Name
        document.getElementById('nameStudent').innerHTML =
            '<i class="fa fa-user"></i> ' + profile['fullName'] + '<b class="caret"> </b>';

        let classList = new ClassList(listClass, classes);
        classList.generate();
    }
});