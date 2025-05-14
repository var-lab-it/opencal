<template>
  <div class="card">
    <div class="card-body">
      <div
        v-if="loading"
        class="text-center p-5 m-5"
      >
        <div class="spinner-border text-dark"></div>
      </div>

      <div v-else>
        <div class="mb-3 d-flex justify-content-between align-items-center">
          <h5>Termin buchen</h5>
        </div>

        <form
          v-if="!submitted"
          class="booking-form"
          @submit.prevent="handleSubmit"
        >
          <div class="form-group mb-3">
            <label for="name">{{ $t('booking.form.fields.name') }}:</label>
            <input
              id="name"
              v-model="formName"
              type="text"
              class="form-control"
              required
              :disabled="loadSubmit"
            />
          </div>

          <div class="form-group mb-3">
            <label for="email">{{ $t('booking.form.fields.email') }}:</label>
            <input
              id="email"
              v-model="formEmail"
              type="email"
              class="form-control"
              required
              :disabled="loadSubmit"
            />
          </div>

          <div class="form-group mb-3">
            <label for="message">{{ $t('booking.form.fields.message', {name: eventType.host.givenName}) }}:</label>
            <textarea
              id="message"
              v-model="formMessage"
              class="form-control"
              :disabled="loadSubmit"
            ></textarea>
          </div>

          <div class="d-grid">
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="loadSubmit"
            >
              <span
                v-if="loadSubmit"
                class="spinner-grow text-light spinner-grow-sm"
                role="status"
              ></span>
              {{ $t('booking.form.buttons.submit') }}
              <font-awesome-icon icon="angle-right" />
            </button>
          </div>
        </form>

        <div v-else-if="submitted">
          <div class="alert alert-success">
            <font-awesome-icon icon="check-circle" />
            {{ $t('booking.form.messages.success') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {submitBooking} from "../services/booking";
import type {CalendarDay} from "v-calendar/dist/types/src/utils/page";
import type {EventType} from "../types/EventType";
import type {TimeSlot} from "../types/TimeSlot";
import type {Booking} from "../types/Booking";
import {formatCalendarDayToString} from "../helper/dateTime";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
  userEmail: {type: String, required: true},
  calendarDay: {type: Object as () => CalendarDay, required: true},
  eventType: {type: Object as () => EventType, required: true},
  timeSlot: {type: Object as () => TimeSlot, required: true}
});

const loading = ref(true);
const formName = ref('');
const formEmail = ref('');
const formMessage = ref('');
const loadSubmit = ref(false);
const submitted = ref(false);
const error = ref('');

onMounted(() => {
  loading.value = false;
});

async function handleSubmit() {
  loadSubmit.value = true;
  error.value = '';

  try {
    const booking: Booking = {
      participantName: formName.value,
      participantEmail: formEmail.value,
      participantMessage: formMessage.value,
      eventType: props.eventType,
      day: formatCalendarDayToString(props.calendarDay),
      startTime: props.timeSlot.start,
      endTime: props.timeSlot.end
    };

    await submitBooking(booking).then(response => {
      submitted.value = true;
    });
  } catch (err) {
    console.error("Error:", err);
  } finally {
    loadSubmit.value = false;
  }
}
</script>
