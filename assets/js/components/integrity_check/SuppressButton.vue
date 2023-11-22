<template>
  <button class="btn btn-outline-primary btn-sm" type="button"
          @click="save()">
    <i class="fas fa-eye" v-if="isLoading === false && isSuppressed === true"></i>
    <i class="fas fa-eye-slash" v-if="isLoading === false && isSuppressed === false"></i>
    <i class="fa-solid fa-spinner fa-spin" v-if="isLoading === true"></i>
  </button>
</template>
<script>


export default {
  name: 'suppress_button',
  props: {
    url: String,
    csrftoken: String,
    csrfname: String,
    state: Boolean
  },
  data() {
    return {
      isLoading: false,
      isSuppressed: false
    }
  },
  mounted() {
    this.isSuppressed = this.state;
  },
  methods: {
    async save() {
      try {
        this.isLoading = true;
        let request = new FormData();
        request.append(this.csrfname, this.csrftoken);

        let response = await this.$http.post(this.url, request, {
          headers: {'X-Requested-With': 'XMLHttpRequest'},
        });

        this.isSuppressed = response.data.is_suppressed;

        this.isLoading = false;
      } catch (e) {
        console.error(e);
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>