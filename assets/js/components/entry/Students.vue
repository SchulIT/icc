<template>
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <div class="flex-fill">
          <i class="fas fa-users"></i> {{ $trans('book.attendance.label') }}
        </div>

        <div>
          <button class="btn btn-primary btn-sm ms-1"
                  :class="{ 'btn-danger': isDirty }"
                  v-if="showSaveButton"
                  id="attendance_submit"
                  type="submit">
            <i class="fa fa-save"></i> {{ $trans('actions.save')}}
            <i class="fa fa-exclamation-triangle ms-1" v-if="isDirty"></i>
          </button>
        </div>
      </div>

      <div class="card-body">
        <div v-for="lessonNumber in range(start, end)">
          <span class="badge text-bg-secondary me-2">{{ $transChoice('label.exam_lessons', 0, { start: lessonNumber }) }}</span>
          <i class="fa fa-check"></i> {{ $trans('book.attendance.overview.present', { 'number': numPresent(lessonNumber) })}}
          <i class="fa fa-clock"></i> {{ $trans('book.attendance.overview.late', { 'number': numLate(lessonNumber) })}}
          <i class="fa fa-times"></i> {{ $trans('book.attendance.overview.absent', { 'number': numAbsent(lessonNumber) })}}
        </div>
      </div>

      <div class="card-body border-top" v-if="removals.length > 0">
        <ul class="list-unstyled mb-0">
          <li v-for="removal in removals" class="d-flex align-items-start">
            <div class="flex-fill">
              <div>
                {{ removal.student.lastname }}, {{ removal.student.firstname }}
              </div>
              <span class="badge text-bg-primary">{{ removal.reason }}</span>
              <span class="badge text-bg-secondary ms-1" v-for="lessonNumber in removal.lessons">
                {{ $transChoice('label.substitution_lessons', 0, { start: lessonNumber }) }}
              </span>
            </div>
            <button type="button"
                    @click="removeBySuggestion(removal)"
                    class="btn btn-sm btn-outline-primary ms-1">{{ $trans('book.attendance.remove.label') }}</button>
          </li>
        </ul>
      </div>

      <div class="card-body border-top" v-if="absences.length > 0">
        <ul class="list-unstyled mb-0">
          <li v-for="absence in absences" class="d-flex align-items-start">
            <div class="flex-fill">
              <div>
                {{ absence.student.lastname }}, {{ absence.student.firstname }}
              </div>
              <span class="badge text-bg-primary">{{ absence.label }}</span>

              <span v-if="absence.zero_absent_lessons" class="badge text-bg-info ms-1">0 FS</span>

              <span class="badge text-bg-secondary ms-1" v-for="lessonNumber in absence.lessons">
                {{ $transChoice('label.substitution_lessons', 0, { start: lessonNumber }) }}
              </span>
            </div>
            <a :href="absence.url"
               v-if="absence.url !== null"
               target="_blank"
               class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-external-link"></i> {{ $trans('actions.show')}}</a>

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
          <button type="button" class="btn btn-outline-primary btn-sm ms-2" @click="onAddStudent()">
            <i class="fas fa-plus"></i>
            {{ $trans('book.entry.add_student.label')}}
          </button>
        </div>
      </div>

      <div class="card-footer">
        <div class="d-flex align-items-stretch">
          <div class="flex-fill">
            <select id="add_studygroup"></select>
          </div>
          <button type="button" class="btn btn-outline-primary btn-sm ms-2" @click="onAddStudyGroup()">
            <i class="fas fa-user-plus"></i>
            {{ $trans('book.entry.add_studygroup.label')}}
          </button>
        </div>
      </div>

      <div class="card-footer">
        <input type="text" placeholder="Lernenden suchen..." class="form-control" @input="applySearchResults($event.target.value)">
      </div>

      <div class="card-footer d-flex align-items-center" v-if="selectedAttendances.length > 0">
        <div class="flex-fill">
          <span class="badge text-bg-primary">
            {{ $trans('book.attendance.selected', { number: selectedAttendances.length })}}
          </span>
        </div>
        <div>
          <button class="btn btn-outline-primary btn-sm ms-1 d-inline-block"
                  @click.prevent="unselect()">
            <i class="far fa-check-square"></i>
          </button>

          <button class="btn btn-outline-success btn-sm ms-1 d-inline-block"
                  @click.prevent="present(selectedAttendances)">
            <i class="fas fa-user-check"></i>
          </button>
          <button class="btn btn-outline-warning btn-sm ms-1"
                  @click.prevent="late(selectedAttendances)">
            <i class="fas fa-user-clock"></i>
          </button>
          <div class="btn-group d-inline-flex align-items-center ms-1">
            <button class="btn btn-outline-warning btn-sm"
                    @click.prevent="plusMinute(selectedAttendances)">
              <i class="fa fa-plus"></i>
            </button>
            <button class="btn btn-outline-warning btn-sm"
                    @click.prevent="minusMinute(selectedAttendances)">
              <i class="fa fa-minus"></i>
            </button>
          </div>
          <button class="btn btn-outline-danger btn-sm ms-1  d-inline-block"
                  @click.prevent="absent(selectedAttendances)">
            <i class="fas fa-user-times"></i>
          </button>
          <button class="btn btn-outline-danger btn-sm ms-1 d-inline-block"
                  :title="$trans('book.attendance.is_zero_absent_lesson')"
                  @click.prevent="zeroAbsentLesson(selectedAttendances, true)">
            0 FS
          </button>
        </div>

      </div>

      <div class="list-group list-group-flush">
        <div class="list-group-item align-items-center p-0"
             v-for="attendance in attendances"
             :class="{ 'bg-selected': selectedAttendances.indexOf(attendance) >= 0, 'd-none': attendance.isHidden || false }">

          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][lesson]'" :value="attendance.lesson">
          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][isZeroAbsentLesson]'" :value="attendance.zero_absent_lesson ? 1 : 0">
          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][type]'" :value="attendance.type">
          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][excuseStatus]'" :value="attendance.excuse_status">
          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][lateMinutes]'" :value="attendance.minutes">
          <input type="hidden" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][student]'" :value="attendance.student.uuid">

          <div class="d-flex flex-wrap">
            <div class="flex-fill p-3 pointer"
                 @click="select(attendance)">
              <span class="badge text-bg-secondary me-1">{{ $transChoice('label.substitution_lessons', 0, { 'start': attendance.lesson }) }}</span>
              <i class="fa fa-user"></i>
              {{ attendance.student.lastname }}, {{ attendance.student.firstname }}
            </div>
            <div class="d-flex align-self-center text-right my-2 me-3 flex-fill justify-content-end">
              <div class="btn-group" v-if="flags.length > 0">
                <template v-for="flag in flags">
                  <input type="checkbox" class="btn-check" autocomplete="off" :checked="attendance.flags.includes(flag.id)" :id="uniquePrefix + '_' + attendance.student.uuid + '_' + flag.id" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][flags][]'" :value="flag.id">
                  <label class="btn btn-outline-primary btn-sm" :title="flag.description" :for="uniquePrefix + '_' + attendance.student.uuid + '_' + flag.id">
                    <span class="fa-stack fa-1x m-n2">
                      <i :class="flag.icon + ' fa-stack-1x'"></i>
                      <i :class="flag.stack_icon + ' fa-stack-1x text-danger'" v-if="flag.stack_icon !== null"></i>
                    </span>
                  </label>
                </template>
              </div>

              <button class="btn btn-outline-primary btn-sm ms-1 d-inline-block"
                      @click.prevent="attendance.showComment !== true ? attendance.showComment = true : attendance.showComment = false"
                      :title="$trans('label.comment')">
                <i class="far fa-comment-alt"></i>
              </button>

              <button class="btn btn-outline-success btn-sm ms-1 d-inline-block"
                      @click.prevent="present(attendance)"
                      :title="$trans('book.attendance.type.present')"
                      :class="{ active: attendance.type === 1}"> <i class="fas fa-user-check"></i>
              </button>
              <button class="btn btn-outline-warning btn-sm ms-1"
                      :class="{ active: attendance.type === 2}"
                      :title="$trans('book.attendance.type.late')"
                      @click.prevent="late(attendance)">
                <i class="fas fa-user-clock"></i>
              </button>
              <div class="btn-group d-inline-flex align-items-center ms-1"
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
              <button class="btn btn-outline-danger btn-sm ms-1 d-inline-block"
                      :class="{ active: attendance.type === 0}"
                      :title="$trans('book.attendance.type.absent')"
                      @click.prevent="absent(attendance)">
                <i class="fas fa-user-times"></i>
              </button>

              <button class="btn btn-outline-danger btn-sm ms-1 d-inline-block"
                      :class="{ active: attendance.zero_absent_lesson === true}"
                      :title="$trans('book.attendance.is_zero_absent_lesson')"
                      @click.prevent="zeroAbsentLesson(attendance, !attendance.zero_absent_lesson)"
                      v-if="attendance.type === 0">
                0 FS
              </button>

              <div class="btn-group d-inline-flex align-items-center ms-1"
                   v-if="attendance.type === 0 && attendance.zero_absent_lesson !== true">
                <button class="btn btn-outline-danger btn-sm"
                        :title="$trans('book.students.not_set')"
                        :class="{ active: attendance.excuse_status === 0 && !attendance.has_excuses}"
                        :disabled="attendance.has_excuses"
                        @click.prevent="setExcuseStatus(attendance, 0)">
                  <i class="fas fa-question"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm"
                        :title="$trans('book.students.excused')"
                        :class="{ active: attendance.excuse_status === 1 || attendance.has_excuses}"
                        @click.prevent="setExcuseStatus(attendance, 1)">
                  <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm"
                        :title="$trans('book.students.not_excused')"
                        :class="{ active: attendance.excuse_status === 2 && !attendance.has_excuses}"
                        :disabled="attendance.has_excuses"
                        @click.prevent="setExcuseStatus(attendance, 2)">
                  <i class="fas fa-times"></i>
                </button>
              </div>

              <div class="btn-group align-items-center d-inline-flex ms-1">
                <button type="button" class="btn btn-outline-danger btn-sm" :title="$trans('book.attendance.remove.label')" @click.prevent="remove(attendance)">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="d-flex px-3 pb-3" v-if="(attendance.comment !== null || attendance.showComment === true)">
            <div class="w-100">
              <div class="input-group">
                <span class="input-group-text"><i class="far fa-comment-alt"></i></span>
                <input type="text" maxlength="255" v-model="attendance.comment" class="form-control" :name="'lesson_entry[attendances][' + attendances.indexOf(attendance) + '][comment]'">
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
    suggestedRemovals: Array,
    attendances: Array,
    step: Number,
    start: Number,
    end: Number,
    listStudentsUrl: String,
    listStudyGroupsUrl: String,
    showSaveButton: Boolean,
    flags: Array
  },
  data() {
    return {
      isDirty: false,
      selectedAttendances: [ ],
      absences: [ ],
      removals: [ ],
      uniquePrefix: (Math.random() + 1).toString(16).substring(12)
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
    this.studyGroupChoice = new Choices(this.$el.querySelector('#add_studygroup'));
  },
  computed: {
    allPresent() {
      return this.numPresent === this.attendances.length;
    }
  },
  methods: {
    numPresent(lesson) {
      let count = 0;
      for(let attendance of this.attendances) {
        if(attendance.lesson === lesson && attendance.type === 1) {
          count++;
        }
      }

      return count;
    },
    numLate(lesson) {
      let count = 0;
      for(let attendance of this.attendances) {
        if(attendance.lesson === lesson && attendance.type === 2) {
          count++;
        }
      }

      return count;
    },
    numAbsent(lesson) {
      let count = 0;
      for(let attendance of this.attendances) {
        if(attendance.lesson === lesson && attendance.type === 0) {
          count++;
        }
      }

      return count;
    },
    range(start, end) {
      return Array.from({ length: end - start + 1 }, (v,i) => i + start)
    },
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

      for(let attendance of this.attendances) {
        if(attendance.student.uuid !== absence.student.uuid) {
          continue;
        }

        let lessonFound = false;

        for(let lessonNumber of absence.lessons) {
          if(lessonNumber === attendance.lesson) {
            lessonFound = true;
          }
        }

        if(lessonFound !== true) {
          continue;
        }

        if(absence.attendance_type === 0) {
          this.absent(attendance);
        } else if(absence.attendance_type === 1) {
          this.present(attendance);
        } else if(absence.attendance_type === 2) {
          this.late(attendance);
        }

        if(absence.zero_absent_lessons === true) {
          attendance.zero_absent_lesson = true;
        }
        attendance.excuse_status = absence.excuse_status;

        if(absence.flags !== null && absence.flags.length > 0) {
          attendance.flags = absence.flags;
        } else {
          attendance.comment = absence.label;
        }
      }

      $this.absences.splice($this.absences.indexOf(absence), 1);
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
    zeroAbsentLesson(attendanceOrAttendances, value) {
      let $this = $this;
      this.apply(attendanceOrAttendances, function(attendance) {
        attendance.zero_absent_lesson = value;
      })
    },
    setExcuseStatus(attendanceOrAttendances, status) {
      this.apply(attendanceOrAttendances, function(attendance) {
        attendance.excuse_status = status;
      });
    },
    remove(attendance) {
      if(this.attendances.indexOf(attendance) !== -1) {
        this.attendances.splice(this.attendances.indexOf(attendance), 1);
      }
    },
    removeBySuggestion(suggestion) {
      let attendancesToRemove = [];

      for(let attendance of this.attendances) {
        if(attendance.student.uuid === suggestion.student.uuid && suggestion.lessons.includes(attendance.lesson)) {
          attendancesToRemove.push(attendance);
        }
      }

      for(let attendanceToRemove of attendancesToRemove) {
        this.remove(attendanceToRemove);
      }

      this.removals.splice(this.removals.indexOf(suggestion), 1);
    },
    addStudent(uuid, firstname, lastname) {
      for(let lessonNumber = this.start; lessonNumber <= this.end; lessonNumber++) {
        let found = false;
        // only add if not already present!
        for(let attendance of this.attendances) {
          if(attendance.student.uuid === uuid && attendance.lesson === lessonNumber) {
            found = true;
            break;
          }
        }

        if(found === true) {
          continue;
        }

        let attendance = {
          uuid: '',
          comment: null,
          excuse_status: 0,
          lesson: lessonNumber,
          zero_absent_lesson: false,
          minutes: 0,
          type: 1,
          student: {
            uuid: uuid,
            firstname: firstname,
            lastname: lastname
          },
          flags: []
        };

        this.attendances.push(attendance);
      }
    },
    onAddStudent() {
      let student = this.studentChoice.getValue();

      if(student.value === '') {
        return;
      }

      this.addStudent(student.value, student.customProperties.firstname, student.customProperties.lastname);
    },
    onAddStudyGroup() {
      let studyGroup = this.studyGroupChoice.getValue();

      if(studyGroup === null) {
        return;
      }

      for(const student of studyGroup.customProperties.students) {
        this.addStudent(student.uuid, student.firstname, student.lastname);
      }
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
      //let students = { };

      this.students.forEach(function(student) {
        if($this.attendances.filter(x => x.student.uuid === student.uuid).length === 0) {
          $this.addStudent(student.uuid, student.firstname, student.lastname);
        }
      });

      this.absences = [ ];

      for(let absence of this.possibleAbsences) {
        // check if attendance is already applied
        for(let attendance of this.attendances) {
          if(attendance.student.uuid !== absence.student.uuid) {
            continue;
          }

          let areFlagsAlreadyApplied = true;
          for(let flag of absence.flags) {
            if(!attendance.flags.includes(flag)) {
              areFlagsAlreadyApplied = false;
            }
          }

          if(attendance.type !== absence.attendance_type || (absence.flags.length === 0 && attendance.comment !== absence.label) || (absence.flags.length > 0 && areFlagsAlreadyApplied === false)) {
            this.absences.push(absence);
          }
        }
      }

      $this.removals = [ ];
      for(let suggestion of $this.suggestedRemovals) {
        $this.removals.push(suggestion);
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
                }
              });
            });

            $this.studentChoice.setChoices(choices, 'value', 'label', true);
          })
          .catch(function(error) {
            console.error(error);
          });

      this.$http.get(this.listStudyGroupsUrl)
          .then(function(response) {
            let choices = [
              {
                label: $this.$trans('label.select.study_group'),
                value: '',
                selected: true
              }
            ];

            for(const detail of response.data) {
              let grades = [ ];

              for(const grade of detail.study_group.grades) {
                grades.push(grade.name);
              }

              let students = [ ];

              for(const student of detail.students) {
                students.push({
                  uuid: student.uuid,
                  firstname: student.firstname,
                  lastname: student.lastname,
                });
              }

              let type = $this.$trans('studygroup.type.' + detail.study_group.type);
              let name = detail.study_group.name + ' [' + type + '] ' + '(' + grades.join(', ') + ')';

              choices.push({
                label: name,
                value: detail.study_group.uuid,
                customProperties: {
                  students: students
                }
              });
            }

            $this.studyGroupChoice.setChoices(choices, 'value', 'label', true);
          }).catch(function(error) {
            console.error(error);
          });
    },
    applySearchResults(searchText) {
      searchText = searchText.trim();

      for(let attendance of this.attendances) {
        if(searchText === null || searchText === '') {
          attendance.isHidden = false;
        } else {
          attendance.isHidden = attendance.student.lastname.includes(searchText) !== true && attendance.student.firstname.includes(searchText) !== true;
        }
      }
    }
  }
}
</script>