<template>
  <div class="row mt-xl-5 mt-0">
    <div class="offset-xl-4 col-xl-4 offset-lg-3 col-lg-6 col-md-12">
      <Logo />

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <div v-if="loading">
            <div class="text-center p-5 m-5">
              <div class="spinner-border text-dark"></div>
            </div>
          </div>

          <div
            v-else
          >
            <div>
              <h3
                v-if="selectedEventType === null"
                class="flex-fill"
              >
                {{ eventTypes[0].host.givenName }} {{ eventTypes[0].host.familyName }}
              </h3>

              <div v-else>
                <div class="d-flex">
                  <div class="flex-fill">
                    <strong>
                      {{ selectedEventType.name }}
                    </strong>
                    ({{ selectedEventType.duration }}min)
                    mit
                    <strong>
                      {{ eventTypes[0].host.givenName }} {{ eventTypes[0].host.familyName }}
                    </strong>
                  </div>

                  <div class="flex-column">
                    <button
                      class="btn btn-sm btn-link p-0 m-0 text-dark text-decoration-none"
                      @click="selectedEventType=null"
                    >
                      <font-awesome-icon icon="pencil" />
                      {{ $t('change') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <p v-if="selectedEventType === null">
              {{ $t('booking.index.intro') }}
            </p>

            <div v-if="selectedEventType === null">
              <div
                v-for="eventType in eventTypes"
                :key="eventType.id"
                class="border rounded shadow-sm mb-3 hover booking-index-link"
              >
                <div
                  class="p-3"
                  @click="selectEventType(eventType)"
                >
                  <strong>
                    {{ eventType.name }}
                  </strong>

                  <div class="float-end">
                    <div class="small">
                      {{ eventType.duration }} {{ $t('booking.index.minutes') }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <BookingCalendar
        v-if="selectedEventType !== null"
        :event-type="selectedEventType"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from 'vue';
import {getEventTypes} from '../services/booking';
import {EventType, EventTypes} from "../types/EventType";
import {useRoute} from 'vue-router'
import BookingCalendar from "./BookingCalendar.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Logo from "./Logo.vue";
const $route = useRoute();
const userEmail = String($route.params.email)
const loading = ref(true)
const eventTypes = ref<EventTypes>([]);
const selectedEventType = ref<EventType | null>(null);

onMounted(async () => {
  try {
    eventTypes.value = await getEventTypes(userEmail);

    loading.value = false
  } catch (error) {
    console.error("Fehler beim Laden der Event-Typen oder Benutzerinformationen", error);
  }
});

function selectEventType(eventType: EventType) {
  const url = `/${userEmail}/${eventType.slug}`;
  selectedEventType.value = eventType;
}
</script>
