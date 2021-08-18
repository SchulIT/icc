<template>
  <div>
    <div class="dropdown" v-if="start !== end">
      <button class="btn btn-primary btn-sm" type="button" data-toggle="dropdown" :title="$trans('book.entry.add.label')">
        <i class="fa fa-plus"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <button class="dropdown-item"
                @click="add(lesson, lesson)">
          <span class="badge badge-primary">
            {{ lesson }}.
          </span>
          {{ $trans('book.entry.add.single') }}
        </button>
        <button class="dropdown-item"
                @click="add(start, end)">
          <span class="badge badge-primary">
            {{ start }}./{{ end }}.
          </span>
          {{ $trans('book.entry.add.double') }}
        </button>
      </div>
    </div>

    <button class="btn btn-primary btn-sm"
            type="button" data-toggle="dropdown"
            :title="$trans('book.entry.add.label')"
            @click="add(lesson, lesson)"
            v-if="start === end">
      <i class="fa fa-plus"></i>
    </button>

    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <form :action="action" method="post">
            <div class="modal-header">
              <h5 class="modal-title">{{ $trans('book.entry.add.label') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group d-flex align-items-center">
                <i class="fas fa-spinner fa-spin" v-if="isLoadingTuition"></i>

                <span class="badge badge-secondary" v-if="tuition !== null">{{ tuition.subject.name.toUpperCase() }}</span>

                <div class="ml-2" v-if="tuition !== null">
                  {{ tuition.name }}
                </div>

                <div class="ml-2" v-if="tuition !== null" v-for="grade in tuition.study_group.grades">
                  <i class="fas fa-users"></i>
                  {{ grade.name }}
                </div>

                <div class="ml-2" v-for="teacher in tuition.teachers" v-if="tuition !== null">
                  <i class="fas fa-chalkboard-teacher"></i>
                  {{ teacher.acronym }}
                </div>

                <div class="ml-2">
                  <i class="fas fa-calendar-alt"></i> {{ date.toLocaleDateString() }}
                </div>
              </div>

              <div class="form-group row">
                <div class="col-6">
                  <label for="start" class="control-label">{{ $trans('label.start')}}</label>
                  <number-input v-model="entry.start" name="lesson_entry_create[lessonStart]" :min="start" :max="end" id="start" :class="validation.start !== null ? 'is-invalid' : ''"></number-input>
                  <div class="invalid-feedback" v-show="validation.start !== null">{{ validation.start }}</div>
                </div>
                <div class="col-6">
                  <label for="end" class="control-label">{{ $trans('label.end')}}</label>
                  <number-input v-model="entry.end" name="lesson_entry_create[lessonEnd]" :min="start" :max="end" id="end" :class="validation.end !== null ? 'is-invalid' : ''"></number-input>
                  <div class="invalid-feedback" v-show="validation.end !== null">{{ validation.end }}</div>
                </div>
              </div>

              <div class="form-group">
                <label for="topic" class="control-label">{{ $trans('label.topic') }}</label>
                <input v-model="entry.topic" name="lesson_entry_create[topic]" :class="'form-control ' + (validation.topic !== null ? 'is-invalid' : '')" id="topic">
                <div class="invalid-feedback" v-show="validation.topic !== null">{{ validation.topic }}</div>
              </div>

              <div class="form-group">
                <label for="exercises" class="control-label">{{ $trans('label.exercises') }}</label>
                <textarea v-model="entry.exercises" name="lesson_entry_create[exercises]" class="form-control" id="exercises"></textarea>
              </div>

              <div class="form-group">
                <label for="comment" class="control-label">{{ $trans('label.comment') }}</label>
                <textarea v-model="entry.comment" name="lesson_entry_create[comment]" class="form-control" id="comment"></textarea>
              </div>

              <div class="form-group">
                <label for="replacementSubject" class="control-label">{{ $trans('label.replacement_subject') }}</label>
                <input v-model="entry.replacementSubject" name="lesson_entry_create[replacementSubject]" class="form-control" id="replacementSubject">
              </div>

              <div class="form-group">
                <label for="replacementTeacher" class="control-label">{{ $trans('label.replacement_teacher') }}</label>
                <select name="lesson_entry_create[replacementTeacher]" class="form-control" id="replacementTeacher"></select>
              </div>

              <div class="form-group">
                <label for="absentStudents" class="control-label">{{ $trans('label.absent_students' )}}</label>
                <select name="lesson_entry_create[absentStudents][]" class="form-control" id="absentStudents" multiple="multiple"></select>
              </div>
            </div>

            <input type="hidden" name="date" :value="date.toJSON()">
            <input type="hidden" name="tuition" :value="tuitionUuid">
            <input type="hidden" :name="'lesson_entry_create[' + csrfname + ']'" :value="csrftoken">
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $trans('action.cancel') }}</button>
              <button type="button" class="btn btn-primary" @click.prevent="submit()" :disabled="!this.isValid">{{$trans('action.save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Dropdown, Modal } from 'bootstrap.native';
import NumberInput from "../NumberInput";
import Choices from "choices.js";

export default {
  name: 'lesson_cancel_button',
  components: { NumberInput },
  props: {
    tuitionUrl: String,
    studentsUrl: String,
    teachersUrl: String,
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
      isLoadingTuition: false,
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
      }
    }
  },
  computed: {
    isValid() {
      return this.validation.start === null
          && this.validation.end === null
          && this.validation.topic === null;
    }
  },
  mounted() {
    let dropdownElement = this.$el.querySelector('.dropdown');

    if(dropdownElement !== null) {
      this.dropdown = new Dropdown(dropdownElement, {persist: false});
    }

    this.choices = new Choices(this.$el.querySelector('#replacementTeacher'), {
      removeItemButton: true
    });
    this.students = new Choices(this.$el.querySelector('#absentStudents'), {
      removeItemButton: true,
      callbackOnCreateTemplates: function(template) {
        return {
          choice: (classNames, data) => {
            return template(`
              <div class="${classNames.item} ${classNames.itemChoice} ${data.disabled ? classNames.itemDisabled : classNames.itemSelectable}"
                   data-select-text="${this.config.itemSelectText}"
                   data-choice
                   ${data.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable'}
                   data-id="${data.id}"
                   data-value="${data.value}"
                   ${data.groupId > 0 ? 'role="treeitem"' : 'role="option"'}>
                <div>
                  <div>${data.label}</div>
                  <div class="text-muted">${data.customProperties.reasons}</div>
                </div>
              </div>
            `)
          },
          item: (classNames, data) => {
            return template(`
              <div class="${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable} ${data.placeholder ? classNames.placeholder : ''}"
                   data-item
                   data-id="${data.id}"
                   data-value="${data.value}"
                   ${data.active ? 'aria-selected="true"' : ''}
                   ${data.disabled ? 'aria-disabled="true"' : ''}>
                 <div class="d-flex"
                      data-id="${data.id}"
                      data-value="${data.value}">
                  <div>
                    <div>${data.label}</div>
                    <div>${data.customProperties.reasons}</div>
                  </div>
                  <button type="button" class="${classNames.button}" aria-label="Remove item: '${data.value}'" data-button="">Remove item</button>
                </div>
             </div>
            `)
          }
        }
      }
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
      } else {
        this.validation.topic = null;
      }
    },
    add(start, end) {
      this.entry.start = start;
      this.entry.end = end;

      if(this.modal === null || this.modal === undefined) {
        this.modal = new Modal(this.$el.querySelector('.modal'));
      }

      if(this.tuition === null) {
        this.isLoadingTuition = true;
        let $this = this;
        this.$http.get(this.tuitionUrl)
            .then(function(response) {
              $this.tuition = response.data;

              if($this.choices !== null) {
                let teachers = $this.tuition.teachers.map(t => t.uuid);
                let value = $this.choices.getValue();

                if(value !== null && value !== undefined && teachers.indexOf(value.value) !== -1) {
                  $this.choices.setValue([]);
                }
              }
            })
            .catch(function(error) {
              console.log(error);
            })
            .finally(function() {
              $this.isLoadingTuition = false;
            });
      }

      let $this = this;
      this.$http.get(this.teachersUrl)
        .then(function(response) {
          let choices = [ ];
          let teachers = [ ];

          if($this.tuition !== null) {
            teachers = $this.tuition.teachers.map(t => t.uuid);
          }

          response.data.forEach(function(teacher) {
            choices.push({
              value: teacher.uuid,
              label: teacher.acronym,
              selected: $this.teacher === teacher.acronym && teachers.indexOf(teacher.uuid) === -1
            })
          });

          $this.choices.setChoices(choices, 'value', 'label', true);
        })
        .catch(function(error) {
          console.log(error);
        });

      this.$http.get(this.studentsUrl)
        .then(function(response) {
          let students = { };

          response.data.students.forEach(function(student) {
            students[student.uuid] = {
              uuid: student.uuid,
              firstname: student.firstname,
              lastname: student.lastname,
              reasons: [ ]
            };
          });

          response.data.absent.forEach(function(absence) {
            if(absence.student.uuid in students) {
              students[absence.student.uuid].reasons.push(absence.reason);
            }
          });

          let choices = [ ];

          for(let uuid in students) {
            let student = students[uuid];
            choices.push({
              value: student.uuid,
              label: student.lastname + ", " + student.firstname,
              customProperties: {
                reasons: student.reasons.map(reason => $this.$trans('book.attendance.absence_reason.' + reason)).join(', ')
              },
              selected: student.reasons.length > 0
            })
          }

          $this.students.setChoices(choices, 'value', 'label', true);
        }).catch(function(error) {
          console.log(error);
        });

      this.modal.show();
      this.validate();
    },
    submit() {
      this.$el.querySelector('form').submit();
    }
  }
}
</script>