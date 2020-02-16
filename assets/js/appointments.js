import Choices from "choices.js";

require('../css/appointments.scss');

import Vue from 'vue';

import axios from 'axios'
import VueAxios from 'vue-axios'

import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import FullCalendar from '@fullcalendar/vue'
import bootstrapPlugin from '@fullcalendar/bootstrap';
import deLocale from '@fullcalendar/core/locales/de'

require('@fullcalendar/core/locales-all');

Vue.use(VueAxios, axios);

document.addEventListener('DOMContentLoaded', function () {

    new Vue({
        el: '#appointments',
        components: {
            FullCalendar: FullCalendar
        },
        data: function () {
            return {
                calendarLocales: [
                    deLocale
                ],
                calendarPlugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin,
                    bootstrapPlugin,
                    listPlugin
                ],
                calendarWeekends: true,
                calendarEvents: [],
                query: {
                    studentId: '',
                    gradeId: '',
                    categoryIds: []
                }
            }
        },
        watch: {
            query: {
                handler() {
                    this.loadEvents();
                },
                deep: true
            }
        },
        methods: {
            handleEventClick(event) {
                console.log(event)
            },
            loadEvents() {
                let $this = this;

                axios.get($this.$data.url, { params: this.$data.query })
                    .then(function(response) {
                        $this.$data.calendarEvents = response.data;
                    })
                    .catch(function(error) {
                        console.error(error);
                    });
            }
        },
        template: '#appointments-template',
        mounted: function() {
            this.$data.url = this.$el.getAttribute('data-url');

            this.$el.querySelectorAll('select[data-choice=true]').forEach(function(el) {
                let removeItemButton = false;

                if(el.getAttribute('multiple') !== null) {
                    removeItemButton = true;
                }

                new Choices(el, {
                    itemSelectText: '',
                    shouldSort: false,
                    shouldSortItems: false,
                    removeItemButton: removeItemButton,
                    callbackOnCreateTemplates: function(template) {
                        return {
                            item: (classNames, data) => {
                                return template(`
          <div class="${classNames.item} ${
                                    data.highlighted
                                        ? classNames.highlightedState
                                        : classNames.itemSelectable
                                } ${
                                    data.placeholder ? classNames.placeholder : ''
                                }" data-item data-id="${data.id}" data-value="${data.value}" ${
                                    data.active ? 'aria-selected="true"' : ''
                                } ${data.disabled ? 'aria-disabled="true"' : ''}>
            ${data.customProperties != null && data.customProperties.startsWith('#') ? '<span class="color-rect" style="background: ' + data.customProperties + ';"></span>' : '' } ${data.label}
          </div>
        `);
                            },
                            choice: (classNames, data) => {
                                return template(`
          <div class="${classNames.item} ${classNames.itemChoice} ${
                                    data.disabled ? classNames.itemDisabled : classNames.itemSelectable
                                }" data-select-text="${this.config.itemSelectText}" data-choice ${
                                    data.disabled
                                        ? 'data-choice-disabled aria-disabled="true"'
                                        : 'data-choice-selectable'
                                } data-id="${data.id}" data-value="${data.value}" ${
                                    data.groupId > 0 ? 'role="treeitem"' : 'role="option"'
                                }>
             ${data.customProperties != null && data.customProperties.startsWith('#') ? '<span class="color-rect" style="background: ' + data.customProperties + ';"></span>' : '' } ${data.label}
          </div>
        `);
                            },
                        };
                    },
                });
            });

            this.loadEvents();
        }
    });
});