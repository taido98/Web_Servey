var WindowOverlap = function () {
    this.appendToBody = function() {
        let div = document.createElement('div');
        div.className = 'window-overlay';
        document.appendChild(div);
    }
};