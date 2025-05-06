<template>
  <h2 class="mb-3">
    {{ $t('account.headline') }}
  </h2>

  <div
    v-if="loading"
    class="spinner-border text-dark"
    role="status"
  />

  <div
    v-else
  >
    <div
      id="account-name"
      class="mb-2"
    >
      <strong>
        {{ $t('account.name') }}:
      </strong>
      {{ user?.givenName }} {{ user?.familyName }}
    </div>

    <div
      id="account-email"
      class="mb-2"
    >
      <strong>
        {{ $t('account.email') }}:
      </strong>
      {{ user?.email }}
    </div>

    <div
      id="account-password"
      class="mb-2"
    >
      <strong>
        {{ $t('account.password') }}:
      </strong>
      *****
    </div>

    <div
      id="account-teams"
      class="mb-2"
    >
      <strong>
        {{ $t('account.teams') }}:
      </strong>

      <router-link to="/teams">
        {{ user?.teams.length }}
      </router-link>
    </div>

    <button
      id="edit-button"
      class="btn btn-outline-secondary btn-sm mt-3"
      @click="openModal"
    >
      <font-awesome-icon icon="pencil" />
      {{ $t('account.buttons.edit') }}
    </button>
  </div>

  <div
    id="editModal"
    ref="modalRef"
    class="modal fade"
    tabindex="-1"
    aria-labelledby="editModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5
            id="editModalLabel"
            class="modal-title"
          >
            {{ $t('account.form.title') }}
          </h5>
          <button
            type="button"
            class="btn-close"
            @click="closeModal"
          />
        </div>
        <div class="modal-body">
          <form @submit.prevent="save">
            <div class="mb-3">
              <label class="form-label">
                {{ $t('account.form.given_name') }}
              </label>
              <input
                v-model="form.givenName"
                type="text"
                class="form-control given-name"
              >
            </div>
            <div class="mb-3">
              <label class="form-label">
                {{ $t('account.form.family_name') }}
              </label>
              <input
                v-model="form.familyName"
                type="text"
                class="form-control family-name"
              >
            </div>
            <div class="mb-3 border-bottom pb-3">
              <label class="form-label">
                {{ $t('account.form.email') }}
              </label>
              <input
                v-model="form.email"
                type="email"
                class="form-control email"
              >
            </div>
            <button
              id="submit-btn"
              type="submit"
              class="btn btn-primary"
            >
              {{ $t('account.form.buttons.save') }}
            </button>
            <button
              type="button"
              class="btn btn-outline-danger float-end"
              @click="closeModal"
            >
              {{ $t('account.form.buttons.cancel') }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {getCurrentUser} from "../../composables/CurrentUser"
import {onMounted, ref} from "vue"
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import apiClient from "../../services/api";
import * as bootstrap from 'bootstrap'
import {toast} from "vue3-toastify";
import 'vue3-toastify/dist/index.css';
import {useI18n} from 'vue-i18n'

const {t, locale, availableLocales} = useI18n()

let {user} = getCurrentUser()
const loading = ref(true)

const form = ref({
  givenName: "",
  familyName: "",
  email: ""
})

const modalRef = ref<HTMLDivElement | null>(null)

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

async function refresh() {
  loading.value = true
  try {
    const response = await apiClient.get(`/me`)
    if (response.data) {
      user.value = response.data
    }
  } catch (error) {
    console.error('Fehler beim Nachladen', error)
  } finally {
    loading.value = false
  }
}

function openModal() {
  if (user.value) {
    form.value.givenName = user.value.givenName || ""
    form.value.familyName = user.value.familyName || ""
    form.value.email = user.value.email || ""
  }
  const modal = new bootstrap.Modal(modalRef.value!)
  modal.show()
}

function closeModal() {
  const modal = bootstrap.Modal.getInstance(modalRef.value!)
  modal?.hide()
}

async function save() {
  try {
    await apiClient.patch(
        `/me/${user?.value.id}`,
        form.value,
        {
          headers: {
            'Content-Type': 'application/merge-patch+json'
          }
        }
    )
    toast(t('account.messages.saved_success'), {autoClose: 2000, type: 'success'})
    closeModal()

    refresh();
  } catch (error) {
    toast(t('account.messages.save_failed'), {autoClose: 2000, type: 'error'})
  }
}
</script>
