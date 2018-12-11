
var Table = function (parent, classTableName, classTHeadName, classTBodyName, additionColumns, keyColumn) {
    this.parent = parent;
    this.table = document.createElement("TABLE");
    this.classTableName = classTableName;
    this.classTHeadName = classTHeadName;
    this.classTBodyName = classTBodyName;
    this.lablesTitle = [];
    this.additionColumns = additionColumns;
    this.body = null;
    this.keyColumn = keyColumn;
    this.generate = function (data, htmlDataPre, htmlDataEnd, callBack) {
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
        if (this.additionColumns) {
            for (let i = 0; i < this.additionColumns.length; ++i) {
                this.lablesTitle.push(this.additionColumns[i]);

            }
        }

        for (let i = 0; i < this.lablesTitle.length; ++i) {
            row.insertCell(i).outerHTML = '<th scope=\"col\">' + this.lablesTitle[i] + '</th>';
        }
        this.body = this.table.createTBody();
        this.body.rowsTable = [];

        if (this.classTBodyName) {
            this.body.className = this.classTBodyName;
        }
        for (let i = 0; i < data.length; ++i) {


            let rowBody = this.body.insertRow(i);
            rowBody.index = i;
            if(this.keyColumn) {

                rowBody.idRequest = data[i][this.keyColumn];
                console.log('idRequest ' + rowBody['idRequest']);
            }
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
            if(this.additionColumns) {
                if(htmlDataPre && htmlDataEnd) {
                    for(let index = 0; index < this.additionColumns.length; ++index) {
                        rowBody.insertCell(j + 1).outerHTML = htmlDataPre[index] +
                            + i + htmlDataEnd[index];
                        ++j;
                    }
                }
            }
        }
        this.parent.appendChild(this.table);
        // console.log(this.rows);
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
    }
    this.deleteRow = function (i) {
            for(let index = i+1; index < this.body.rowsTable.length; ++index) {
                --this.body.rowsTable[index].index;
            }
        this.body.rowsTable.splice(i, 1);
           this.body.deleteRow(i);
    }
};