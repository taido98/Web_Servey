document.getElementById('username').focus();
document.getElementById('username').onkeyup = function (key) {
    if(key.keyCode === 13) {
        document.getElementById('password').focus();

    }
};
document.getElementById('password').onkeyup = function (key) {
    if(key.keyCode === 13) {
        document.getElementById('button_login').click();

    }
};
