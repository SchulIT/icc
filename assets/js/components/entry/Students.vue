<template>
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <div class="flex-fill">
          <i class="fas fa-users"></i> {{ $trans('book.attendance.label') }}
          <span class="badge badge-primary"
                v-if="selectedAttendances.length > 0">
            {{ $trans('book.attendance.selected', { number: selectedAttendances.length })}}
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
                    @click.prevent="plusMinute(selectedAttendances)">
              <i class="fa fa-plus"></i>
            </button>
            <button class="btn btn-outline-warning btn-sm"
                    @click.prevent="minusMinute(selectedAttendances)">
              <i class="fa fa-minus"></i>
            </button>
          </div>
          <button class="btn btn-outline-danger btn-sm ml-1  d-inline-block"
                  @click.prevent="absent(selectedAttendances)">
            <i class="fas fa-user-times"></i>
          </button>
          <div class="btn-group d-inline-flex align-items-center ml-1">
            <button class="btn btn-outline-danger btn-sm"
                    @click.prevent="plusLesson(selectedAttendances)">
              <i class="fa fa-plus"></i>
            </button>
            <button class="btn btn-outline-danger btn-sm"
                    @click.prevent="minusLesson(selectedAttendances)">
              <i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div>
          <button class="btn btn-success btn-sm ml-1"
                  @click.prevent="present(attendances)"
                  :title="$trans('book.attendance.all')">
            <i class="fas fa-check-double"></i>
          </button>

          <button class="btn btn-primary btn-sm ml-1"
                  :class="{ 'btn-danger': isDirty }"
                  id="attendance_submit"
                  type="submit" :title="$trans('actions.save')">
            <i class="fa fa-save"></i>
            <i class="fa fa-exclamation-triangle ml-1" v-if="isDirty"></i>
          </button>
        </div>
      </div>

      <div class="card-body">
        <i class="fa fa-check"></i> {{ $trans('book.attendance.overview.present', { 'number': numPresent })}}
        <i class="fa fa-clock"></i> {{ $trans('book.attendance.overview.late', { 'number': numLate })}}
        <i class="fa fa-times"></i> {{ $trans('book.attendance.overview.absent', { 'number': numAbsent })}}
      </div>

      <div class="card-body border-top" v-if="absences.length > 0">
        <ul class="list-unstyled mb-0">
          <li v-for="absence in absences" class="d-flex align-items-start">
            <div class="flex-fill">
              <div>
                {{ absence.lastname }}, {{ absence.firstname }}
              </div>
              <span v-for="reason in absence.reasons" class="badge badge-primary">{{ reason }}</span>
            </div>
            <button type="button"
                    @click="applyAbsence(absence)"
                    class="btn btn-sm btn-outline-primary">{{ $trans('actions.apply')}}</button>
          </li>
        </ul>
      </div>

      <div class="list-group list-group-flush">
        <div class="list-group-item align-items-center p-0"
             v-for="attendance in attendances"
             :class="{ 'bg-selected': selectedAttendances.indexOf(attendance) >= 0 }">
          <input type="hidden" :name="'lesson_entry[attendances][' + originalAttendances.indexOf(attendance) + '][type]'" :value="attendance.type">
          <input type="hidden" :name="'lesson_entry[attendances][' + originalAttendances.indexOf(attendance) + '][lateMinutes]'" :value="attendance.minutes">
          <input type="hidden" :name="'lesson_entry[attendances][' + originalAttendances.indexOf(attendance) + '][absentLessons]'" :value="attendance.lessons">

          <div class="d-flex">
            <div class="flex-fill p-3 pointer"
                 @click="select(attendance)">
              <i class="fa fa-user"></i> {{ attendance.student.lastname }}, {{ attendance.student.firstname }}
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
                        @click.prevent="minusMinute(attendance)">
                  <i class="fa fa-minus"></i>
                </button>
                <span class="border-top border-bottom border-warning align-self-stretch align-items-center d-flex px-2">
                  <span>{{ $trans('book.attendance.late_minutes', { '%count%': attendance.minutes }) }}</span>
                </span>
                <button class="btn btn-outline-warning btn-sm"
                        @click.prevent="plusMinute(attendance)">
                  <i class="fa fa-plus"></i>
                </button>
              </div>
              <button class="btn btn-outline-danger btn-sm ml-1 d-inline-block"
                     :class="{ active: attendance.type === 0}"
                     @click.prevent="absent(attendance)">
                <i class="fas fa-user-times"></i>
              </button>

              <div class="btn-group d-inline-flex align-items-center ml-1"
                   v-if="attendance.type === 0">
                <button class="btn btn-outline-danger btn-sm"
                        @click.prevent="minusLesson(attendance)">
                  <i class="fa fa-minus"></i>
                </button>
                <span class="border-top border-bottom border-danger align-self-stretch align-items-center d-flex px-2">
                  <span>{{ $transChoice('book.attendance.absence_lesson', attendance.lessons, {'%count%': attendances.lessons })}}</span>
                </span>
                <button class="btn btn-outline-danger btn-sm"
                        @click.prevent="plusLesson(attendance)">
                  <i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  name: 'attendances',
  props: {
    attendancedata: Array,
    step: Number,
    studentsUrl: String,
    start: Number,
    end: Number
  },
  data() {
    return {
      isDirty: false,
      originalAttendances: this.attendancedata.slice(0, this.attendancedata.length),
      attendances: this.attendancedata,
      selectedAttendances: [ ],
      absences: [ ]
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
    this.attendancedata.sort(function(a, b) {
      let studentA = a.student.lastname + ", " + a.student.firstname;
      let studentB = b.student.lastname + ", " + b.student.firstname;
      return studentA.localeCompare(studentB, 'de', { sensitivity: 'base', numeric: true });
    });
  },
  beforeUnmount() {
    window.removeEventListener('beforeunload', this.preventReload);
  },
  mounted() {
    let $this = this;
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
              students[absence.student.uuid].reasons.push($this.$trans('book.attendance.absence_reason.' + absence.reason));
            }
          });

          $this.absences = [ ];

          for(let uuid in students) {
            let student = students[uuid];

            let attendance = $this.attendancedata.filter(x => x.student.uuid === uuid).map(x => x.type);

            if(student.reasons.length > 0 && attendance.length > 0 && attendance[0] !== 0) {
              $this.absences.push(student);
            }
          }
        }).catch(function(error) {
          console.log(error);
        });
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
    applyAbsence(absence) {
      let $this = this;
      this.attendancedata.forEach(function(attendance) {
        if(attendance.student.uuid === absence.uuid) {
          $this.absent(attendance);
          $this.absences.splice($this.absences.indexOf(absence), 1);
        }
      });
    },
    absent(attendance) {
      let $this = this;
      this.setType(attendance, 0);
      this.apply(attendance, function(attendance) {
        attendance.lessons = $this.end - $this.start + 1;
        attendance.minutes = 0;
      });
    },
    present(attendance) {
      this.setType(attendance, 1);
      this.apply(attendance, function(attendance) {
        attendance.minutes = 0;
        attendance.lessons = 0;
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
    plusMinute(attendanceOrAttendances) {
      let step = this.step;
      this.apply(attendanceOrAttendances, function(attendance) {
        attendance.minutes += step;
      });
      this.setType(attendanceOrAttendances, 2);
    },
    minusMinute(attendanceOrAttendances) {
      let step = this.step;
      this.apply(attendanceOrAttendances, function(attendance) {
        if(attendance.minutes > 0) {
          attendance.minutes -= step;
        }
      });
      this.setType(attendanceOrAttendances, 2);
    },
    plusLesson(attendanceOrAttendances) {
      let $this = this;
      this.apply(attendanceOrAttendances, function(attendance) {
        if(attendance.lessons <= ($this.end - $this.start)) {
          attendance.lessons++;
        }
      });
    },
    minusLesson(attendanceOrAttendances) {
      this.apply(attendanceOrAttendances, function(attendance) {
        if(attendance.lessons > 0) {
          attendance.lessons--;
        }
      });
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