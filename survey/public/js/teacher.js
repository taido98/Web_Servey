
// update profile behavior
var updateProfile = null;
document.getElementById('updateProfile').onclick = function () {
    updateProfile = TeacherUpdateProfile;
    updateProfile.showPopup();
    updateProfile.update();
};

// list class object
var listClass = document.getElementById('demo');
var mainContent = document.getElementById('mainContent');
let dataPost = {'jwt': window.localStorage.getItem('jwt')};
var mainData = null;
request('POST', dataPost, 'teacher/getAll', function (response) {
    if (response['ok'] === 'true') {
        mainData= response['data'];
        profile = response['data']['profile'];
        classes = response['data']['classes'];
        appendix = response['data']['appendix'];
        // init Name
        document.getElementById('nameStudent').innerHTML =
            '<i class="fa fa-user"></i> ' + profile['fullName'] + '<b class="caret"> </b>';
        //
        let classList = new ClassList(listClass, classes);
        classList.generateTeacher();
    }
});