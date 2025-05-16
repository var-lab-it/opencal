<template>
  <div class="d-flex justify-content-center mt-5">
    <div class="w-50">
      <div class="card shadow">
        <div class="card-body">
          <div
            v-if="loading"
            data-testid="loading-spinner"
          >
            <div class="text-center p-5 m-5">
              <div class="spinner-border text-dark"></div>
            </div>
          </div>
          <div v-else>
            <h4>
              {{ $t('booking.cancel.headline') }}
            </h4>

            <p>
              {{ $t('booking.cancel.confirm_message') }}
            </p>

            <div
              class="mb-3"
              data-testid="appointment-details"
            >
              <strong>{{ $t('booking.cancel.appointment') }}: </strong> {{ eventData?.eventType.name }} <br />
              <strong>{{ $t('booking.cancel.day') }}:</strong> {{ formatDateStringToDay(eventData?.day) }}<br />
              <strong>{{ $t('booking.cancel.time') }}:</strong> {{ formatDateStringToTime(eventData?.startTime) }} -
              {{ formatDateStringToTime(eventData?.endTime) }}<br />
            </div>

            <div class="mt-3">
              <button
                v-if="isCancelled === false"
                type="button"
                class="btn btn-danger"
                data-testid="cancel-button"
                @click="doCancellation"
              >
                <span
                  v-if="loadCancellation"
                  class="spinner-grow text-light spinner-grow-sm"
                  role="status"
                ></span>
                {{ $t('booking.cancel.buttons.cancel_appointment') }}
              </button>
              <div
                v-else
                class="alert alert-success"
              >
                <font-awesome-icon icon="calendar-xmark" />
                {{ $t('booking.cancel.success_message') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {cancelBooking, getOneEvent} from "../../services/booking";
import {useRoute} from "vue-router";
import {Event} from "../../types/Event";
import {formatDateStringToDay, formatDateStringToTime} from "../../helper/dateTime";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const loading = ref(true);
const eventData = ref<Event | null>(null);

const route = useRoute();
const eventId = Number(route.params.id);

const loadCancellation = ref(false);
const isCancelled = ref(false);

onMounted(async () => {
  try {
    eventData.value = await getOneEvent(eventId);

    loading.value = false

    if (eventData.value.cancelledByAttendee === true) {
      isCancelled.value = true;
    }
  } catch (error) {
    console.error("Error", error);
  }
});

function doCancellation() {
  try {
    loadCancellation.value = true;

    if (eventData.value !== null) {
      console.log("Event Data:", eventData.value);
      cancelBooking(eventData.value.id, eventData.value.cancellationHash).then(response => {
        loadCancellation.value = false;
        isCancelled.value = true;
      });
    }
  } catch (error) {
    console.error("Error", error);
    loading.value = false;
  }
}
</script>
