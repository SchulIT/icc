<template>
  <div>
    <div class="dropdown" v-if="start !== end">
      <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="dropdown" :title="$trans('book.entry.add.label')">
        <i class="fa fa-plus"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-end">
        <button class="dropdown-item"
                @click="add(lesson, lesson)">
          <span class="badge text-bg-primary">
            {{ lesson }}.
          </span>
          {{ $trans('book.entry.add.single') }}
        </button>
        <button class="dropdown-item"
                @click="add(start, end)">
          <span class="badge text-bg-primary">
            {{ start }}./{{ end }}.
          </span>
          {{ $trans('book.entry.add.double') }}
        </button>
      </div>
    </div>

    <button class="btn btn-primary btn-sm"
            type="button" data-bs-toggle="dropdown"
            :title="$trans('book.entry.add.label')"
            @click="add(lesson, lesson)"
            v-if="start === end">
      <i class="fa fa-plus"></i>
    </button>

    <div class="modal fade">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <form :action="action" method="post">
            <div class="modal-header">
              <h5 class="modal-title">{{ $trans('book.entry.add.label') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


              <div class="container-fluid px-0">
                <div class="row">
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header">
                        <div v-if="isLoading">
                          <i class="fas fa-spinner fa-spin"></i> {{ $trans('label.loading')}}
                        </div>
                        <div class="d-flex align-items-center" v-if="isInitialized === true">
                          <span class="badge text-bg-secondary" v-if="tuition !== null">{{ tuition.subject.name.toUpperCase() }}</span>

                          <div class="ms-2" v-if="tuition !== null">
                            {{ tuition.name }}
                          </div>

                          <div class="ms-2" v-if="tuition !== null" v-for="grade in tuition.study_group.grades">
                            <i class="fas fa-users"></i>
                            {{ grade.name }}
                          </div>

                          <div class="ms-2" v-for="teacher in tuition.teachers" v-if="tuition !== null">
                            <i class="fas fa-chalkboard-teacher"></i>
                            {{ teacher.acronym }}
                          </div>

                          <div class="ms-2">
                            <i class="fas fa-calendar-alt"></i> {{ date.toLocaleDateString() }}
                          </div>
                        </div>
                      </div>

                      <div class="card-body">
                        <div class="mb-3 row">
                          <div class="col-6">
                            <label for="start" class="control-label">{{ $trans('label.start')}}</label>
                            <number-input v-model="entry.start" name="lesson_entry[lessonStart]" :min="start" :max="end" id="start" :class="validation.start !== null ? 'is-invalid' : ''"></number-input>
                            <div class="invalid-feedback" v-show="validation.start !== null">{{ validation.start }}</div>
                          </div>
                          <div class="col-6">
                            <label for="end" class="control-label">{{ $trans('label.end')}}</label>
                            <number-input v-model="entry.end" name="lesson_entry[lessonEnd]" :min="start" :max="end" id="end" :class="validation.end !== null ? 'is-invalid' : ''"></number-input>
                            <div class="invalid-feedback" v-show="validation.end !== null">{{ validation.end }}</div>
                          </div>
                        </div>

                        <div class="mb-3">
                          <label for="topic" class="control-label">{{ $trans('label.topic') }}</label>
                          <input v-model="entry.topic" name="lesson_entry[topic]" :class="'form-control ' + (validation.topic !== null ? 'is-invalid' : '')" id="topic">
                          <div class="invalid-feedback" v-show="validation.topic !== null">{{ validation.topic }}</div>
                        </div>

                        <div class="mb-3">
                          <label for="exercises" class="control-label">{{ $trans('label.exercises') }}</label>
                          <textarea v-model="entry.exercises" name="lesson_entry[exercises]" class="form-control" id="exercises"></textarea>
                        </div>

                        <div class="mb-3">
                          <label for="comment" class="control-label">{{ $trans('label.comment') }}</label>
                          <textarea v-model="entry.comment" name="lesson_entry[comment]" class="form-control" id="comment"></textarea>
                        </div>

                        <div class="mb-3">
                          <label for="replacementSubject" class="control-label">{{ $trans('label.replacement_subject') }}</label>
                          <input v-model="entry.replacementSubject" maxlength="255" name="lesson_entry[replacementSubject]" class="form-control" id="replacementSubject">
                        </div>

                        <div class="mb-3">
                          <label for="replacementTeacher" class="control-label">{{ $trans('label.replacement_teacher') }}</label>
                          <select name="lesson_entry[replacementTeacher]" class="form-control" id="replacementTeacher"></select>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <students :students-url="studentsUrl"
                              :attendancedata="[]"
                              :step="1"
                              :start="start"
                              :end="end"
                              :show-save-button="false"
                              :list-students-url="listStudentsUrl"></students>
                  </div>
                </div>
              </div>
            </div>

            <input type="hidden" name="date" :value="date.toJSON()">
            <input type="hidden" name="tuition" :value="tuitionUuid">
            <input type="hidden" :name="'lesson_entry[' + csrfname + ']'" :value="csrftoken">
            <input type="hidden" name="_ref" :value="ref">
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $trans('action.cancel') }}</button>
              <button type="button" class="btn btn-primary" @click.prevent="submit()" :disabled="!this.isValid">{{$trans('action.save')}}</button>
            </div>
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
  name: 'lesson_create_button',
  components: { NumberInput, Students },
  props: {
    tuitionUrl: String,
    studentsUrl: String,
    teachersUrl: String,
    listStudentsUrl: String,
    date: Date,
    start: Number,
    end: Number,
    tuitionUuid: String,
    teacher: String,
    lesson: Number,
    csrftoken: String,
    csrfname: String,
    action: String
  },
  data() {
    return {
      //isInitialized: false,
      isLoading: false,
      //isLoadingTuition: false,
      tuition: null,
      choices: null,
      students: null,
      validation: {
        start: null,
        end: null,
        topic: null
      },
      entry: {
        tuition: this.tuition,
        start: this.start,
        end: this.end,
        date: this.date,
        topic: null,
        subject: null,
        replacementTeacher: null,
        replacementSubject: null,
        exercises: null,
        comment: null
      },
      ref: null
    }
  },
  computed: {
    isValid() {
      return this.validation.start === null
          && this.validation.end === null
          && this.validation.topic === null;
    },
    isInitialized() {
      return this.tuition !== null && this.choices !== null;
    }
  },
  mounted() {
    this.choices = new Choices(this.$el.querySelector('#replacementTeacher'), {
      removeItemButton: true
    });
    this.ref = window.location;
  },
  watch: {
    entry: {
      deep: true,
      handler() {
        this.validate();
      }
    }
  },
  methods: {
    validate() {
      if(this.entry.start > this.entry.end) {
        this.validation.start = this.$trans('This value should be less than or equal to {{ compared_value }}.', { '{{ compared_value }}': this.entry.end }, 'validators');
      } else {
        this.validation.start = null;
      }

      if(this.entry.end < this.entry.start) {
        this.validation.end = this.$trans('This value should be greater than or equal to {{ compared_value }}.', { '{{ compared_value }}': this.entry.start }, 'validators');
      } else {
        this.validation.end = null;
      }

      if(this.entry.topic === null || this.entry.topic.trim() === '') {
        this.validation.topic = this.$trans('This value should not be blank.', {}, 'validators');
      } else if(this.entry.topic.length > 255) {
        this.validation.topic = this.$transChoice('This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.', 255, { }, 'validators').replace('{{ limit }}', 255);
      } else {
        this.validation.topic = null;
      }
    },
    add(start, end) {
      this.entry.start = start;
      this.entry.end = end;

      if(this.modal === null || this.modal === undefined) {
        let $this = this;
        let modalEl = this.$el.querySelector('.modal');
        this.modal = new Modal(modalEl);
        modalEl.addEventListener('shown.bs.modal', function() {
          console.log('shown!');
          $this.$el.querySelector('#topic').focus();
        });
      }

      let $this = this;

      // Load tuition and teachers
      if(this.isInitialized !== true) {
        this.isLoading = true;
        let tuitionRequest = this.$http.get(this.tuitionUrl);
        let teachersRequest = this.$http.get(this.teachersUrl);

        this.$http
            .all([tuitionRequest, teachersRequest])
            .then(this.$http.spread((...responses) => {
              let tuitionResponse = responses[0];
              let teachersResponse = responses[1];

              $this.tuition = tuitionResponse.data;

              let choices = [ ];
              let teachers = [ ];

              if($this.tuition !== null) {
                teachers = $this.tuition.teachers.map(t => t.uuid);
              }

              teachersResponse.data.forEach(function(teacher) {
                choices.push({
                  value: teacher.uuid,
                  label: $this.formatTeacher(teacher),
                  selected: $this.teacher === teacher.acronym && teachers.indexOf(teacher.uuid) === -1
                })
              });

              $this.choices.clearChoices();
              $this.choices.setChoices(choices, 'value', 'label', true);
            }))
            .catch(error => {
              console.error(error);
            })
            .finally(() => {
              $this.isLoading = false;
            });
      }

      this.modal.show();
      this.validate();
    },
    submit() {
      this.$el.querySelector('form').submit();
    },
    formatTeacher(teacher) {
      let saluation = teacher.gender === 'male' ? 'Herr' : (teacher.gender === 'female' ? 'Frau' : '');
      return `${teacher.acronym} (${saluation} ${teacher.lastname}}`;
    }
  }
}
</script>