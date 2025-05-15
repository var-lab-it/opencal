<template>
  <div class="card mb-3">
    <div class="card-body">
      <div
        v-if="selectedCalendarDay === null"
        class="date-picker pe-3"
      >
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
      <div v-else>
        <div class="d-flex align-items-center">
          <div class="flex-fill">
            <font-awesome-icon icon="calendar-check" />
            {{ selectedCalendarDay.ariaLabel }}
          </div>
          <div class="flex-column">
            <button
              class="btn btn-sm btn-link p-0 m-0 text-dark text-decoration-none"
              @click="selectedCalendarDay=null"
            >
              <font-awesome-icon icon="pencil" />
              {{ $t('change') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <BookingAvailabilities
    v-if="selectedCalendarDay !== null"
    :selected-calendar-day="selectedCalendarDay"
    :event-type="eventType"
  />
</template>

<script setup lang="ts">
import {ref} from "vue";
import {CalendarDay} from "v-calendar/dist/types/src/utils/page";
import {EventType} from "../../types/EventType";
import BookingAvailabilities from "./BookingAvailabilities.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

defineProps({
  eventType: {type: Object as () => EventType, required: true},
});

const currentDate = ref<Date | null>(null);
const calendarAttributes = ref<Array<Record<string, unknown>>>([]);
const selectedCalendarDay = ref<CalendarDay | null>(null);

async function dayClicked(day: CalendarDay): Promise<void> {
  selectedCalendarDay.value = day;
}
</script>
