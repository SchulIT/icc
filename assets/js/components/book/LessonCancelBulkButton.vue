<template>
  <div>
    <div class="btn btn-outline-primary btn-sm"
         @click.prevent="openModal">
      <i class="fas fa-times"></i> {{ button }}
    </div>

    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $trans('book.entry.cancel.label') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3 d-flex align-items-center" v-for="tuition in tuitions">
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

            <div class="mb-3">
              <label for="reason" class="control-label">{{ $trans('book.entry.cancel.reason' )}} </label>
              <input v-model="reason" :class="'form-control ' + (validation.reason !== null ? 'is-invalid' : '')" id="reason">
              <div class="invalid-feedback" v-show="validation.reason !== null">{{ validation.reason }}</div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $trans('actions.cancel') }}</button>
            <button type="button" class="btn btn-danger" @click.prevent="submit()" :disabled="!isValid || isLoading">
              <i class="fas fa-spinner fa-spin" v-if="isLoading"></i>
              {{ $trans('book.entry.propose_cancel.button')}}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Modal from 'bootstrap/js/dist/modal';

export default {
  name: 'lesson_cancel_bulk_button',
  props: {
    csrftoken: String,
    tuitionUrls: Array,
    actions: Array,
    date: Date,
    button: String,
    prefillreason: String
  },
  data() {
    return {
      isLoading: false,
      progress: 0,
      reason: null,
      tuitions: [ ],
      validation: {
        reason: null
      }
    }
  },
  watch: {
    reason: {
      handler() {
        this.validate();
      }
    }
  },
  computed: {
    isValid() {
      return this.validation.reason === null;
    }
  },
  mounted() {
    let modalEl = this.$el.querySelector('.modal');
    this.modal = new Modal(modalEl);
    if(this.prefillreason !== null && this.reason === null) {
      this.reason = this.prefillreason;
    }

    modalEl.addEventListener('shown.bs.modal', function() {
      modalEl.querySelector('input').focus();
    });
  },
  methods: {
    validate() {
      if(this.reason === null || this.reason === undefined || this.reason.trim() === '') {
        this.validation.reason = this.$trans('This value should not be blank.', {}, 'validators');
      } else if(this.reason.length > 255) {
        this.validation.reason = this.$transChoice('This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.', 255, { }, 'validators').replace('{{ limit }}', 255);
      } else {
        this.validation.reason = null;
      }
    },
    openModal() {
      this.validate();
      this.tuitions.splice(0, this.tuitions.length);

      let $this = this;

      this.tuitionUrls
          .filter(function(value, index, self) {
            return self.indexOf(value) === index;
          })
          .forEach(function(url) {
            $this.$http.get(url)
              .then(function(response) {
                $this.tuitions.push(response.data);
              })
              .catch(function(error) {
                console.error(error);
              });
          })

      this.modal.show();
    },
    submit() {
      let requests = [ ];
      let $this = this;
      $this.isLoading = true;

      this.actions
          .filter(function(value, index, self) {
            return self.indexOf(value) === index;
          })
          .forEach(function(url) {
            let request = $this.$http.post(url, {
              '_token': $this.csrftoken,
              'reason': $this.reason
            });
            requests.push(request);
          });

      $this.$http.all(requests)
        .then(function() {

        })
        .catch(function(error) {
          console.error(error);
        })
        .finally(function() {
          $this.isLoading = false;
          location.reload();
        });
      }
    }
  };
</script>