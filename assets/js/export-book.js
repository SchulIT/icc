import { Modal } from 'bootstrap.native';
import { jsPDF } from "jspdf";
import 'jspdf-autotable';
const axios = require('axios');
const crypto = require('easy-web-crypto');

let regularFont = null;
let boldFont = null;
let fontInitialized = false;
let decryptedKey = null;

function setFont(pdf, weight) {
    if(regularFont === null || boldFont === null) {
        pdf.setFont('Helvetica', weight);
        return;
    }

    pdf.setFont('Custom', weight);
}

function getFontName() {
    if(regularFont === null || boldFont === null) {
        return 'helvetica';
    }

    return 'Custom';
}

async function initializeFont(regularUrl, boldUrl) {
    if(fontInitialized) {
        return;
    }

    if(regularFont !== null && boldFont !== null) {
        return;
    }

    try {
        regularFont = (await axios.get(regularUrl)).data;
        boldFont = (await axios.get(boldUrl)).data;
    } catch (e) {
        console.error('Keine eigene Schrift hinterlegt, nutze Helventica (nur ASCII)');
        regularFont = null;
        boldFont = null;
    } finally {
        fontInitialized = true;
    }
}

function addFont(pdf) {
    if(regularFont === null || boldFont === null) {
        return;
    }

    pdf.addFileToVFS('regular.ttf', regularFont);
    pdf.addFileToVFS('bold.ttf', boldFont);
    pdf.addFont('regular.ttf', 'Custom', 'normal');
    pdf.addFont('bold.ttf', 'Custom', 'bold');
}

document.addEventListener('DOMContentLoaded', function(event) {
    let modalEl = document.querySelector('.modal#export-modal');
    let modal = new Modal(modalEl, {
        backdrop: 'static',
        keyboard: false
    });

    const weekdays = [ 'Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];

    async function createPdf(response) {
        let pdf = new jsPDF();
        addFont(pdf);
        setFont(pdf, 'normal');

        addTitlePage(pdf, response.section, response.tuition, response.grades);

        // Schülerübersicht
        let summary = [];
        for (let idx = 0; idx < response.students_summary.length; idx++) {
            let student = response.students_summary[idx];
            summary.push([
                student.student.lastname,
                student.student.firstname,
                student.absent_lessons_count,
                student.not_excused_absent_lessons_count + student.excuse_status_not_set_lessons_count,
                student.late_minutes_count
            ]);
        }

        pdf.addPage();

        pdf.text('Übersicht', 15, 25);
        pdf.autoTable({
            startY: 30,
            theme: 'grid',
            margin: {
                top: 25,
                bottom: 25
            },
            head: [
                ['Nachname', 'Vorname', 'FS (insg.)', 'FS (ue.)', 'Versp. (min)']
            ],
            body: summary,
            styles: {
                font: getFontName()
            }
        })

        // Notenübersicht
        if (response.students_grades !== null && decryptedKey !== null) {
            for (const students_grades of response.students_grades) {
                if(students_grades.categories === undefined || students_grades.categories.length === 0) {
                    continue;
                }

                pdf.addPage('a4', 'landscape');

                let matrix = {};
                for (let grade of students_grades.grades) {
                    if (matrix[grade.student] === undefined) {
                        matrix[grade.student] = {};
                    }

                    if (grade.encrypted_grade !== null && grade.encrypted_grade !== '') {
                        matrix[grade.student][grade.grade_category] = await crypto.decrypt(decryptedKey, JSON.parse(grade.encrypted_grade));
                    } else {
                        matrix[grade.student][grade.grade_category] = ' ';
                    }
                }

                let scores = [];

                for (let student of response.students_summary.map(x => x.student)) {
                    if (student === null || matrix[student.id] === undefined) {
                        continue;
                    }

                    let data = [];
                    data.push(student.lastname);
                    data.push(student.firstname);

                    let grades = matrix[student.id];

                    students_grades.categories.forEach(function (category) {
                        if (grades[category.uuid] === undefined) {
                            data.push(' ');
                        } else {
                            data.push(grades[category.uuid]);
                        }
                    });

                    scores.push(data);
                }

                let header = ['Nachname', 'Vorname'];
                students_grades.categories.forEach(function (category) {
                    header.push(category.display_name);
                });

                if(response.students_grades.length > 1) {
                    pdf.text('Notenübersicht (' + students_grades.tuition.name + ')', 15, 25);
                } else {
                    pdf.text('Notenübersicht', 15, 25);
                }
                pdf.autoTable({
                    startY: 30,
                    theme: 'grid',
                    margin: {
                        top: 25,
                        bottom: 25
                    },
                    head: [header],
                    body: scores,
                    styles: {
                        font: getFontName(),
                        fontSize: 8
                    }
                });

                if(response.students_grades.length === 1) {
                    pdf.autoTable({
                        theme: 'plain',
                        body: [
                            [' '],
                            [' '],
                            ['_________________________________'],
                            ['Datum, Unterschrift Fachlehrkraft']
                        ],
                        styles: {
                            font: getFontName(),
                            fontSize: 8
                        }
                    });
                }
            }
        }

        pdf.text('Unterschrift der Fachlehrkraft', 0, 0);

        // Stundenübersicht
        pdf.addPage('a4', 'landscape');

        if (response.tuition !== null) {
            addLessonsForTuition(pdf, response);
        } else {
            addLessonsForGrades(pdf, response);
        }

        addHeader(pdf, response.section, response.tuition, response.grades);
        addFooter(pdf);

        return pdf;
    }

    function formatLessons(start, end) {
        if(start === end) {
            return start + '.';
        }

        if((end - start) === 1) {
            return start + './' + end + '.';
        }

        return start + '.-' + end + '.';
    }

    function formatDate(isoDate, extended) {
        let date = new Date(isoDate);
        let output = date.getDate() + '.' + (date.getMonth()+1) + '.' + date.getFullYear();

        if(extended === true) {
            output = weekdays[date.getDay()] + ', ' + output;
        }

        return output;
    }

    function formatAttendances(attendances) {
        let comments = [ ];
        attendances.filter(x => x.type === 'late').forEach(function(attendance) {
            comments.push(
                attendance.student.firstname + ' ' + attendance.student.lastname + ' verspätet (' + attendance.late_minutes_count + ' min' + (attendance.comment !== null ? ', ' + attendance.comment.trim() : '') + ')'
            );
        });

        attendances.filter(x => x.type === 'absent').forEach(function(attendance) {
            comments.push(
                attendance.student.firstname + ' ' + attendance.student.lastname + ' absent (' + attendance.absent_lesson_count + ' FS, ' + (attendance.is_excused ? 'E' : 'UE') + (attendance.comment !== null ? ', ' + attendance.comment.trim() : '') + ')'
            )
        });

        return comments;
    }

    function addComments(body, comments, colspan) {
        comments.forEach(function(comment) {
            body.push([
                {
                    content: [
                        'Lehrkraft: ' + comment.teacher.acronym,
                        'Kind(er): ' + comment.students.map(x => x.firstname + " " + x.lastname).join(', '),
                        comment.comment
                        ].join('\n'),
                    colSpan: colspan
                }
            ]);
        });

        return body;
    }

    function addLessonsForGrades(pdf, data) {
        let body = [ ];
        let header = [[
            {
                content: 'Std',
                styles: {
                    valign: 'middle'
                }
            },
            {
                content: 'Fach',
                styles: {
                    valign: 'middle'
                }
            },
            {
                content: 'Thema der Stunde',
                styles: {
                    valign: 'middle'
                }
            },
            {
                content: 'Aufgabe',
                styles: {
                    valign: 'middle'
                }
            },
            {
                content: 'Absenzen/ Bemerkungen',
                styles: {
                    valign: 'middle',
                    cellWidth: 90
                }
            },
            {
                content: 'Lehrkraft',
                styles: {
                    valign: 'middle'
                }
            }
        ]];

        data.weeks.forEach(function(week) {
            week.days.forEach(function(day) {
                // Datumsüberschrift
                body.push([{
                    content: formatDate(day.date, true),
                    colSpan: 6,
                    styles: {
                        fontStyle: 'bold'
                    }
                }]);

                body = addComments(body, day.comments, 6);

                day.lessons.forEach(function(lesson) {
                    let row = [
                        [ formatLessons(lesson.start, lesson.end) ],
                        [
                            lesson.subject + (lesson.replacement_subject !== null ? ' → ' + lesson.replacement_subject : '')
                        ]
                    ];

                    let remarksAndAttendances = formatAttendances(lesson.attendances);

                    if(lesson.was_cancelled) {
                        lesson.topic = '[Entfall] ' + lesson.topic;
                    }
                    row.push([ lesson.topic ]);
                    row.push([ lesson.exercises ]);
                    row.push([ remarksAndAttendances.join('\n') ]);

                    if(lesson.teacher !== null) {
                        row.push([
                            lesson.teacher.acronym + (lesson.replacement_teacher !== null ? ' → ' + lesson.replacement_teacher.acronym : '')
                        ])
                    }

                    body.push(row);
                });
            });
        });

        pdf.autoTable({
            margin: {
                top: 25,
                bottom: 25
            },
            theme: 'grid',
            head: header,
            body: body,
            styles: {
                font: getFontName(),
                fontSize: 8
            }
        });
    }

    function addLessonsForTuition(pdf, data) {
        let body = [];

        let header = [ [
            {
                content: 'Datum',
                styles: {
                    valign: 'middle'
                }
            },
            {
                content: 'Std',
                styles: {
                    valign: 'middle'
                }
            },
            {
                content: 'Thema der Stunde',
                styles: {
                    valign: 'middle'
                }
            },
            {
                content: 'Aufgabe',
                styles: {
                    valign: 'middle'
                }
            },
            {
                content: 'Absenzen/ Bemerkungen',
                styles: {
                    valign: 'middle',
                    cellWidth: 90
                }
            },
        ]];

        data.weeks.forEach(function(week) {
            // KW-Überschrift
            body.push([{
                content: 'KW ' + week.week_number,
                colSpan: 4,
                styles: {
                    fontStyle: 'bold'
                }
            }]);

            week.days.forEach(function(day) {
                body = addComments(body, day.comments, 4);

                day.lessons.forEach(function(lesson) {
                    let row = [
                        [ formatDate(day.date) ],
                        [ formatLessons(lesson.start, lesson.end) ]
                    ];

                    let remarksAndAttendances = formatAttendances(lesson.attendances);

                    if(lesson.was_cancelled) {
                        lesson.topic = '[Entfall] ' + lesson.topic;
                    }
                    row.push([ lesson.topic ]);
                    row.push([ lesson.exercises ]);
                    row.push([ remarksAndAttendances.join('\n') ]);

                    body.push(row);
                });
            });
        });

        pdf.autoTable({
            margin: {
                top: 25,
                bottom: 25
            },
            theme: 'grid',
            head: header,
            body: body,
            styles: {
                font: getFontName(),
                fontSize: 8
            }
        });
    }

    function addTitlePage(pdf, section, tuition, grades) {
        let originalFontSize = pdf.getFontSize();
        // Titelseite
        setFont(pdf, 'bold');
        pdf.setFontSize(20);
        pdf.text("Unterrichtsbuch", 15, 25);
        pdf.setFontSize(originalFontSize);

        let text = [ ];
        text.push('Abschnitt: ' + section.name);
        text.push('Klasse: ' + grades.map(x => x.name).join(', '));
        if(tuition !== null) {
            text.push('Kurs: ' + tuition.name);
            text.push('Lehrkraft: ' + tuition.teachers.map(x => x.firstname + ' ' + x.lastname).join(', '));
        } else if(grades !== null && grades !== undefined) {
            let teachers = [ ];
            grades.forEach(function(grade) {
                teachers = teachers.concat(grade.teachers);
            })

            text.push('Klassenleitung: ' + teachers.map(x => x.firstname + ' ' + x.lastname).join(', '));
        }
        pdf.text(text, 15, 40);

        // Schriftgröße zurücksetzen
        pdf.setFontSize(originalFontSize);
        setFont(pdf, 'normal');
    }

    function addHeader(pdf, section, tuition, grades) {
        let count = pdf.internal.getNumberOfPages();
        pdf.setFontSize(8);

        let header = grades.map(x => x.name).join(', ');

        if(tuition !== null) {
            header = tuition.name + ' - ' + header + ' - ' + tuition.teachers.map(x => x.acronym).join(', ');
        } else {
            let teachers = [ ];
            grades.forEach(function(grade) {
                teachers = teachers.concat(grade.teachers);
            });

            header = header + ' - ' + teachers.map(x => x.acronym).join(', ')
        }

        for(let pageNumber = 1; pageNumber <= count; pageNumber++) {
            pdf.setPage(pageNumber);
            pdf.text(header, 15, 15);
            pdf.text(section.name, pdf.internal.pageSize.width - 15, 15, {
                align: 'right'
            });
        }
    }

    function addFooter(pdf) {
        let count = pdf.internal.getNumberOfPages();

        pdf.setFontSize(8);
        for(let pageNumber = 1; pageNumber <= count; pageNumber++) {
            pdf.setPage(pageNumber);
            pdf.text('- Seite ' + pageNumber + ' von ' + count + ' -', pdf.internal.pageSize.width / 2, pdf.internal.pageSize.height - 15, {
                align: 'center'
            });
        }
    }

    if(document.getElementById('password_btn') !== null) {
        document.getElementById('password_btn').addEventListener('click', async function(event) {
            event.preventDefault();

            let password = document.querySelector(this.getAttribute('data-passphrase')).value;
            let encryptedKey = JSON.parse(document.querySelector(this.getAttribute('data-key')).innerHTML.trim());

            try {
                decryptedKey = await crypto.decryptMasterKey(password, encryptedKey);
                document.querySelector(this.getAttribute('data-passphrase')).value = '';

                this.closest('.card-body').querySelector('.bs-callout').classList.remove('hide');
                this.closest('.input-group').remove();
            } catch (e) {
                alert('Falsches Passwort');
                console.error(e);
            }
        });
    }

    document.querySelectorAll('[data-export-url]').forEach(function(element) {
        element.addEventListener('click', async function(event) {
            event.preventDefault();
            modal.show();

            let $el = this;

            let response = await axios.get($el.getAttribute('data-export-url'));
            await initializeFont($el.getAttribute('data-regular-font-url'), $el.getAttribute('data-bold-font-url'));
            let pdf = await createPdf(response.data);

            let parts = [ ];
            parts.push(response.data.section.year);
            parts.push(response.data.section.number);

            for(let grade of response.data.grades) {
                parts.push(grade.name);
            }

            if(response.data.tuition !== null) {
                parts.push(response.data.tuition.name);

                for(let teacher of response.data.tuition.teachers) {
                    parts.push(teacher.acronym);
                }
            } else if(response.data.grades.length === 1) {
                for(let teacher of response.data.grades[0].teachers) {
                    parts.push(teacher.acronym);
                }
            }

            let filename = parts.join('-') + ".pdf";

            pdf.save(filename);

            modal.hide();
        });
    });

});