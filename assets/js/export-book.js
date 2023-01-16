import { Modal } from 'bootstrap.native';
import { jsPDF } from "jspdf";
import 'jspdf-autotable';
const axios = require('axios');

document.addEventListener('DOMContentLoaded', function(event) {
    let modalEl = document.querySelector('.modal#export-modal');
    let modal = new Modal(modalEl, {
        backdrop: 'static',
        keyboard: false
    });

    const weekdays = [ 'Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];

    function createPdf(response) {
        return new Promise(resolve => {
            let pdf = new jsPDF();

            addTitlePage(pdf, response.section, response.tuition, response.grades);

            // Schülerübersicht
            let summary = [ ];
            for(let idx = 0; idx < response.students_summary.length; idx++) {
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
                body: summary
            })

            // Notenübersicht
            if(response.tuition !== null) {
                pdf.addPage();

                let scores = [];
                for (let idx = 0; idx < response.students_summary.length; idx++) {
                    let student = response.students_summary[idx];
                    scores.push([
                        student.student.lastname,
                        student.student.firstname,
                        ' ', // SoMi E
                        ' ', // SoMi E
                        ' ', // SoMi G
                        ' ', // Klausur E
                        ' ', // Klausur E
                        ' ', // Klausur E
                        ' ', // Klausur G
                        ' ', // Zensur
                        ' ', // Punkte
                    ]);
                }

                pdf.text('Notenübersicht', 15, 25);
                pdf.autoTable({
                    startY: 30,
                    theme: 'grid',
                    margin: {
                        top: 25,
                        bottom: 25
                    },
                    head: [
                        [
                            '',
                            '',
                            { content: 'Sonstige Mitarbeit', colSpan: 3, styles: { halign: 'center'}},
                            { content: 'Klausuren', colSpan: 4, styles: { halign: 'center'} },
                            { content: 'Zensur', styles: { halign: 'center'}},
                            { content: 'Punkte', styles: { halign: 'center'}}
                        ],
                        [
                            'Nachname',
                            'Vorname',
                            { content: 'E', styles: { halign: 'center' }},
                            { content: 'E', styles: { halign: 'center' }},
                            { content: 'G', styles: { halign: 'center' }},
                            { content: 'E', styles: { halign: 'center' }},
                            { content: 'E', styles: { halign: 'center' }},
                            { content: 'E', styles: { halign: 'center' }},
                            { content: 'G', styles: { halign: 'center' }},
                            '',
                            '' ]
                    ],
                    body: scores
                })
            }

            // Stundenübersicht
            pdf.addPage('a4', 'landscape');

            if(response.tuition !== null) {
                addLessonsForTuition(pdf, response);
            } else {
                addLessonsForGrades(pdf, response);
            }

            addHeader(pdf, response.section, response.tuition, response.grades);
            addFooter(pdf);

            resolve(pdf);
        })
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
                            lesson.subject + (lesson.replacement_subject !== null ? ' >> ' + lesson.replacement_subject : '')
                        ]
                    ];

                    let remarksAndAttendances = formatAttendances(lesson.attendances);

                    if(lesson.was_cancelled) {
                        lesson.topic = '[Entfall] ' + lesson.topic;
                    }
                    row.push([ lesson.topic ]);
                    row.push([ lesson.exercises ]);
                    row.push([ remarksAndAttendances.join(', ') ]);

                    if(lesson.teacher !== null) {
                        row.push([
                            lesson.teacher.acronym + (lesson.replacement_teacher !== null ? ' >> ' + lesson.replacement_teacher.acronym : '')
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
            body: body
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
                    row.push([ remarksAndAttendances.join(', ') ]);

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
            body: body
        });
    }

    function addTitlePage(pdf, section, tuition, grades) {
        let originalFontSize = pdf.getFontSize();
        // Titelseite
        pdf.setFont("Helvetica", "bold");
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
        pdf.setFont("Helvetica", "normal");
    }

    function addHeader(pdf, section, tuition, grades) {
        let count = pdf.internal.getNumberOfPages();
        pdf.setFontSize(8);

        let header = grades.map(x => x.name).join(', ');

        if(tuition !== null) {
            header = tuition.name + ' - ' + header;
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

    document.querySelectorAll('[data-export-url]').forEach(function(element) {
        element.addEventListener('click', function(event) {
            event.preventDefault();
            modal.show();

            let $el = this;

            modalEl.querySelector('.generating').classList.remove('hide');
            modalEl.querySelector('.completed').classList.add('hide');

            let downloadButton = modalEl.querySelector('.download');
            downloadButton.classList.add('disabled');
            downloadButton.href = '#';

            axios.get($el.getAttribute('data-export-url'))
                .then(function(response) {
                    createPdf(response.data)
                        .then(pdf => {
                            modalEl.querySelector('.generating').classList.add('hide');
                            modalEl.querySelector('.completed').classList.remove('hide');

                            let downloadButton = modalEl.querySelector('.download');
                            downloadButton.classList.remove('disabled');
                            downloadButton.href = pdf.output('bloburi', {filename: 'export.pdf'});

                            downloadButton.addEventListener('click', function() {
                                modal.hide();
                            });
                        });
                });
        });
    });

});