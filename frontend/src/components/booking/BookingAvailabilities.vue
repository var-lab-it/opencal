<template>
  <div class="card mb-3">
    <div class="card-body">
      <div v-if="loading">
        <div class="text-center p-5 m-5">
          <div class="spinner-border text-dark"></div>
        </div>
      </div>
      <div v-else>
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
            <div v-if="selectedTimeSlot === null">
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

            <div v-else>
              <div class="d-flex align-items-center">
                <div class="flex-fill">
                  <font-awesome-icon icon="clock" />
                  {{ selectedTimeSlot.start }} - {{ selectedTimeSlot.end }}
                </div>
                <div class="flex-column">
                  <button
                    class="btn btn-sm btn-link p-0 m-0 text-dark text-decoration-none"
                    @click="selectedTimeSlot=null"
                  >
                    <font-awesome-icon icon="pencil" />
                    {{ $t('change') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-if="selectedTimeSlot !== null">
    <BookingForm
      :time-slot="selectedTimeSlot"
      :event-type="eventType"
      :calendar-day="selectedCalendarDay"
      :user-email="userEmail"
    />
  </div>
</template>

<script setup lang="ts">
import {EventType} from "../../types/EventType";
import {CalendarDay} from "v-calendar/dist/types/src/utils/page";
import {onMounted, ref} from "vue";
import {getDayAvailabilities} from "../../services/booking";
import {formatCalendarDayToString} from "../../helper/dateTime";
import {useRoute} from "vue-router";
import {Availability} from "../../types/Availability";
import {TimeSlot} from "../../types/TimeSlot";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import BookingForm from "./BookingForm.vue";

const route = useRoute();
const userEmail = String(route.params.email);
const eventSlug = String(route.params.slug);

const loading = ref(true);
const availability = ref<Availability | null>(null);
const notAvailable = ref(false);
const selectedTimeSlot = ref<TimeSlot | null>(null);

const props = defineProps({
  eventType: {type: Object as () => EventType, required: true},
  selectedCalendarDay: {type: Object as () => CalendarDay, required: true},
});

onMounted(async () => {
  const dayString = formatCalendarDayToString(props.selectedCalendarDay);

  try {
    const response = await getDayAvailabilities(userEmail, dayString, props.eventType.id);
    availability.value = response;

    const hasAvailabilities = response.availabilities.length > 0;

    loading.value = false;
    notAvailable.value = !hasAvailabilities;

    if (!hasAvailabilities) {
      selectedTimeSlot.value = null;
    }
  } catch (error) {
    console.error('Failed to load availabilities:', error);
    loading.value = false;
    notAvailable.value = true;
  }
});
</script>
