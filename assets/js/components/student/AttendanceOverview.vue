<template>
  <div>
    <div class="card">
      <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped card-table">
          <tbody>
            <template v-for="group in dayGroups">
              <tr>
                <th>{{ $trans('date.week_label', { 'week': group.week.weekNumber}) }}</th>
                <th class="text-center"><i class="far fa-comments"></i></th>
                <th v-for="lessonNumber in maxLessons" class="text-center">{{ lessonNumber }}.</th>
              </tr>

              <tr v-for="day in group.days">
                <td class="align-middle">
                  {{ weekday(day) }}
                  <span class="text-muted">
                    {{ date(day) }}
                  </span>
                </td>
                <td>
                  <template v-for="comment in getComments(day)">
                    {{ comment.comment }} [{{comment.teacher}}]
                  </template>
                </td>
                <template v-for="lesson in days[day]">
                  <td v-if="lesson.colspan > 0"
                      :colspan="lesson.colspan"
                      :class="'text-center align-middle ' + (lesson.attendance !== null && lesson.attendance.attendance.type === 1 ? 'table-success text-success pointer' : '') + (lesson.attendance !== null && lesson.attendance.attendance.type === 0 ? 'table-danger text-danger pointer' : '') + (lesson.attendance !== null && lesson.attendance.attendance.type === 2 ? 'table-warning text-warning pointer' : '') + (lesson.entry !== null && lesson.entry.is_cancelled ? 'table-secondary' : '')"
                      @click="edit(lesson)"
                      @contextmenu.prevent="changeExcuseStatus(lesson)"
                      :title="lesson.entry !== null ? lesson.entry.lesson.subject + ' (' + lesson.entry.lesson.teachers.join(', ') + ')' + (lesson.entry.is_cancelled ? ' [' + lesson.entry.cancel_reason + ']' : '') : ''">
                      <div v-if="lesson.entry !== null">
                        <div v-if="lesson.entry !== null && lesson.entry.is_cancelled">
                            <i class="far fa-calendar-times"></i>
                        </div>
                        <div v-if="lesson.attendance !== null && lesson.attendance.attendance.type === 1">
                          <i class="fas fa-user-check"></i>
                        </div>
                        <div v-if="lesson.attendance !== null && lesson.attendance.attendance.type === 2">
                          <i class="fas fa-user-clock"></i>

                          <span class="badge text-bg-info d-block">
                            {{ $trans('book.attendance.late_minutes', { 'count': lesson.attendance.attendance.late_minutes}) }}
                          </span>
                        </div>
                        <div v-if="lesson.attendance !== null && lesson.attendance.attendance.type === 0">
                          <div v-if="lesson.attendance.has_excuses === true">
                            <i class="fas fa-check"></i>
                          </div>
                          <div v-if="lesson.attendance.has_excuses === false">
                            <i class="fas fa-question" v-if="lesson.attendance.attendance.excuse_status === 0 && lesson.attendance.attendance.absent_lessons > 0"></i>
                            <i class="fas fa-check" v-if="lesson.attendance.attendance.excuse_status === 1 || lesson.attendance.attendance.absent_lessons === 0"></i>
                            <i class="fas fa-times" v-if="lesson.attendance.attendance.excuse_status === 2"></i>
                          </div>

                          <span class="badge text-bg-info d-block" v-if="lesson.attendance.attendance.absent_lessons !== (lesson.entry.end - lesson.entry.start + 1)">
                            {{ lesson.attendance.attendance.absent_lessons }} FS
                          </span>
                        </div>
                      </div>
                  </td>
                </template>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $trans('book.attendance.label') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body" v-if="editAttendance !== null && editLesson !== null">
            <div class="d-flex" v-if="editLesson !== null">
              <span class="badge text-bg-primary">
                {{ editLesson.entry.lesson.subject }}
              </span>

              <div class="ms-2" v-for="teacher in editLesson.entry.lesson.teachers">
                <i class="fas fa-chalkboard-teacher"></i>
                {{ teacher }}
              </div>

              <div class="ms-2">
                <i class="fas fa-calendar-alt"></i>

                {{ date(editLesson.entry.lesson.date) }}
              </div>

              <div class="ms-2">
                <i class="fas fa-clock"></i>

                {{ $transChoice('label.exam_lessons', editLesson.entry.end - editLesson.entry.start, { 'start': editLesson.entry.start, 'end': editLesson.entry.end}) }}
              </div>
            </div>

            <div class="mt-2">
              <button class="btn btn-outline-success btn-sm d-inline-block"
                      @click.prevent="present()"
                      :title="$trans('book.attendance.type.present')"
                      :class="{ active: editAttendance.attendance.type === 1}"> <i class="fas fa-user-check"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm ms-1"
                      :class="{ active: editAttendance.attendance.type === 2}"
                      :title="$trans('book.attendance.type.late')"
                      @click.prevent="late()">
                <i class="fas fa-user-clock"></i>
              </button>
              <div class="btn-group d-inline-flex align-items-center ms-1"
                   :title="$trans('book.attendance.late')"
                   v-if="editAttendance.attendance.type === 2">
                <button class="btn btn-outline-warning btn-sm"
                        @click.prevent="minusMinute()">
                  <i class="fa fa-minus"></i>
                </button>
                <span class="border-top border-bottom border-warning align-self-stretch align-items-center d-flex px-2">
                  <span>{{ $transChoice('book.attendance.late_minutes', editAttendance.attendance.late_minutes, { '%count%': editAttendance.attendance.late_minutes }) }}</span>
                </span>
                <button class="btn btn-outline-warning btn-sm"
                        @click.prevent="plusMinute()">
                  <i class="fa fa-plus"></i>
                </button>
              </div>
              <button class="btn btn-outline-danger btn-sm ms-1 d-inline-block"
                      :class="{ active: editAttendance.attendance.type === 0}"
                      :title="$trans('book.attendance.type.absent')"
                      @click.prevent="absent()">
                <i class="fas fa-user-times"></i>
              </button>

              <div class="btn-group d-inline-flex align-items-center ms-1"
                   :title="$trans('book.attendance.absent_lessons')"
                   v-if="editAttendance.attendance.type === 0">
                <button class="btn btn-outline-danger btn-sm"
                        @click.prevent="minusLesson()">
                  <i class="fa fa-minus"></i>
                </button>
                <span class="border-top border-bottom border-danger align-self-stretch align-items-center d-flex px-2">
                  <span>{{ $transChoice('book.attendance.absence_lesson', editAttendance.attendance.absent_lessons, {'%count%': editAttendance.attendance.absent_lessons })}}</span>
                </span>
                <button class="btn btn-outline-danger btn-sm"
                        @click.prevent="plusLesson()">
                  <i class="fa fa-plus"></i>
                </button>
              </div>

              <div class="btn-group d-inline-flex align-items-center ms-1"
                   v-if="editAttendance.attendance.type === 0">
                <button class="btn btn-outline-danger btn-sm"
                        :title="$trans('book.students.not_set')"
                        :class="{ active: editAttendance.attendance.excuse_status === 0 && !editAttendance.attendance.has_excuses}"
                        :disabled="editAttendance.has_excuses"
                        @click.prevent="setExcuseStatus(0)">
                  <i class="fas fa-question"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm"
                        :title="$trans('book.students.excused')"
                        :class="{ active: editAttendance.attendance.excuse_status === 1 || editAttendance.has_excuses}"
                        @click.prevent="setExcuseStatus(1)">
                  <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm"
                        :title="$trans('book.students.not_excused')"
                        :class="{ active: editAttendance.attendance.excuse_status === 2 && !editAttendance.has_excuses}"
                        :disabled="editAttendance.has_excuses"
                        @click.prevent="setExcuseStatus(2)">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <div class="mt-2">
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fas fa-comment"></i>
                </span>

                <input type="text" v-model="editAttendance.attendance.comment" class="form-control" />
              </div>
            </div>

            <div class="mt-2" v-if="editAttendance.has_excuses">
              <div class="bs-callout bs-callout-success my-0">
                <p><i class="fas fa-circle-check"></i> {{ $trans('book.attendance.absence_reason.excuse')}}</p>
              </div>
            </div>

            <div class="mt-2" v-if="absences.length > 0">
              <div class="card" v-for="absence in absences">
                <div class="card-header">{{ absence.type }}</div>
                <div class="card-body" v-html="absence.html"></div>
                <div class="card-footer">
                  <a :href="absence.url" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fas fa-external-link-alt"></i> {{ $trans('absences.students.show.label')}}
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $trans('actions.cancel')}}</button>
            <button type="button" class="btn btn-primary" @click.prevent="save()"><i class="fas fa-save"></i> {{ $trans('actions.save')}}</button>
          </div>
        </div>
      </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end p-3" style="z-index: 5; right:0; bottom:0">
      <div class="toast bg-success text-bg-success" role="alert" aria-live="polite" aria-atomic="true" id="successToast">
        <div class="toast-body">
          <i class="fas fa-check-circle"></i> Erfolgreich gespeichert
        </div>
      </div>

      <div class="toast bg-danger text-bg-danger" role="alert" aria-live="polite" aria-atomic="true" id="errorToast">
        <div class="toast-body">
          <i class="fas fa-exclamation-triangle"></i> Fehler beim Speichern
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import Modal from 'bootstrap/js/dist/modal';
import Toast from 'bootstrap/js/dist/toast';

export default {
  name: 'attendance_overview',
  props: {
    readonly: Boolean,
    entries: Object,
    attendances: Array,
    comments: Array,
    dayGroups: Array,
    maxLessons: Number,
    url: String,
    absencesUrl: String,
    csrftoken: String
  },
  data() {
    return {
      days: { },
      modal: null,
      toasts: {
        success: null,
        error: null,
        successId: null,
        errorId: null
      },
      editAttendance: null,
      editLesson: null,
      absences: [ ]
    }
  },
  mounted() {
    // Create data structures
    let $this = this;
    let lessonRange = [...Array(this.maxLessons).keys()].map(i => i + 1);

    this.dayGroups.forEach(function(group) {
      group.days.forEach(function(day) {
        let lessons = { };
        let date = $this.toDate(day);
        let dateKey = '' + date.getFullYear() + ("0" + (date.getMonth() + 1)).slice(-2) + '' + ("0" + date.getDate()).slice(-2);

        lessonRange.forEach(function(lessonNumber) {
          let key = dateKey + '_' + lessonNumber;
          let entry = $this.entries[key] ?? null;
          let attendance = $this.attendances.filter(a => a.date === day && a.lesson === lessonNumber)[0] ?? null;

          let colspan = 1;

          if(entry != null) {
            colspan = entry.end - entry.start + 1

            if(lessonNumber !== entry.start) {
              colspan = 0;
            }
          }

          lessons[lessonNumber] = {
            'lesson': lessonNumber,
            'entry': entry,
            'attendance': entry !== null ? attendance : null,
            'colspan': colspan
          };
          
        });

        $this.days[day] = lessons;
      });
    });
  },
  methods: {
    toDate(dateAsString) {
      return new Date(dateAsString);
    },
    weekday(dateAsString) {
      let key = 'date.days_short.' + this.toDate(dateAsString).getDay();
      return this.$trans(key);
    },
    date(dateAsString) {
      return this.toDate(dateAsString).toLocaleDateString(undefined, { weekday: undefined, day: '2-digit', month: '2-digit', year: 'numeric' });
    },
    getComments(dateAsString) {
      return this.comments.filter(c => c.date === dateAsString);
    },
    edit(lesson) {
      if(this.readonly === true) {
        return;
      }

      if(lesson.entry === null || lesson.attendance === null) {
        return;
      }

      if(this.modal === null) {
        this.modal = new Modal(this.$el.querySelector('.modal'));
      }

      this.editLesson = lesson;
      this.editAttendance = JSON.parse(JSON.stringify(lesson.attendance));

      this.modal.show();

      this.absences = [ ];

      if(lesson.attendance.attendance.type !== 1) {
        let $this = this;
        let url = this.absencesUrl.replace('lesson', lesson.entry.lesson.uuid);
        this.$http.get(url)
            .then(function(response) {
              $this.absences = response.data;
            })
            .catch(function(error) {
              console.error(error);
            });
      }
    },
    minusMinute() {
      if(this.editAttendance.attendance.late_minutes > 0) {
        this.editAttendance.attendance.late_minutes--;
      }
    },
    plusMinute() {
      this.editAttendance.attendance.late_minutes++;
    },
    setType(type) {
      if(this.editAttendance !== null) {
        this.editAttendance.attendance.type = type;
      }
    },
    absent() {
      this.setType(0);
    },
    present() {
      this.setType(1);
    },
    late() {
      this.setType(2);
    },
    plusLesson() {
      if(this.editAttendance.attendance === null) {
        return;
      }

      if(this.editAttendance.attendance.absent_lessons <= (this.editLesson.entry.end - this.editLesson.entry.start)) {
        this.editAttendance.attendance.absent_lessons++;
      }
    },
    minusLesson() {
      if(this.editAttendance.attendance === null) {
        return;
      }

      if(this.editAttendance.attendance.absent_lessons > 0) {
        this.editAttendance.attendance.absent_lessons--;
      }
    },
    setExcuseStatus(status) {
      if(this.editAttendance.attendance === null) {
        return;
      }

      this.editAttendance.attendance.excuse_status = status;
    },
    save() {
      if(this.editAttendance.attendance === null) {
        return;
      }

      // write data back to original attendance
      this.editLesson.attendance.attendance.type = this.editAttendance.attendance.type;
      this.editLesson.attendance.attendance.late_minutes = this.editAttendance.attendance.late_minutes;
      this.editLesson.attendance.attendance.absent_lessons = this.editAttendance.attendance.absent_lessons;
      this.editLesson.attendance.attendance.comment = this.editAttendance.attendance.comment;
      this.editLesson.attendance.attendance.excuse_status = this.editAttendance.attendance.excuse_status;

      let $this = this;

      this.uploadData(this.editLesson, function() {
        $this.editLesson = null;
        $this.editAttendance = null;
        $this.modal.hide();
      });
    },
    uploadData(lesson, callback) {
      if(this.readonly === true) {
        return;
      }

      let url = this.url.replace('uuid', lesson.attendance.attendance.uuid);
      let comment = lesson.attendance.attendance.comment;

      if(comment !== null) {
        comment = comment.trim();

        if(comment === '') {
          comment = null;
        }
      }

      let request = {
        '_token': this.csrftoken,
        'type': lesson.attendance.attendance.type,
        'absent_lessons': lesson.attendance.attendance.absent_lessons,
        'late_minutes': lesson.attendance.attendance.late_minutes,
        'comment': comment,
        'excuse_status': lesson.attendance.attendance.excuse_status
      };

      let $this = this;

      // save
      this.$http
          .put(url, request)
          .then(function() {
            let successToast = Toast.getOrCreateInstance($this.$el.querySelector('#successToast'));
            successToast.show();
          })
          .catch(function(e) {
            let errorToast = Toast.getOrCreateInstance($this.$el.querySelector('#errorToast'));
            errorToast.show();

            console.error(e);
          });

      callback();
    },
    changeExcuseStatus(lesson) {
      if(this.readonly === true) {
        return;
      }

      if(lesson.entry === null || lesson.attendance === null || lesson.attendance.attendance.type !== 0) {
        return;
      }

      if(lesson.attendance.has_excuses === true) {
        return;
      }

      lesson.attendance.attendance.excuse_status = (lesson.attendance.attendance.excuse_status + 1) % 3;
      this.uploadData(lesson, function() { });
    }
  }
}
</script>