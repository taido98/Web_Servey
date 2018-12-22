function getXmlHttpObject() {
    var xmlHttp = null;
    try {
        xmlHttp = new XMLHttpRequest();
    } catch (e) {
        try  {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                return null;
            }
        }
    }
    return xmlHttp;
}

function convertToTableData(data) {
    let aClass = data['class'];
    let appendix = data['appendix'];
    let statistic = aClass['statistic'];
    var data = [];
    for(let key in statistic) {
        if(statistic.hasOwnProperty(key)) {

            let row = statistic[key];
            var o = {};
            o['Name'] = appendix[key];
            for(let v in row) {
                if(row.hasOwnProperty(v)) {
                    o[v] = row[v];
                }

            }

            data.push(o);
        }
    }
    return data;
}
var createElement = function (tagName, className, href, id) {
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