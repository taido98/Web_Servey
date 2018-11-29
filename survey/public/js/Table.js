var Table = function (parent, classTableName, classTHeadName, classTBodyName) {
    this.parent = parent;
    this.table = document.createElement("TABLE");
    this.classTableName = classTableName;
    this.classTHeadName = classTHeadName;
    this.classTBodyName = classTBodyName;
    this.lablesTitle = [];
    this.generate = function (data) {
        console.log('generate');
        if (this.classTableName) {
            this.table.className = this.classTableName;
        }
        let header = this.table.createTHead();
        if (this.classTHeadName) {
            header.className = this.classTHeadName;
        }
        let row = header.insertRow(0);
        this.lablesTitle.push('#');
        for (let key in data[0]) {
            if (data[0].hasOwnProperty(key)) {
                this.lablesTitle.push(key);
            }
        }
        for (let i = 0; i < this.lablesTitle.length; ++i) {
            row.insertCell(i).outerHTML = '<th scope=\"col\">' + this.lablesTitle[i] + '</th>';
        }
        let body = this.table.createTBody();

        if(this.classTBodyName) {
            body.className = this.classTBodyName;
        }
        for(let i = 0; i < data.length; ++i) {
            let rowBody = body.insertRow(i);
            let j = 0;
            rowBody.insertCell(0).outerHTML = '<th scope=\"row\">' + (i+1)+'</th>';
            for (let key in data[i]) {
                if (data[i].hasOwnProperty(key)) {
                    rowBody.insertCell(j+1).outerHTML = '<td>' +data[i][key]+'</td>';
                    ++j;
                }

            }
        }
        this.parent.appendChild(this.table);
    }
};