var Message = function (idObject) {
    this.MessageType = {'Error': 0, "Success": 2};
    this.show = function (message, messageType) {
        let responseMess = document.getElementById(idObject);
        switch (messageType) {
            case this.MessageType.Success:
                responseMess.innerText = 'Success';
                responseMess.style.display = 'inline';
                responseMess.style.color = 'green';
                break;
            case this.MessageType.Error:

                console.log('error');
                responseMess.innerText = 'Error';
                responseMess.style.display = 'inline';
                responseMess.style.color = 'red';
                break;
        }
        setTimeout(function () {
            responseMess.style.display = 'none';
        }, 2000)
    };
    this.ShowMessageSuccess = function () {
        this.show('Success', this.MessageType.Success);
    };
    this.ShowMessageError = function () {
        this.show('Error', this.MessageType.Error);
    };
    this.ShowMessageWaiting = function () {
        this.show('Waiting', this.MessageType.Success);
    }
};