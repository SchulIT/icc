<template>
  <div v-if="actions.length > 0">
    <div class="btn btn-danger btn-sm"
         @click.prevent="openModal">
      <i class="fas fa-trash"></i> {{ $trans('admin.messages.bulk_remove.label') }}
    </div>

    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $trans('admin.messages.bulk_remove.label') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <p>{{ $transChoice('admin.messages.bulk_remove.confirm', actions.length, { '%count%': actions.length })}}</p>

            <div class="progress" v-if="actions.length > 0 && isLoading">
              <div class="progress-bar" role="progressbar" :style="{ width: (progress / actions.length * 100) + '%' }">
                {{ Math.round(progress / actions.length * 100) }}%
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $trans('action.cancel') }}</button>
            <button type="button" class="btn btn-danger" @click.prevent="submit()" :disabled="isLoading">
              <i class="fas fa-spinner fa-spin" v-if="isLoading"></i>
              {{ $trans('actions.remove')}}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap.native';

export default {
  name: 'bulk_remove_button',
  props: {
    csrftoken: String,
    actions: Array
  },
  data() {
    return {
      isLoading: false,
      progress: 0
    }
  },
  mounted() {
    if(this.actions.length > 0) {
      let modalEl = this.$el.querySelector('.modal');
      this.modal = new Modal(modalEl);
    }
  },
  methods: {
    openModal() {
      this.modal.show();
    },
    submit() {
      let requests = [ ];
      let $this = this;
      $this.isLoading = true;
      $this.progress = 0;

      this.actions
          .filter(function(value, index, self) {
            return self.indexOf(value) === index;
          })
          .forEach(function(url) {
            let request = $this.$http.post(url, {
              '_token': $this.csrftoken
            });
            request.then(function() {
              $this.progress++;
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