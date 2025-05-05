<template>
  <li class="nav-item dropdown">
    <a
      id="languageDropdown"
      class="nav-link dropdown-toggle"
      href="#"
      role="button"
      data-bs-toggle="dropdown"
      aria-expanded="false"
    >
      <font-awesome-icon icon="language" />
    </a>
    <ul
      class="dropdown-menu dropdown-menu-start"
      aria-labelledby="languageDropdown"
    >
      <li
        v-for="lang in availableLocales"
        :key="lang"
      >
        <a
          class="dropdown-item"
          href="#"
          @click.prevent="switchLanguage(lang)"
        >
          {{ lang.toUpperCase() }}
        </a>
      </li>
    </ul>
  </li>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const { locale, availableLocales } = useI18n()

function switchLanguage(lang: string) {
  locale.value = lang
}
</script>
