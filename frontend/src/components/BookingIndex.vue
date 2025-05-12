<template>
  <div class="d-flex justify-content-center mt-5">
    <div class="w-50">
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
            <h3 v-if="eventTypes.length > 0">
              {{ eventTypes[0].host.givenName }} {{ eventTypes[0].host.familyName }}
            </h3>

            <p>
              {{ $t('booking.index.intro') }}
            </p>

            <div
              v-for="eventType in eventTypes"
              :key="eventType.id"
              class="border rounded shadow-sm mb-3 hover booking-index-link"
            >
              <router-link
                :to="`/${userEmail}/${eventType.slug}`"
                class="d-block p-3 text-dark text-decoration-none"
              >
                <strong>
                  {{ eventType.name }}
                </strong>

                <div class="float-end">
                  <div class="small">
                    {{ eventType.duration }} Minuten
                  </div>
                </div>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from 'vue';
import {getEventTypes} from '../services/booking';
import {EventType, EventTypes} from "../types/EventType";
import { useRoute } from 'vue-router'

const $route = useRoute();
const userEmail = String($route.params.email)
const loading = ref(true)
const eventTypes = ref<EventTypes>([]);

onMounted(async () => {
  try {
    eventTypes.value = await getEventTypes(userEmail);

    loading.value = false
  } catch (error) {
    console.error("Fehler beim Laden der Event-Typen oder Benutzerinformationen", error);
  }
});
</script>
