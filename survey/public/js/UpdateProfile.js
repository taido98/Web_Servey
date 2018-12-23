var UpdateProfile = function (route) {
    this.profileInfo = null;
    this.route = route;
    this.update = function () {
        console.log('route: ' + this.route);
        if(this.profileInfo) {
            let data = {
                'jwt': window.localStorage.getItem('jwt'),
                'data': this.profileInfo
            };
            console.log('post data: ' + data);
            console.log('route: ' + this.route);
            request('POST', data, this.route, function (response) {
                if(response['ok'] === 'true') {
                    console.log('success');
                }
            })
        } else {
            throw new Error('profileInfo is empty is null please set first');
        }

    };
    this.showPopup = function () {
        // implement show pop up to see;
        console.log('show window to see profile');
    };
    this.setProfileInfo = function(info) {
        this.profileInfo = info;
    }
};

var StudentUpdateProfile = new UpdateProfile('/student/updateProfile');
var TeacherUpdateProfile = new UpdateProfile('/teacher/updateProfile');
