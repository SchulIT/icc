<template>
  <form method="post">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <div class="flex-fill">
          <i class="fas fa-users"></i> Lernende
          <span class="badge badge-primary"
                v-if="selectedAttendances.length > 0">
              {{ selectedAttendances.length }} ausgewählt
          </span>
        </div>
        <div v-if="selectedAttendances.length > 0">
          <button class="btn btn-outline-primary btn-sm ml-1 d-inline-block"
                  @click.prevent="unselect()">
            <i class="far fa-check-square"></i>
          </button>

          <button class="btn btn-outline-success btn-sm ml-1 d-inline-block"
                  @click.prevent="present(selectedAttendances)">
            <i class="fas fa-user-check"></i>
          </button>
          <button class="btn btn-outline-warning btn-sm ml-1"
                  @click.prevent="late(selectedAttendances)">
            <i class="fas fa-user-clock"></i>
          </button>
          <div class="btn-group d-inline-flex align-items-center ml-1">
            <button class="btn btn-outline-warning btn-sm"
                    @click.prevent="plus(selectedAttendances)">
              <i class="fa fa-plus"></i>
            </button>
            <button class="btn btn-outline-warning btn-sm"
                    @click.prevent="minus(selectedAttendances)">
              <i class="fa fa-minus"></i>
            </button>
          </div>
          <button class="btn btn-outline-danger btn-sm ml-1  d-inline-block"
                  @click.prevent="absent(selectedAttendances)">
            <i class="fas fa-user-times"></i>
          </button>
        </div>
        <div>
          <button class="btn btn-success btn-sm ml-1"
                  @click.prevent="present(attendances)"
                  title="alle anwesend">
            <i class="fas fa-check-double"></i>
          </button>

          <button class="btn btn-primary btn-sm ml-1"
                  :class="{ 'btn-danger': isDirty }"
                  id="attendance_submit"
                  type="submit" title="Anwesenheit speichern">
            <i class="fa fa-save"></i>
            <i class="fa fa-exclamation-triangle ml-1" v-if="isDirty"></i>
          </button>
        </div>
      </div>

      <div class="card-body">
        <i class="fa fa-check"></i> {{ numPresent }} anwesend
        <i class="fa fa-clock"></i> {{ numLate }} verspätet
        <i class="fa fa-times"></i> {{ numAbsent }} abwesend
      </div>

      <div class="list-group list-group-flush">
        <div class="list-group-item align-items-center p-0"
             v-for="attendance in attendances"
             :class="{ 'bg-selected': selectedAttendances.indexOf(attendance) >= 0 }">
          <input type="hidden" :name="'attendances[attendances][' + attendances.indexOf(attendance) + '][type]'" :value="attendance.type">
          <input type="hidden" :name="'attendances[attendances][' + attendances.indexOf(attendance) + '][lateMinutes]'" :value="attendance.minutes">

          <div class="d-flex">
            <div class="flex-fill p-3 pointer"
                 @click="select(attendance)">
              <i class="fa fa-user"></i> {{ attendance.student.lastname }}, {{ attendance.student.firstname }}
            </div>
            <div class="mr-2 align-self-center">
              <a href="#" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-comment"></i>
              </a>
            </div>
            <div class="align-self-center mr-3">
              <button class="btn btn-outline-success btn-sm ml-1 d-inline-block"
                      @click.prevent="present(attendance)"
                      :class="{ active: attendance.type === 1}"> <i class="fas fa-user-check"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm ml-1"
                      :class="{ active: attendance.type === 2}"
                      @click.prevent="late(attendance)">
                <i class="fas fa-user-clock"></i>
              </button>
              <div class="btn-group d-inline-flex align-items-center ml-1"
                   v-if="attendance.type === 2">
                <button class="btn btn-outline-warning btn-sm"
                        @click.prevent="minus(attendance)">
                  <i class="fa fa-minus"></i>
                </button>
                <span class="border-top border-bottom border-warning align-self-stretch align-items-center d-flex px-2">
                  <span>{{ attendance.minutes }} min</span>
                </span>
                <button class="btn btn-outline-warning btn-sm"
                        @click.prevent="plus(attendance)">
                  <i class="fa fa-plus"></i>
                </button>
              </div>
              <button class="btn btn-outline-danger btn-sm ml-1 d-inline-block"
                     :class="{ active: attendance.type === 0}"
                     @click.prevent="absent(attendance)">
                <i class="fas fa-user-times"></i>
              </button>
            </div>
          </div>


        </div>
      </div>
    </div>

    <input type="hidden" :name="csrfname" :value="csrftoken">
  </form>
</template>

<script>
export default {
  name: 'attendances',
  props: {
    attendancedata: Array,
    commentsdata: Array,
    step: Number,
    csrftoken: String,
    csrfname: String
  },
  data() {
    return {
      isDirty: false,
      attendances: this.attendancedata,
      comments: this.commentsdata,
      selectedAttendances: [ ]
    }
  },
  watch: {
    attendances: {
      deep: true,
      handler() {
        this.isDirty = true;
      }
    }
  },
  beforeMount() {
    window.addEventListener('beforeunload', this.preventReload);
  },
  beforeUnmount() {
    window.removeEventListener('beforeunload', this.preventReload);
  },
  computed: {
    allPresent() {
      return this.numPresent === this.attendances.length;
    },
    numPresent() {
      let count = 0;
      this.attendances.forEach(function(attendance) {
        if(attendance.type === 1) {
          count++;
        }
      });

      return count;
    },
    numLate() {
      let count = 0;
      this.attendances.forEach(function(attendance) {
        if(attendance.type === 2) {
          count++;
        }
      });

      return count;
    },
    numAbsent() {
      let count = 0;
      this.attendances.forEach(function(attendance) {
        if(attendance.type === 0) {
          count++;
        }
      });

      return count;
    },
    commentsByStudent(attendance) {
      console.log(attendance);
    }
  },
  methods: {
    select(attendance) {
      let idx = this.selectedAttendances.indexOf(attendance);
      if(idx === -1) {
        this.selectedAttendances.push(attendance);
      } else {
        this.selectedAttendances.splice(idx, 1);
      }
    },
    unselect() {
      this.selectedAttendances.splice(0, this.selectedAttendances.length);
    },
    late(attendance) {
      this.setType(attendance, 2);
    },
    absent(attendance) {
      this.setType(attendance, 0);
      this.apply(attendance, function(attendance) {
        attendance.minutes = 0;
      });
    },
    present(attendance) {
      this.setType(attendance, 1);
      this.apply(attendance, function(attendance) {
        attendance.minutes = 0;
      });
    },
    setType(attendanceOrAttendances, type) {
      this.apply(attendanceOrAttendances, function(attendance) {
        attendance.type = type;
      });
    },
    apply(attendanceOrAttendances, callback) {
      if(attendanceOrAttendances instanceof Array) {
        attendanceOrAttendances.forEach(function(attendance) {
          callback(attendance);
        });
      } else {
        callback(attendanceOrAttendances);
      }
    },
    plus(attendanceOrAttendances) {
      let step = this.step;
      this.apply(attendanceOrAttendances, function(attendance) {
        attendance.minutes += step;
      });
      this.setType(attendanceOrAttendances, 2);
    },
    minus(attendanceOrAttendances) {
      let step = this.step;
      this.apply(attendanceOrAttendances, function(attendance) {
        if(attendance.minutes > 0) {
          attendance.minutes -= step;
        }
      });
      this.setType(attendanceOrAttendances, 2);
    },
    preventReload(event) {
      let isButtonOrChild = event.explicitOriginalTarget instanceof HTMLElement;
      let closestAttendanceSubmitButton = null;

      if(isButtonOrChild) {
        closestAttendanceSubmitButton = event.explicitOriginalTarget.closest('#attendance_submit');
      }

      if(this.isDirty && (isButtonOrChild === false || closestAttendanceSubmitButton === null)) {
        event.preventDefault();
      }
    }
  }
}
</script>