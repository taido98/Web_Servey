let logout = document.getElementById("logout");
logout.href = '/logout?jwt=' + window.localStorage.getItem('jwt');