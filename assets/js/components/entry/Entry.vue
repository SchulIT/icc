<template>
  <div>
    <div class="dropdown">
      <button class="btn btn-primary btn-sm" type="button" data-toggle="dropdown"
              v-if="!isInitialized || entry.isCancelled === false">
        <i class="fas fa-spinner fa-spin" v-if="!isInitialized"></i>
        <i class="fas fa-book-open" v-if="isInitialized"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right" v-if="entry.uuid === null">
        <div class="dropdown-header">{{ $trans('book.entry.add.label')}}</div>
        <button class="dropdown-item"
                @click="create(lessonNumber, lessonNumber)">
          <span class="badge badge-primary">{{ lessonNumber }}.</span>
          {{ $trans('book.entry.add.single') }}
        </button>
        <button class="dropdown-item"
                @click="create(lesson.lessonStart, lesson.lessonEnd)"
                v-if="lesson.lessonStart !== lesson.lessonEnd">
          <span class="badge badge-primary">{{ lesson.lessonStart }}./{{ lesson.lessonEnd }}.</span>
          {{ $trans('book.entry.add.double') }}
        </button>

        <div class="dropdown-header">{{ $trans('book.entry.cancel.label')}}</div>

        <button class="dropdown-item"
                @click="cancel(lessonNumber, lessonNumber)">
          <span class="badge badge-primary">{{ lessonNumber }}.</span>
          {{ $trans('book.entry.cancel.single') }}
        </button>
        <button class="dropdown-item"
                @click="cancel(lesson.lessonStart, lesson.lessonEnd)"
                v-if="lesson.lessonStart !== lesson.lessonEnd">
          <span class="badge badge-primary">{{ lesson.lessonStart }}./{{ lesson.lessonEnd }}.</span>
          {{ $trans('book.entry.cancel.double') }}
        </button>
      </div>
    </div>

    <div class="modal fade entry">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <form :action="createAction" method="post">
            <div class="modal-header">
              <h5 class="modal-title">{{ $trans('book.entry.label') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="container-fluid px-0">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="card">
                      <div class="card-header">
                        <div class="d-flex align-items-center">
                          <span class="badge badge-secondary" v-if="tuition.subject !== null">{{ tuition.subject.name.toUpperCase() }}</span>
                          <div class="ml-2">
                            {{ tuition.name }}
                          </div>

                          <div class="ml-2" v-if="tuition.studyGroup !== null" v-for="grade in tuition.studyGroup.grades">
                            <i class="fas fa-users"></i>
                            {{ grade.name }}
                          </div>

                          <div class="ml-2" v-for="teacher in tuition.teachers">
                            <i class="fas fa-chalkboard-teacher"></i>
                            {{ teacher.acronym }}
                          </div>

                          <div class="ml-2" v-if="lesson.date !== null">
                            <i class="fas fa-calendar-alt"></i> {{ lesson.date.toLocaleDateString() }}
                          </div>
                        </div>
                      </div>

                      <div class="card-body">
                        <div class="form-group row">
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

                        <div class="form-group">
                          <label for="topic" class="control-label">{{ $trans('label.topic') }}</label>
                          <input v-model="entry.topic" name="lesson_entry[topic]" :class="'topic form-control ' + (validation.topic !== null ? 'is-invalid' : '')" id="topic">
                          <div class="invalid-feedback" v-show="validation.topic !== null">{{ validation.topic }}</div>
                        </div>

                        <div class="form-group">
                          <label for="exercises" class="control-label">{{ $trans('label.exercises') }}</label>
                          <textarea v-model="entry.exercises" name="lesson_entry[exercises]" class="form-control" id="exercises"></textarea>
                        </div>

                        <div class="form-group">
                          <label for="comment" class="control-label">{{ $trans('label.comment') }}</label>
                          <textarea v-model="entry.comment" name="lesson_entry[comment]" class="form-control" id="comment"></textarea>
                        </div>

                        <div class="form-group">
                          <label for="replacementSubject" class="control-label">{{ $trans('label.replacement_subject') }}</label>
                          <input v-model="entry.replacementSubject" name="lesson_entry[replacementSubject]" class="form-control" id="replacementSubject">
                        </div>

                        <div class="form-group">
                          <label for="replacementTeacher" class="control-label">{{ $trans('label.replacement_teacher') }}</label>
                          <select name="lesson_entry[replacementTeacher]" class="form-control" id="replacementTeacher"></select>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <students :attendances="entry.attendances"
                              :possible-absences="absences"
                              :students="students"
                              :step="1"
                              :list-students-url="studentsUrl"
                              :start="entry.start"
                              :end="entry.end"
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
          <form :action="cancelAction" method="post">
            <div class="modal-header">
              <h5 class="modal-title">{{ $trans('book.entry.cancel.label') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="d-flex align-items-center">
                <span class="badge badge-secondary" v-if="tuition.subject !== null">{{ tuition.subject.name.toUpperCase() }}</span>
                <div class="ml-2">
                  {{ tuition.name }}
                </div>

                <div class="ml-2" v-if="tuition.studyGroup !== null" v-for="grade in tuition.studyGroup.grades">
                  <i class="fas fa-users"></i>
                  {{ grade.name }}
                </div>

                <div class="ml-2" v-for="teacher in tuition.teachers">
                  <i class="fas fa-chalkboard-teacher"></i>
                  {{ teacher.acronym }}
                </div>

                <div class="ml-2" v-if="lesson.date !== null">
                  <i class="fas fa-calendar-alt"></i> {{ lesson.date.toLocaleDateString() }}
                </div>
              </div>

              <div class="form-group row">
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

              <div class="form-group">
                <label for="topic" class="control-label">{{ $trans('book.entry.cancel.reason') }}</label>
                <input v-model="entry.topic" name="lesson_entry_cancel[cancelReason]" :class="'cancel_reason form-control ' + (validation.topic !== null ? 'is-invalid' : '')" id="topic">
                <div class="invalid-feedback" v-show="validation.topic !== null">{{ validation.topic }}</div>
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
import { Dropdown, Modal } from 'bootstrap.native';
import NumberInput from "../NumberInput";
import Students from "../entry/Students";
import Choices from "choices.js";

export default {
  name: 'entry',
  components: { NumberInput, Students },
  props: {
    url: String,
    studentsUrl: String,
    csrftoken: String,
    csrfname: String,
    createAction: String,
    cancelAction: String,
    lessonNumber: Number
  },
  data() {
    return {
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
    this.ref = window.location;
    this.$http
      .get(this.url)
      .then(function(response) {
        $this.lesson.uuid = response.data.lesson.uuid;
        $this.lesson.date = new Date(response.data.lesson.date);
        $this.lesson.lessonStart = response.data.lesson.lesson_start;
        $this.lesson.lessonEnd = response.data.lesson.lesson_end;

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
          $this.entry.cancelReason = response.data.entry.cancel_reason;
          $this.entry.attendances = response.data.entry.attendances;
        }

        $this.students = response.data.students.sort(function(a, b) {
          let studentA = a.lastname + ", " + a.firstname;
          let studentB = b.lastname + ", " + b.firstname;
          return studentA.localeCompare(studentB, 'de', { sensitivity: 'base', numeric: true });
        });

        if($this.entry.uuid === null) {
          new Dropdown($this.$el.querySelector('.dropdown'), { persist: false });
        } else {
          $this.$el.querySelector('.dropdown > button').addEventListener('click', function() {
            $this.show();
          })
        }

        $this.absences = response.data.absences;
        $this.isInitialized = true;
      })
      .catch(function(error) {
        console.log(error);
      });
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
    validate() {
      if(this.entry.topic === null || this.entry.topic.trim() === '') {
        this.validation.topic = this.$trans('This value should not be blank.', {}, 'validators');
      } else {
        this.validation.topic = null;
      }
    },
    show() {
      if(this.modal.create === null) {
        let modalEl = this.$el.querySelector('.modal.entry');
        this.modal.create = new Modal(modalEl);

        modalEl.addEventListener('shown.bs.modal', function() {
          modalEl.querySelector('input.topic').focus();
        });
      }

      this.$refs.studentComponent.load();
      this.modal.create.show();
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
            comment: null
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