var FormOverlap = function (formHTML, yesBtn, noBtn) {
    this.formhtml = formHTML;
    this.yesBtn = yesBtn;
    this.noBtn = noBtn;
    this.setVisible = function (value) {
        if(value === true) {
            this.formhtml.style.display = "block";
        } else {
            this.formhtml.style.display = "none";
        }
    };
    this.setOnClickYes = function (callBack) {
        this.yesBtn.onclick = callBack;
    };
    this.setOnClickNo = function (callBack) {
        this.noBtn.onclick = callBack;

    }
};