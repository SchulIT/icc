<template>
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <div class="flex-fill">
          <i class="fas fa-users"></i> {{ $trans('book.attendance.label') }}
        </div>

        <div>
          <button class="btn btn-success btn-sm ml-1"
                  @click.prevent="present(attendances)"
                  :title="$trans('book.attendance.all')">
            <i class="fas fa-check-double"></i>
          </button>

          <button class="btn btn-primary btn-sm ml-1"
                  :class="{ 'btn-danger': isDirty }"
                  v-if="showSaveButton"
                  id="attendance_submit"
                  type="submit">
            <i class="fa fa-save"></i> {{ $trans('actions.save')}}
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

      <div class="card-footer">
        <div class="d-flex align-items-stretch">
          <div class="flex-fill">
            <select id="add_student"></select>
          </div>
          <button type="button" class="btn btn-outline-primary btn-sm ml-2" @click="onAddStudent()">
            <i class="fas fa-user-plus"></i>
            {{ $trans('book.entry.add_student.label')}}
          </button>
        </div>
      </div>

      <div class="card-footer d-flex align-items-center" v-if="selectedAttendances.length > 0">
        <div class="flex-fill">
          <span class="badge badge-primary">
            {{ $trans('book.attendance.selected', { number: selectedAttendances.length })}}
          </span>
        </div>
        <div>
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

      </div>

      <div class="list-group list-group-flush">
        <div class="list-group-item align-items-center p-0"
             v-for="attendance in attendances"
             :class="{ 'bg-selected': selectedAttendances.indexOf(attendance) >= 0 }">

          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][type]'" :value="attendance.type">
          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][excuseStatus]'" :value="attendance.excuse_status">
          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][lateMinutes]'" :value="attendance.minutes">
          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][absentLessons]'" :value="attendance.lessons">
          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][student]'" :value="attendance.student.uuid">

          <div class="d-flex">
            <div class="flex-fill p-3 pointer flex-grow-1"
                 @click="select(attendance)">
              <i class="fa fa-user"></i> {{ attendance.student.lastname }}, {{ attendance.student.firstname }}
            </div>
            <div class="align-self-center text-right mr-3 flex-shrink-1">
              <button class="btn btn-outline-primary btn-sm ml-1 d-inline-block"
                      v-if="attendance.type !== 1"
                      @click.prevent="attendance.showComment !== true ? attendance.showComment = true : attendance.showComment = false"
                      :title="$trans('label.comment')">
                <i class="far fa-comment-alt"></i>
              </button>

              <button class="btn btn-outline-success btn-sm ml-1 d-inline-block"
                      @click.prevent="present(attendance)"
                      :title="$trans('book.attendance.type.present')"
                      :class="{ active: attendance.type === 1}"> <i class="fas fa-user-check"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm ml-1"
                      :class="{ active: attendance.type === 2}"
                      :title="$trans('book.attendance.type.late')"
                      @click.prevent="late(attendance)">
                <i class="fas fa-user-clock"></i>
              </button>
              <div class="btn-group d-inline-flex align-items-center ml-1"
                   :title="$trans('book.attendance.late')"
                   v-if="attendance.type === 2">
                <button class="btn btn-outline-warning btn-sm"
                        @click.prevent="minusMinute(attendance)">
                  <i class="fa fa-minus"></i>
                </button>
                <span class="border-top border-bottom border-warning align-self-stretch align-items-center d-flex px-2">
                  <span>{{ $transChoice('book.attendance.late_minutes', attendance.minutes, { '%count%': attendance.minutes }) }}</span>
                </span>
                <button class="btn btn-outline-warning btn-sm"
                        @click.prevent="plusMinute(attendance)">
                  <i class="fa fa-plus"></i>
                </button>
              </div>
              <button class="btn btn-outline-danger btn-sm ml-1 d-inline-block"
                     :class="{ active: attendance.type === 0}"
                     :title="$trans('book.attendance.type.absent')"
                     @click.prevent="absent(attendance)">
                <i class="fas fa-user-times"></i>
              </button>

              <div class="btn-group d-inline-flex align-items-center ml-1"
                   :title="$trans('book.attendance.absent_lessons')"
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

              <div class="btn-group d-inline-flex align-items-center ml-1"
                   v-if="attendance.type === 0">
                <button class="btn btn-outline-danger btn-sm"
                        :title="$trans('book.students.not_set')"
                        :class="{ active: attendance.excuse_status === 0}"
                        @click.prevent="setExcuseStatus(attendance, 0)">
                  <i class="fas fa-question"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm"
                        :title="$trans('book.students.excused')"
                        :class="{ active: attendance.excuse_status === 1}"
                        @click.prevent="setExcuseStatus(attendance, 1)">
                  <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm"
                        :title="$trans('book.students.not_excused')"
                        :class="{ active: attendance.excuse_status === 2}"
                        @click.prevent="setExcuseStatus(attendance, 2)">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="d-flex px-3 pb-3" v-if="attendance.type !== 1 && (attendance.comment !== null || attendance.showComment === true)">
            <div class="w-100">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="far fa-comment-alt"></i></span>
                </div>
                <input type="text" v-model="attendance.comment" class="form-control" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][comment]'">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
import Choices from "choices.js";

export default {
  name: 'attendances',
  props: {
    students: Array,
    possibleAbsences: Array,
    attendances: Array,
    step: Number,
    start: Number,
    end: Number,
    listStudentsUrl: String,
    showSaveButton: Boolean
  },
  data() {
    return {
      isDirty: false,
      //originalAttendances: this.attendancedata.slice(0, this.attendancedata.length),
      //attendances: this.attendancedata,
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
    },
    absences: {
      handler() {

      }
    }
  },
  beforeMount() {
    window.addEventListener('beforeunload', this.preventReload);
    this.attendances.sort(function(a, b) {
      let studentA = a.student.lastname + ", " + a.student.firstname;
      let studentB = b.student.lastname + ", " + b.student.firstname;
      return studentA.localeCompare(studentB, 'de', { sensitivity: 'base', numeric: true });
    });
  },
  beforeUnmount() {
    window.removeEventListener('beforeunload', this.preventReload);
  },
  mounted() {
    this.studentChoice = new Choices(this.$el.querySelector('#add_student'));
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
      this.attendances.forEach(function(attendance) {
        if(attendance.student.uuid === absence.uuid) {
          $this.absent(attendance);
          attendance.comment = absence.reasons.join(', ');
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
    setExcuseStatus(attendanceOrAttendances, status) {
      this.apply(attendanceOrAttendances, function(attendance) {
        attendance.excuse_status = status;
      });
    },
    addStudent(uuid, firstname, lastname, email) {
      let attendance = {
        uuid: '',
        comment: null,
        excuse_status: 0,
        lessons: 0,
        minutes: 0,
        type: 1,
        student: {
          uuid: uuid,
          firstname: firstname,
          lastname: lastname,
          email: email
        }
      };

      this.attendances.push(attendance);
    },
    onAddStudent() {
      let student = this.studentChoice.getValue();
      this.addStudent(student.value, student.customProperties.firstname, student.customProperties.lastname, student.customProperties.email);
    },
    preventReload(event) {
      if(this.showSaveButton === false) {
        return;
      }

      let isButtonOrChild = event.explicitOriginalTarget instanceof HTMLElement;
      let closestAttendanceSubmitButton = null;

      if(isButtonOrChild) {
        closestAttendanceSubmitButton = event.explicitOriginalTarget.closest('#attendance_submit');
      }

      if(this.isDirty && (isButtonOrChild === false || closestAttendanceSubmitButton === null)) {
        event.preventDefault();
      }
    },
    load() {
      let $this = this;
      let students = { };

      this.students.forEach(function(student) {
        students[student.uuid] = {
          uuid: student.uuid,
          firstname: student.firstname,
          lastname: student.lastname,
          reasons: [ ]
        };

        if($this.attendances.filter(x => x.student.uuid === student.uuid).length === 0) {
          $this.addStudent(student.uuid, student.firstname, student.lastname, student.email);
        }
      });

      this.possibleAbsences.forEach(function(absence) {
        if(absence.student.uuid in students) {
          students[absence.student.uuid].reasons.push($this.$trans('book.attendance.absence_reason.' + absence.reason));
        }
      });

      $this.absences = [ ];

      for(let uuid in students) {
        let student = students[uuid];

        let attendance = $this.attendances.filter(x => x.student.uuid === uuid).map(x => x.type);

        if(student.reasons.length > 0 && attendance.length > 0 && attendance[0] !== 0) {
          $this.absences.push(student);
        }
      }

      this.$http.get(this.listStudentsUrl)
          .then(function(response) {
            let choices = [
              {
                label: $this.$trans('label.select.student'),
                value: '',
                selected: true
              }
            ];

            response.data.forEach(function(student) {
              let gradeAppendix = '';

              if(student.grade !== null) {
                gradeAppendix = ' [' + student.grade.name + ']';
              }

              choices.push({
                label: student.lastname + ', ' + student.firstname + gradeAppendix,
                value: student.uuid,
                customProperties: {
                  firstname: student.firstname,
                  lastname: student.lastname,
                  email: student.email
                }
              });
            });

            $this.studentChoice.setChoices(choices, 'value', 'label', true);
          })
          .catch(function(error) {
            console.error(error);
          });
    }
  }
}
</script>