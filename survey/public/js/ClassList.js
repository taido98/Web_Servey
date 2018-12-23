/**
 *
 * @param parent
 * @param data
 * @constructor
 */
var ClassList = function (parent, data) {
    this.parent = parent;
    this.data = data;
    this.listElements = [];
    this.generate = function () {
        console.log('generate');
        this.parent.innerHTML = '';
        let length = this.data.length;
        for (let i = 0; i < length; ++i) {
            let li = createElement('li');
            let a = createElement('a', null, '#');
            a.innerHTML = this.data[i]['idClass'];
            li.appendChild(a);
            this.parent.appendChild(li);
            li.idClass = this.data[i]['idClass'];
            li.contentData = this.data[i]['content'];
            li.subjectName = this.data[i]['subjectName'];
            li.numberLesson = this.data[i]['numberLesson'];
            li.location = this.data[i]['location'];
            li.onclick = function () {
                mainContent.innerHTML = '';
                // create survey form
                document.getElementById('startMain').style.display = 'none';
                let surveyForm = new SurveyForm(mainContent, this.contentData,
                    appendix, this.subjectName, this.idClass);
                surveyForm.generate();
            };
            this.listElements.push(li);
        }
    };
    this.generateTeacher = function () {
        console.log('generate');
        this.parent.innerHTML = '';
        let length = this.data.length;
        for (let i = 0; i < length; ++i) {
            let li = createElement('li');
            let a = createElement('a', null, '#');
            a.innerHTML = this.data[i]['idClass'];
            li.appendChild(a);
            this.parent.appendChild(li);
            li.idClass = this.data[i]['idClass'];
            li.content = this.data[i]['content'];
            li.subjectName = this.data[i]['subjectName'];
            li.numberLesson = this.data[i]['numberLesson'];
            li.location = this.data[i]['location'];
            li.onclick = function () {
                mainContent.innerHTML = '';
                // create survey form
                document.getElementById('startMain').style.display = 'none';

                let newTabel = new NewTable(mainContent,
                    'table table-hover',
                    'title', 'sub',
                );
                let classData = mainData['classes'][i];
                let d = {
                    'class': classData,
                    'appendix': appendix,
                };
                console.log(d);
                newTabel.generate(convertToTableData(d))

            };

            this.listElements.push(li);
        }
    }
};