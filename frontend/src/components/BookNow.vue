<template>
  <div class="d-flex justify-content-center mt-5 booking-now">
    <div class="">
      <div class="card shadow">
        <div class="card-body">
          <div v-if="loading">
            <div class="text-center p-5 m-5">
              <div class="spinner-border text-dark"></div>
            </div>
          </div>

          <div
            v-else
          >
            <div class="d-flex">
              <div class="">
                <div class="event-type">
                  <router-link
                    class="text-decoration-none"
                    :to="`/${userEmail}`"
                  >
                    <font-awesome-icon icon="arrow-left" />
                    {{ eventType?.host.givenName }} {{ eventType?.host.familyName }}
                  </router-link>
                </div>

                <div class="mb-3">
                  <h3>
                    {{ eventType?.name }}
                  </h3>
                </div>

                <div>
                  <font-awesome-icon icon="clock" />
                  {{ eventType?.duration }} min
                </div>
              </div>
              <div class="date-picker pe-3">
                <VDatePicker
                  v-model="currentDate"
                  expanded
                  transparent
                  borderless
                  :attributes="calendarAttributes"
                  mode="date"
                  is-required
                  @dayclick="dayClicked"
                />
              </div>
              <div
                v-if="showTimeSelector && selectedCalendarDay"
                class="ps-2"
              >
                <h5 class="mt-2">
                  {{ $t('booking.book_now.available_timeslots') }}:
                </h5>

                <div v-if="loadAvailabilities">
                  <div class="text-center p-5 m-5">
                    <div class="spinner-border text-dark"></div>
                  </div>
                </div>

                <div
                  v-else
                >
                  <div class="time-picker-wrapper">
                    <div v-if="notAvailable">
                      <div class="alert alert-warning p-2 mt-3 small">
                        {{
                          $t('booking.book_now.not_available', {
                            name: eventType?.host.givenName,
                            date_string: selectedCalendarDay.ariaLabel
                          })
                        }}
                      </div>
                    </div>

                    <div v-else>
                      <div class="time-slot-list">
                        <div class="d-grid">
                          <button
                            v-for="(timeSlot, index) in availability?.availabilities"
                            :id="'time_slot_'+String(index)"
                            :key="timeSlot.start"
                            :class="['btn', 'me-2', 'mb-2', selectedTimeSlot === timeSlot ? 'btn-primary' : 'btn-outline-primary']"
                            @click="selectedTimeSlot = timeSlot"
                          >
                            {{ timeSlot.start }} - {{ timeSlot.end }}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-if="selectedTimeSlot && eventType && selectedCalendarDay">
            <BookingForm
              :user-email="userEmail"
              :event-type="eventType"
              :calendar-day="selectedCalendarDay"
              :time-slot="selectedTimeSlot"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import {getDayAvailabilities, getOneEventType} from "../services/booking";
import {EventType} from "../types/EventType";
import {CalendarDay} from "v-calendar/dist/types/src/utils/page";
import {Availability} from "../types/Availability";
import {TimeSlot} from "../types/TimeSlot";
import BookingForm from "./BookingForm.vue";
import {formatCalendarDayToString} from "../helper/dateTime";

const widthClass = ref("w-50");
const showTimeSelector = ref(false);

const loading = ref(true);
const eventType = ref<EventType | null>(null);
const currentDate = ref<Date | null>(null);
const calendarAttributes = ref<Array<Record<string, unknown>>>([]);

const route = useRoute();
const userEmail = String(route.params.email);
const eventSlug = String(route.params.slug);

const selectedCalendarDay = ref<CalendarDay | null>(null);

const loadAvailabilities = ref(false);
const availability = ref<Availability | null>(null);
const notAvailable = ref(false);

const selectedTimeSlot = ref<TimeSlot | null>(null);

async function dayClicked(day: CalendarDay): Promise<void> {
  widthClass.value = 'w-75';
  selectedCalendarDay.value = day;
  showTimeSelector.value = true;
  loadAvailabilities.value = true;

  const dayString = formatCalendarDayToString(day);

  try {
    const response = await getDayAvailabilities(userEmail, dayString, eventType.value.id);
    availability.value = response;

    const hasAvailabilities = response.availabilities.length > 0;

    loadAvailabilities.value = false;
    notAvailable.value = !hasAvailabilities;

    if (!hasAvailabilities) {
      selectedTimeSlot.value = null;
    }
  } catch (error) {
    console.error('Failed to load availabilities:', error);
    loadAvailabilities.value = false;
    notAvailable.value = true;
  }
}

onMounted(async () => {
  eventType.value = await getOneEventType(userEmail, eventSlug);

  loading.value = false;
});
</script>
