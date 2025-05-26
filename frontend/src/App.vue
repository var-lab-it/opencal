<template>
  <div>
    <AppHeader
      v-if="user"
      :user="user"
    />
    <div class="container mt-3">
      <div class="row">
        <div class="col">
          <router-view />
        </div>
      </div>
      <div class="row mt-3 mb-3">
        <div class="col">
          <div class="text-center small text-muted">
            <a
              href="https://github.com/var-lab-it/opencal"
              target="_blank"
              class="text-dark"
            >OpenCal</a>
            &nbsp;
            <a
              href="https://github.com/sponsors/var-lab-it"
              target="_blank"
            >
              <font-awesome-icon
                icon="heart"
                class="text-danger"
              />
            </a>
            &nbsp;
            Open Source Scheduling
            <div class="mt-1 small">
              Created by
              <a
                href="https://var-lab.com"
                target="_blank"
                class="text-dark"
              >var-lab IT GmbH</a>
              and Contributors
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AppHeader from "./components/dashboard/AppHeader.vue";
import {getCurrentUser} from "./composables/CurrentUser.js";
import {isAuthenticated} from "./services/auth.js";

export default {
  name: 'App',
  components: {AppHeader},
  setup() {
    let user = null;

    if (isAuthenticated()) {
      user = getCurrentUser().user
    }

    return {user}
  }
}
</script>
