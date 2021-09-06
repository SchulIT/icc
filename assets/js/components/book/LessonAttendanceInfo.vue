<template>
  <div class="dropdown">
    <button type="button" data-toggle="dropdown" :class="this.class" :title="title">
      <span v-if="isLoading">
        <i class="fas fa-spinner fa-spin"></i>
      </span>
      <span v-else>
        <i :class="icon"></i> {{ attendances.length }}
      </span>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
      <span class="dropdown-item-text" v-for="attendance in attendances">
        {{ attendance.student.lastname }}, {{ attendance.student.firstname }}
      </span>
    </div>
  </div>
</template>

<script>
import { Dropdown } from 'bootstrap.native';

export default {
  name: 'lesson_attendance_info',
  props: {
    url: String,
    icon: String,
    class: String,
    title: String
  },
  data() {
    return {
      isLoading: true,
      attendances: [ ]
    }
  },
  mounted() {
    let $this = this;
    this.$http
        .get(this.url)
        .then(function(response) {
          $this.attendances = response.data;
          new Dropdown(
              $this.$el.querySelector('button')
          );
        })
        .catch(function(error) {
            console.error(error);
        })
        .finally(function() {
          $this.isLoading = false;
        });
  },
  methods: {

  }
}
</script>