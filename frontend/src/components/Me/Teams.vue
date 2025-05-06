<template>
  <h2>
    {{ $t('teams.headline') }}
  </h2>

  <div
    v-if="loading"
    class="spinner-border text-dark"
    role="status"
  />

  <TeamRow
    v-for="team in user?.teams"
    v-else
    :key="team.id"
    :team="team"
  />
</template>

<script setup lang="ts">
import {getCurrentUser} from "../../composables/CurrentUser"
import {onMounted, ref} from "vue"
import TeamRow from "./TeamRow.vue";

const {user} = getCurrentUser()
const loading = ref(true)

onMounted(() => {
  const check = setInterval(() => {
    if (user.value !== null) {
      loading.value = false
      clearInterval(check)
    }
  }, 100)
  setTimeout(() => {
    loading.value = false
    clearInterval(check)
  }, 5000)
})
</script>
