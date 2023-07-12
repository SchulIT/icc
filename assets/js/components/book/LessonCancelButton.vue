<template>
  <div>
    <div class="dropdown" v-if="start !== end">
      <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="dropdown" :title="$trans('book.entry.cancel.label')">
        <i class="fa fa-times"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-end">
        <button class="dropdown-item"
                @click="cancel(lesson, lesson)">
          <span class="badge text-bg-primary">
            {{ lesson }}.
          </span>
          {{ $trans('book.entry.cancel.single') }}
        </button>
        <button class="dropdown-item"
                @click="cancel(start, end)">
          <span class="badge text-bg-primary">
            {{ start }}./{{ end }}.
          </span>
          {{ $trans('book.entry.cancel.double') }}
        </button>
      </div>
    </div>

    <button class="btn btn-primary btn-sm"
            type="button" data-bs-toggle="dropdown"
            :title="$trans('book.entry.cancel.label')"
            @click="cancel(lesson, lesson)"
            v-if="start === end">
      <i class="fa fa-times"></i>
    </button>

    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <form :action="action" method="post">
            <div class="modal-header">
              <h5 class="modal-title">{{ $trans('book.entry.cancel.label') }}</h5>
              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group d-flex align-items-center">
                <i class="fas fa-spinner fa-spin" v-if="isLoadingTuition"></i>

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

              <div class="form-group">
                <label for="start" class="control-label">{{ $trans('label.start')}}</label>
                <number-input v-model="entry.start" name="lesson_entry_cancel[lessonStart]" :min="start" :max="end" id="start" :class="validation.start !== null ? 'is-invalid' : ''"></number-input>
                <div class="invalid-feedback" v-show="validation.start !== null">{{ validation.start }}</div>
              </div>

              <div class="form-group">
                <label for="end" class="control-label">{{ $trans('label.end')}}</label>
                <number-input v-model="entry.end" name="lesson_entry_cancel[lessonEnd]" :min="start" :max="end" id="end" :class="validation.end !== null ? 'is-invalid' : ''"></number-input>
                <div class="invalid-feedback" v-show="validation.end !== null">{{ validation.end }}</div>
              </div>

              <div class="form-group">
                <label for="reason" class="control-label">{{ $trans('book.entry.cancel.reason' )}}</label>
                <input v-model="entry.reason" name="lesson_entry_cancel[cancelReason]" :class="'form-control ' + (validation.reason !== null ? 'is-invalid' : '')" id="reason">
                <div class="invalid-feedback" v-show="validation.reason !== null">{{ validation.reason }}</div>
              </div>

              <div class="form-group">
                <label for="exercises" class="control-label">{{ $trans('label.exercises') }}</label>
                <textarea v-model="entry.exercises" name="lesson_entry_cancel[exercises]" class="form-control" id="exercises"></textarea>
              </div>
            </div>

            <input type="hidden" name="date" :value="date.toJSON()">
            <input type="hidden" name="tuition" :value="tuitionUuid">
            <input type="hidden" :name="'lesson_entry_cancel[' + csrfname + ']'" :value="csrftoken">
            <input type="hidden" name="_ref" :value="ref">
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $trans('actions.cancel') }}</button>
              <button type="button" class="btn btn-primary" @click.prevent="submit()" :disabled="!this.isValid">{{$trans('actions.save')}}</button>
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

export default {
  name: 'lesson_cancel_button',
  components: { NumberInput },
  props: {
    tuitionUrl: String,
    date: Date,
    start: Number,
    end: Number,
    tuitionUuid: String,
    lesson: Number,
    csrftoken: String,
    csrfname: String,
    action: String
  },
  data() {
    return {
      isLoadingTuition: false,
      tuition: null,
      validation: {
        start: null,
        end: null,
        reason: null
      },
      entry: {
        tuition: this.tuition,
        start: this.start,
        end: this.end,
        date: this.date,
        reason: null,
        exercises: null
      },
      ref: null
    }
  },
  computed: {
    isValid() {
      return this.validation.start === null
          && this.validation.end === null
          && this.validation.reason === null;
    }
  },
  mounted() {
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

      if(this.entry.reason === null || this.entry.reason.trim() === '') {
        this.validation.reason = this.$trans('This value should not be blank.', {}, 'validators');
      } else {
        this.validation.reason = null;
      }
    },
    cancel(start, end) {
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

      if(this.tuition === null) {
        this.isLoadingTuition = true;
        let $this = this;
        this.$http.get(this.tuitionUrl)
          .then(function(response) {
            $this.tuition = response.data;
          })
          .catch(function(error) {
            console.log(error);
          })
          .finally(function() {
            $this.isLoadingTuition = false;
          });
      }

      this.modal.show();
      this.validate();
    },
    submit() {
      this.$el.querySelector('form').submit();
    }
  }
}
</script>