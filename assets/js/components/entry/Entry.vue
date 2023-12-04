<template>
  <div>
    <div class="dropdown">
      <button class="btn btn-primary btn-sm" type="button"
              :data-bs-toggle="entry.uuid === null ? 'dropdown' : ''"
              :class="entry.uuid === null ? 'btn btn-primary btn-sm' : 'btn btn-success btn-sm'">
        <i class="fas fa-spinner fa-spin" v-if="!isInitialized"></i>
        <i class="fas fa-book-open" v-if="isInitialized"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-end" v-if="entry.uuid === null">
        <div class="dropdown-header">{{ $trans('book.entry.add.label')}}</div>
        <button class="dropdown-item"
                @click="create(lessonNumber, lessonNumber)">
          <span class="badge text-bg-primary">{{ lessonNumber }}.</span>
          {{ $trans('book.entry.add.single') }}
        </button>
        <button class="dropdown-item"
                @click="create(lesson.lessonStart, lesson.lessonEnd)"
                v-if="lesson.lessonStart !== lesson.lessonEnd">
          <span class="badge text-bg-primary">{{ lesson.lessonStart }}./{{ lesson.lessonEnd }}.</span>
          {{ $trans('book.entry.add.double') }}
        </button>

        <div class="dropdown-header">{{ $trans('book.entry.cancel.label')}}</div>

        <button class="dropdown-item"
                @click="cancel(lessonNumber, lessonNumber)">
          <span class="badge text-bg-primary">{{ lessonNumber }}.</span>
          {{ $trans('book.entry.cancel.single') }}
        </button>
        <button class="dropdown-item"
                @click="cancel(lesson.lessonStart, lesson.lessonEnd)"
                v-if="lesson.lessonStart !== lesson.lessonEnd">
          <span class="badge text-bg-primary">{{ lesson.lessonStart }}./{{ lesson.lessonEnd }}.</span>
          {{ $trans('book.entry.cancel.double') }}
        </button>
      </div>
    </div>

    <div class="modal fade entry">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <form :action="action" method="post">
            <div class="modal-header">
              <h5 class="modal-title me-auto">{{ $trans('book.entry.label') }}</h5>

              <button type="submit" class="btn btn-primary" :disabled="!isValid || !isInitialized">
                <i class="fas fa-save"></i> {{ $trans('actions.save')}}
              </button>

              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="container-fluid px-0">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="card">
                      <div class="card-header">
                        <div class="d-flex align-items-center">
                          <span class="badge text-bg-secondary" v-if="tuition.subject !== null">{{ tuition.subject.name.toUpperCase() }}</span>
                          <div class="ms-2">
                            {{ tuition.name }}
                          </div>

                          <div class="ms-2" v-if="tuition.studyGroup !== null" v-for="grade in tuition.studyGroup.grades">
                            <i class="fas fa-users"></i>
                            {{ grade.name }}
                          </div>

                          <div class="ms-2" v-for="teacher in tuition.teachers">
                            <i class="fas fa-chalkboard-teacher"></i>
                            {{ teacher.acronym }}
                          </div>

                          <div class="ms-2" v-if="lesson.date !== null">
                            <i class="fas fa-calendar-alt"></i> {{ lesson.date.toLocaleDateString() }}
                          </div>
                        </div>
                      </div>

                      <div class="card-body">
                        <div class="mb-3 row">
                          <div class="col-6">
                            <label for="start" class="control-label">{{ $trans('label.start')}}</label>
                            <number-input v-model="entry.start" name="lesson_entry[lessonStart]" :min="lesson.lessonStart" :max="lesson.lessonEnd" id="start" :class="validation.start !== null ? 'is-invalid' : ''"></number-input>
                            <div class="invalid-feedback" v-show="validation.start !== null">{{ validation.start }}</div>
                          </div>
                          <div class="col-6">
                            <label for="end" class="control-label">{{ $trans('label.end')}}</label>
                            <number-input v-model="entry.end" name="lesson_entry[lessonEnd]" :min="lesson.lessonStart" :max="lesson.lessonEnd" id="end" :class="validation.end !== null ? 'is-invalid' : ''"></number-input>
                            <div class="invalid-feedback" v-show="validation.end !== null">{{ validation.end }}</div>
                          </div>
                        </div>

                        <div class="mb-3">
                          <label for="topic" class="control-label">{{ $trans('label.topic') }}</label>
                          <input v-model="entry.topic" name="lesson_entry[topic]" :class="'topic form-control ' + (validation.topic !== null ? 'is-invalid' : '')" id="topic">
                          <div class="invalid-feedback" v-show="validation.topic !== null">{{ validation.topic }}</div>
                        </div>

                        <div class="mb-3">
                          <label for="exercises" class="control-label">{{ $trans('label.exercises') }}</label>
                          <textarea v-model="entry.exercises" name="lesson_entry[exercises]" class="form-control" id="exercises"></textarea>
                        </div>

                        <div class="mb-3">
                          <label for="comment" class="control-label">{{ $trans('book.entry.comment.label') }}</label>
                          <textarea v-model="entry.comment" name="lesson_entry[comment]" class="form-control" id="comment"></textarea>
                          <small class="form-text text-muted">
                            {{ $trans('book.entry.comment.help')}}
                          </small>
                        </div>

                        <div class="mb-3">
                          <label for="replacementSubject" class="control-label">{{ $trans('label.replacement_subject') }}</label>
                          <input v-model="entry.replacementSubject" name="lesson_entry[replacementSubject]" class="form-control" id="replacementSubject">
                        </div>

                        <div class="mb-3">
                          <label for="replacementTeacher" class="control-label">{{ $trans('label.replacement_teacher') }}</label>
                          <select name="lesson_entry[replacementTeacher]" class="form-control" id="replacementTeacher"></select>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <students :attendances="entry.attendances"
                              :possible-absences="absences"
                              :suggested-removals="removals"
                              :students="students"
                              :step="1"
                              :list-students-url="studentsUrl"
                              :list-study-groups-url="studyGroupsUrl"
                              :start="entry.start"
                              :end="entry.end"
                              :flags="flags"
                              :show-save-button="false"
                              ref="studentComponent"></students>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" :disabled="!isValid || !isInitialized">
                <i class="fas fa-save"></i> {{ $trans('actions.save')}}
              </button>
            </div>

            <input type="hidden" name="date" v-if="lesson.date !== null" :value="lesson.date.toJSON()">
            <input type="hidden" :name="'lesson_entry[' + csrfname + ']'" :value="csrftoken">
            <input type="hidden" name="_ref" :value="ref">
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade cancel">
      <div class="modal-dialog">
        <div class="modal-content">
          <form :action="editAction !== '' ? editAction : cancelAction" method="post">
            <div class="modal-header">
              <h5 class="modal-title">{{ $trans('book.entry.cancel.label') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="d-flex align-items-center">
                <span class="badge text-bg-secondary" v-if="tuition.subject !== null">{{ tuition.subject.name.toUpperCase() }}</span>
                <div class="ms-2">
                  {{ tuition.name }}
                </div>

                <div class="ms-2" v-if="tuition.studyGroup !== null" v-for="grade in tuition.studyGroup.grades">
                  <i class="fas fa-users"></i>
                  {{ grade.name }}
                </div>

                <div class="ms-2" v-for="teacher in tuition.teachers">
                  <i class="fas fa-chalkboard-teacher"></i>
                  {{ teacher.acronym }}
                </div>

                <div class="ms-2" v-if="lesson.date !== null">
                  <i class="fas fa-calendar-alt"></i> {{ lesson.date.toLocaleDateString() }}
                </div>
              </div>

              <div class="mb-3 row">
                <div class="col-6">
                  <label for="start" class="control-label">{{ $trans('label.start')}}</label>
                  <number-input v-model="entry.start" name="lesson_entry_cancel[lessonStart]" :min="lesson.lessonStart" :max="lesson.lessonEnd" id="start" :class="validation.start !== null ? 'is-invalid' : ''"></number-input>
                  <div class="invalid-feedback" v-show="validation.start !== null">{{ validation.start }}</div>
                </div>
                <div class="col-6">
                  <label for="end" class="control-label">{{ $trans('label.end')}}</label>
                  <number-input v-model="entry.end" name="lesson_entry_cancel[lessonEnd]" :min="lesson.lessonStart" :max="lesson.lessonEnd" id="end" :class="validation.end !== null ? 'is-invalid' : ''"></number-input>
                  <div class="invalid-feedback" v-show="validation.end !== null">{{ validation.end }}</div>
                </div>
              </div>

              <div class="mb-3">
                <label for="topic" class="control-label">{{ $trans('book.entry.cancel.reason') }}</label>
                <input v-model="entry.topic" name="lesson_entry_cancel[cancelReason]" :class="'cancel_reason form-control ' + (validation.topic !== null ? 'is-invalid' : '')" id="topic">
                <div class="invalid-feedback" v-show="validation.topic !== null">{{ validation.topic }}</div>
              </div>

              <div class="mb-3">
                <label for="exercises" class="control-label">{{ $trans('label.exercises') }}</label>
                <textarea v-model="entry.exercises" name="lesson_entry_cancel[exercises]" class="form-control" id="exercises"></textarea>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" :disabled="!isValid || !isInitialized">
                <i class="fas fa-save"></i> {{ $trans('actions.save')}}
              </button>
            </div>

            <input type="hidden" name="date" v-if="lesson.date !== null" :value="lesson.date.toJSON()">
            <input type="hidden" :name="'lesson_entry_cancel[' + csrfname + ']'" :value="csrftoken">
            <input type="hidden" name="_ref" :value="ref">

          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Modal from 'bootstrap/js/dist/modal';
import NumberInput from "../NumberInput";
import Students from "../entry/Students";
import Choices from "choices.js";

export default {
  name: 'entry',
  components: { NumberInput, Students },
  props: {
    url: String,
    studentsUrl: String,
    teachersUrl: String,
    studyGroupsUrl: String,
    teacher: String,
    csrftoken: String,
    csrfname: String,
    createAction: String,
    editAction: String,
    cancelAction: String,
    lessonNumber: Number
  },
  data() {
    return {
      action: this.createAction,
      isInitialized: false,
      isLoading: false,
      lesson: {
        uuid: null,
        date: null,
        lessonStart: null,
        lessonEnd: null
      },
      entry: {
        uuid: null,
        start: null,
        end: null,
        topic: null,
        exercises: null,
        comment: null,
        isCancelled: false,
        replacementTeacher: null,
        replacementSubject: null,
        attendances: [ ]
      },
      tuition: {
        uuid: null,
        name: null,
        subject: null,
        teachers: [ ],
        studyGroup: {
          name: null,
          type: null,
          grades: [ ]
        }
      },
      students: [ ],
      absences: [ ],
      removals: [ ],
      flags: [ ],
      validation: {
        topic: null,
        start: null,
        end: null
      },
      modal: {
        create: null,
        cancel: null
      },
      ref: null
    }
  },
  mounted() {
    let $this = this;
    let observer = new IntersectionObserver(onIntersecting);
    function onIntersecting(entries) {
      if(entries[0].intersectionRatio > 0) {
        $this.initialize();
        observer.unobserve(entries[0].target);
      }
    };
    observer.observe(this.$el);
  },
  watch: {
    entry: {
      deep: true,
      handler() {
        this.validate();
      }
    }
  },
  computed: {
    isValid() {
      return this.isInitialized
          && this.validation.topic === null;
    }
  },
  methods: {
    initialize() {
      let $this = this;
      this.ref = window.location;

      this.$http
          .get(this.url)
          .then(function(response) {
            $this.lesson.uuid = response.data.lesson.uuid;
            $this.lesson.date = new Date(response.data.lesson.date);
            $this.lesson.lessonStart = response.data.lesson.lesson_start;
            $this.lesson.lessonEnd = response.data.lesson.lesson_end;

            if(response.data.has_other_entries) {
              $this.lesson.lessonEnd = response.data.lesson.lesson_start;
            }

            $this.tuition.uuid = response.data.lesson.tuition.uuid;
            $this.tuition.name = response.data.lesson.tuition.name;
            $this.tuition.subject = response.data.lesson.tuition.subject;
            $this.tuition.teachers = response.data.lesson.tuition.teachers;
            $this.tuition.studyGroup.name = response.data.lesson.tuition.study_group.name;
            $this.tuition.studyGroup.type = response.data.lesson.tuition.study_group.type;
            $this.tuition.studyGroup.grades = response.data.lesson.tuition.study_group.grades.sort(function(a, b) {
              return a.name.localeCompare(b.name, 'de', { sensitivity: 'base', numeric: true });
            })

            if(response.data.entry !== null) {
              $this.entry.uuid = response.data.entry.uuid;
              $this.entry.start = response.data.entry.start;
              $this.entry.end = response.data.entry.end;
              $this.entry.topic = response.data.entry.topic;
              $this.entry.exercises = response.data.entry.exercises;
              $this.entry.comment = response.data.entry.comment;
              $this.entry.isCancelled = response.data.entry.is_cancelled;
              if(response.data.entry.is_cancelled) {
                $this.entry.topic = response.data.entry.cancel_reason;
              }
              $this.entry.attendances = response.data.entry.attendances;
              $this.entry.replacementTeacher = response.data.entry.replacement_teacher;
              $this.entry.replacementSubject = response.data.entry.replacement_subject;
            }

            $this.students = response.data.students.sort(function(a, b) {
              let studentA = a.lastname + ", " + a.firstname;
              let studentB = b.lastname + ", " + b.firstname;
              return studentA.localeCompare(studentB, 'de', { sensitivity: 'base', numeric: true });
            });

            if($this.entry.uuid === null) {

            } else {
              $this.action = $this.editAction;
              $this.$el.querySelector('.dropdown > button').addEventListener('click', function() {
                $this.show();
              })
            }

            $this.absences = response.data.absences;
            $this.removals = response.data.removals;
            $this.flags = response.data.flags;
            $this.isInitialized = true;
          })
          .catch(function(error) {
            console.log(error);
          });

      this.teacherChoice = new Choices(this.$el.querySelector('#replacementTeacher'));
    },
    validate() {
      if(this.entry.topic === null || this.entry.topic.trim() === '') {
        this.validation.topic = this.$trans('This value should not be blank.', {}, 'validators');
      } else if(this.entry.topic.length > 255) {
        this.validation.topic = this.$transChoice('This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.', 255, { }, 'validators').replace('{{ limit }}', 255);
      }  else {
        this.validation.topic = null;
      }
    },
    show() {
      let $this = this;

      if(this.entry.isCancelled === true) {
        if(this.modal.cancel === null) {
          let modalEl = this.$el.querySelector('.modal.cancel');
          this.modal.cancel = new Modal(modalEl);

          modalEl.addEventListener('shown.bs.modal', function () {
            modalEl.querySelector('input.cancel_reason').focus();
          });

          this.modal.cancel.show();
        }
      } else {
        if (this.modal.create === null) {
          let modalEl = this.$el.querySelector('.modal.entry');
          this.modal.create = new Modal(modalEl);

          modalEl.addEventListener('shown.bs.modal', function () {
            modalEl.querySelector('input.topic').focus();
          });
        }

        this.$http
            .get(this.teachersUrl)
            .then(function (response) {
              let choices = [
                {
                  label: $this.$trans('label.select.teacher'),
                  value: '',
                  selected: true
                }
              ];

              let autoSelectTeacher = null;
              let tuitionTeachers = $this.tuition.teachers.map(function (teacher) {
                return teacher.acronym;
              });

              if ($this.teacher !== null && !tuitionTeachers.includes($this.teacher) && $this.entry.uuid === null) {
                autoSelectTeacher = $this.teacher;
              }

              if ($this.entry.replacementTeacher !== null) {
                autoSelectTeacher = $this.entry.replacementTeacher.acronym;
              }

              response.data.forEach(function (teacher) {
                choices.push({
                  label: teacher.acronym,
                  value: teacher.uuid,
                  selected: teacher.acronym === autoSelectTeacher
                });
              });

              $this.teacherChoice.setChoices(choices, 'value', 'label', true);
            })
            .catch(function (error) {
              console.log(error);
            });

        this.$refs.studentComponent.load();
        this.modal.create.show();
      }
    },

    create(start, end) {
      this.entry.start = start;
      this.entry.end = end;

      let $this = this;

      if(this.entry.attendances.length === 0) {
        this.students.forEach(function(student) {
          $this.entry.attendances.push({
            type: 1,
            student: student,
            minutes: 0,
            lessons: 0,
            excuse_status: 0,
            comment: null,
            flags: [ ]
          });
        });
      }

      this.show();
    },

    cancel(start, end) {
      this.entry.start = start;
      this.entry.end = end;

      if(this.modal.cancel === null) {
        let modalEl = this.$el.querySelector('.modal.cancel');
        this.modal.cancel = new Modal(modalEl);
        modalEl.addEventListener('shown.bs.modal', function() {
          modalEl.querySelector('input.cancel_reason').focus();
        });
      }

      this.modal.cancel.show();
    },
  }
}
</script>