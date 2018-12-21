var NewTable = function (parent, classTableName, classTHeadName, classTBodyName) {
    this.parent = parent;
    this.table = document.createElement("TABLE");
    this.classTableName = classTableName;
    this.classTHeadName = classTHeadName;
    this.classTBodyName = classTBodyName;
    this.lablesTitle = [];
    this.body = null;
    this.generate = function (data) {
        this.parent.innerHTML = '';
        if (this.classTableName) {
            this.table.className = this.classTableName;
        }
        // init header
        let header = this.table.createTHead();
        if (this.classTHeadName) {
            header.className = this.classTHeadName;
        }
        let row = header.insertRow(0);
        this.lablesTitle.push('STT');
        for (let key in data[0]) {
            if (data[0].hasOwnProperty(key)) {
                this.lablesTitle.push(key);
            }
        }

        for (let i = 0; i < this.lablesTitle.length; ++i) {
            row.insertCell(i).outerHTML = '<th scope=\"col\">' + this.lablesTitle[i] + '</th>';
        }

        // init body
        this.body = this.table.createTBody();
        this.body.rowsTable = [];
        if (this.classTBodyName) {
            this.body.className = this.classTBodyName;
        }

        for (let i = 0; i < data.length; ++i) {


            let rowBody = this.body.insertRow(i);
            rowBody.index = i;
            this.body.rowsTable.push(rowBody);
            rowBody.id = 'rowBody'+i;

            let j = 0;
            rowBody.insertCell(0).outerHTML = '<th scope=\"row\">' + (i + 1) + '</th>';

            for (let key in data[i]) {
                if (data[i].hasOwnProperty(key)) {
                    rowBody.insertCell(j + 1).outerHTML = '<td>' + data[i][key] + '</td>';
                    ++j;
                }
            }
        }
        this.parent.appendChild(this.table);
    };
    this.createElement = function (tagName, className, href, id) {
        let tag = document.createElement(tagName);
        if(className) {
            tag.className = className;
        }
        if(href) {
            tag.href = href;
        }
        if(id) {
            tag.id = id;
        }

        return tag;
    };
};