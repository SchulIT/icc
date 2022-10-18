<template>
  <div class="list-group-item d-flex align-items-center">
    <div class="book-lesson m-0 p-0">
      <span>{{ lessonStart }}</span>
      <span v-if="lessonEnd > lessonStart"><br>{{ lessonEnd }}</span>
    </div>

    <div class="d-flex align-items-center flex-fill">
      <div class="flex-fill">
        <span class="badge badge-primary">{{ tuition }}</span>
        {{ teacher }}
      </div>
      <div class="flex-fill text-right">
        <div>
        <div class="btn-group d-inline-flex align-items-center ml-1"
             :title="$trans('book.attendance.late')"
             v-if="attendance.type === 2">
          <button class="btn btn-outline-warning btn-sm"
                  @click.prevent="minusMinute()">
            <i class="fa fa-minus"></i>
          </button>
          <span class="border-top border-bottom border-warning align-self-stretch align-items-center d-flex px-2">
                  <span>{{ $transChoice('book.attendance.late_minutes', attendance.minutes, { '%count%': attendance.minutes }) }}</span>
                </span>
          <button class="btn btn-outline-warning btn-sm"
                  @click.prevent="plusMinute()">
            <i class="fa fa-plus"></i>
          </button>
        </div>

        <div class="btn-group d-inline-flex align-items-center ml-1"
             :title="$trans('book.attendance.absent_lessons')"
             v-if="attendance.type === 0">
          <button class="btn btn-outline-danger btn-sm"
                  @click.prevent="minusLesson()">
            <i class="fa fa-minus"></i>
          </button>
          <span class="border-top border-bottom border-danger align-self-stretch align-items-center d-flex px-2">
                  <span>{{ attendance.lessons }}</span>
                </span>
          <button class="btn btn-outline-danger btn-sm"
                  @click.prevent="plusLesson()">
            <i class="fa fa-plus"></i>
          </button>
        </div>

        <div class="btn-group d-inline-flex align-items-center ml-1"
             v-if="attendance.type === 0">
          <button class="btn btn-outline-danger btn-sm"
                  :title="hasExcuses ? $trans('book.students.excuse_note_exists') : $trans('book.students.not_set')"
                  :class="{ active: attendance.excuse_status === 0}"
                  :disabled="hasExcuses"
                  @click.prevent="setExcuseStatus(0)">
            <i class="fas fa-question"></i>
          </button>
          <button class="btn btn-outline-danger btn-sm"
                  :title="$trans('book.students.excused')"
                  :class="{ active: attendance.excuse_status === 1 || hasExcuses }"
                  @click.prevent="setExcuseStatus(1)">
            <i class="fas fa-check"></i>
          </button>
          <button class="btn btn-outline-danger btn-sm"
                  :title="hasExcuses ? $trans('book.students.excuse_note_exists') : $trans('book.students.not_excused')"
                  :class="{ active: attendance.excuse_status === 2}"
                  :disabled="hasExcuses"
                  @click.prevent="setExcuseStatus(2)">
            <i class="fas fa-times"></i>
          </button>
        </div>
        </div>

        <div class="input-group input-group-sm d-inline-flex mt-1 w-auto">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="far fa-comment-alt"></i></span>
          </div>
          <input type="text" v-model="attendance.comment" class="form-control">
        </div>
      </div>

      <div class="ml-2">
        <button type="button" class="btn btn-primary btn-sm" @click.prevent="save">
          <i class="fas fa-save" v-if="!isSaving && !isSaved"></i>
          <i class="fas fa-spinner fa-spin" v-if="isSaving"></i>
          <i class="fas fa-check" v-if="isSaved"></i>
        </button>
      </div>
      <div class="ml-2">
        <button type="button" class="btn btn-primary btn-sm" title="coming soon 😉" disabled>
          <i class="fas fa-book-open"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'attendance',
  props: {
    uuid: String,
    subject: String,
    teacher: String,
    tuition: String,
    lessonStart: Number,
    lessonEnd: Number,
    absentLessons: Number,
    lateMinutes: Number,
    excuseStatus: Number,
    type: Number,
    comment: String,
    step: Number,
    hasExcuses: Boolean,
    url: String,
    csrftoken: String
  },
  mounted() {
    this.attendance.lessons = this.absentLessons;
    this.attendance.minutes = this.lateMinutes;
    this.attendance.type = this.type;
    this.attendance.comment = this.comment;
    this.attendance.excuse_status = this.excuseStatus;
  },
  data() {
    return {
      isSaving: false,
      isSaved: false,
      attendance: {
        lessons: 0,
        minutes: 0,
        excuse_status: 0,
        type: 0,
        comment: null
      }
    }
  },
  methods: {
    save() {
      let $this = this;
      let data = {
        '_token': $this.csrftoken,
        'absent_lessons': $this.attendance.lessons,
        'late_minutes': $this.attendance.minutes,
        'type': $this.attendance.type,
        'excuse_status': $this.attendance.excuse_status,
        'comment': $this.attendance.comment
      };

      if(data.comment !== null && data.comment.trim().length === 0) {
        data.comment = null;
      }

      $this.isSaving = true;

      $this.$http
          .put(this.url, data)
          .then(function() {
            $this.isSaving = false;
            $this.isSaved = true;

            setTimeout(function() {
              $this.isSaved = false;
            }, 3000);
          })
          .catch(function(error) {
            console.log(error);
          });
    },
    plusMinute() {
      this.attendance.minutes += step;
    },
    minusMinute() {
      this.attendance.minutes -= step;

      if(this.attendance.minutes < 0) {
        this.attendance.minutes = 0;
      }
    },
    plusLesson() {
      if(this.attendance.lessons <= (this.lessonEnd - this.lessonStart)) {
        this.attendance.lessons++;
      }
    },
    minusLesson() {
      if(this.attendance.lessons > 0) {
        this.attendance.lessons--;
      }
    },
    setExcuseStatus(status) {
      this.attendance.excuse_status = status;
    },
    absent() {
      this.attendance.type = 0;
    },
    present() {
      this.attendance.type = 1;
    },
    late() {
      this.attendance.type = 2;
    }
  }
}
</script>